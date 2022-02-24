<?php

namespace Divante\Bundle\AdventureBundle\Command;

use Divante\Bundle\AdventureBundle\Entity\Data\ContractOwner;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Hardware\HardwareAgreement;
use Divante\Bundle\AdventureBundle\Entity\Hardware\HardwareAssignment;
use Divante\Bundle\AdventureBundle\Entity\Notification;
use Divante\Bundle\AdventureBundle\Entity\PotentialEmployee;
use Divante\Bundle\AdventureBundle\Entity\Repository\EmployeeRepository;
use Divante\Bundle\AdventureBundle\Entity\Repository\Hardware\HardwareAssignmentRepository;
use Divante\Bundle\AdventureBundle\Entity\Repository\PotentialEmployeeRepository;
use Divante\Bundle\AdventureBundle\Events\Hardware\HardwareAssignedEvent;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\SystemConfig;
use Divante\Bundle\AdventureBundle\Message\Dashboard\CreateNotification;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\EmailConfig;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Psr\Http\Message\StreamInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SnipeSyncCommand extends ContainerAwareCommand
{
    private EntityManagerInterface $em;
    private string $snipeUrl;
    private string $accessToken;
    private EventDispatcherInterface $dispatcher;
    private string $admResponsibleEmail;
    private MessageBusInterface $messageBus;

    use ResponseConverterTrait;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $dispatcher,
        EmailConfig $emailConfig,
        MessageBusInterface $messageBus,
        SystemConfig $config
    ) {
        parent::__construct(null);
        $this->em = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->messageBus = $messageBus;
        $this->snipeUrl = $config->getValueOrDefault(SystemConfig::KEY_SNIPE_IT_INSTANCE_URL, '');
        $this->accessToken = $config->getValueOrDefault(SystemConfig::KEY_SNIPE_IT_TOKEN, '');
        $this->admResponsibleEmail = $emailConfig->getAdmResponsibleEmail();
    }

    protected function configure() : void
    {
        $this
            ->setName('adventure:snipe:sync')
            ->setDescription('Update Snipe users ID in our employees entity');
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $snipeUsers = $this->getSnipe('users');
        /** @var EmployeeRepository $employeesRepo */
        $employeesRepo = $this->em->getRepository(Employee::class);
        $minute = round(time() / 60);
        $usersCount = ceil(count($snipeUsers) / 100);
        $offset = ($minute % $usersCount) * 100;
        $snipeUsersDownload = array_slice($snipeUsers, $offset, 100);
        foreach ($snipeUsersDownload as $snipeUser) {
            $output->writeln($snipeUser['id'] . ' => ' . $snipeUser['username']);
            if ($snipeUser['assets_count'] > 0) {
                try {
                    $snipeAssignments = $this->getSnipe('users/' . $snipeUser['id'] . '/assets');
                } catch (\Exception $e) {
                    $output->writeln("Błąd dla ID " . $snipeUser['id'].":".$e->getMessage());
                    continue;
                }
                $snipeUserEmail = $snipeUser['email'];
                /** @var Employee|null $employee */
                $employee = $employeesRepo->findOneByEmailAddressUsername($snipeUserEmail);
                if (!is_null($employee) && is_null($employee->getContractId())) {
                    continue;
                }
                $potentialEmployee = null;
                if (!$employee) {
                    /** @var PotentialEmployeeRepository $potentialEmployeesRepo */
                    $potentialEmployeesRepo = $this->em->getRepository(PotentialEmployee::class);
                    /** @var PotentialEmployee|null $potentialEmployee */
                    $potentialEmployee = $potentialEmployeesRepo->findOneByEmailAddressUsername($snipeUserEmail);
                }
                if (!is_null($employee) || !is_null($potentialEmployee)) {
                    /** @var ContractOwner $user */
                    $user = is_null($employee) ? $potentialEmployee : $employee;
                    $hardwareRepo = $this->em->getRepository(HardwareAssignment::class);
                    $this->deleteUnassignedAssets($user, $snipeAssignments);

                    foreach ($snipeAssignments as $snipeAssignment) {
                        $dataForQuery = [
                            'assetId' => $snipeAssignment['id']
                        ];

                        if (!is_null($employee)) {
                            $dataForQuery['employee'] = $employee;
                        }

                        if (!is_null($potentialEmployee)) {
                            $dataForQuery['potentialEmployee'] = $potentialEmployee;
                        }
                        /** @var HardwareAssignment|null $hardwareAssignment */
                        $hardwareAssignment = $hardwareRepo->findOneBy($dataForQuery);
                        if (is_null($hardwareAssignment)) {
                            $hardwareAssignment = new HardwareAssignment();
                            $hardwareAssignment->setAssetId($snipeAssignment['id']);
                            try {
                                if ($hardwareAssignment->isSendEmail() === false) {
                                    $hardwareAgreement = new HardwareAgreement();
                                    $hardwareAgreement
                                        ->setCreatedAt()
                                        ->setUpdatedAt();
                                    $hardwareAgreement->setAssignment($hardwareAssignment);
                                    $this->em->persist($hardwareAgreement);
                                    $this->sendEmailToAdministration(
                                        $user,
                                        $snipeAssignment['model']['name'],
                                        $snipeAssignment['manufacturer']['name'],
                                        $snipeAssignment['serial'],
                                        $snipeAssignment['updated_at']['datetime']
                                    );
                                    $hardwareAssignment->setSendEmail(true);
                                    $this->createNotification($user);
                                } else {
                                    $hardwareAssignment->setSendEmail(true);
                                }
                            } catch (\Exception $exception) {
                                $hardwareAssignment->setSendEmail(false);
                                throw  $exception;
                            }
                            $this->em->persist($hardwareAssignment);
                        }
                        $hardwareAssignment
                            ->setPotentialEmployee($potentialEmployee)
                            ->setEmployee($employee)
                            ->setCategory($snipeAssignment['category']['name'])
                            ->setManufacturer($snipeAssignment['manufacturer']['name'])
                            ->setModel($snipeAssignment['model']['name'])
                            ->setAssetId($snipeAssignment['id'])
                            ->setSerialNumber($snipeAssignment['serial']);
                        $this->em->persist($hardwareAssignment);
                    }
                }
            }
        }
        $this->em->flush();
        return 0;
    }

    /**
     * @param string $path
     * @return array<int|string,mixed>
     * @throws \Exception
     */
    private function getSnipe(string $path) : array
    {
        $client = new Client();
        try {
            $request = $client->request('GET', $this->snipeUrl . $path, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ],
            ]);
            $json = $this->streamInterfaceToString($request->getBody());
            $jsonArray = json_decode($json, true);
        } catch (GuzzleException $e) {
            throw new \Exception("GuzzleException: " . $e->getMessage(), 0, $e);
        }
        return $jsonArray['rows'];
    }

    private function createNotification($user)
    {
        /** @var Employee $admResponsible */
        $admResponsible = $this->em->getRepository(Employee::class)
            ->findBy(['email' => $this->admResponsibleEmail])[0];
        /** @var ContractOwner $user */
        $subject = sprintf('%s %s', $user->getName(), $user->getLastname());
        $createEntry = new CreateNotification(
            $admResponsible->getId(),
            Notification::HARDWARE_TO_GENERATE,
            $subject,
            '/hardware'
        );
        $this->messageBus->dispatch($createEntry);
    }

    private function sendEmailToAdministration(
        ContractOwner $user,
        string $model,
        string $manufacturer,
        string $serialNumber,
        string $updatedAt
    ) : void {
        $firstName = $user->getName();
        $lastName = $user->getLastName();
        $this->dispatcher->dispatch(
            new HardwareAssignedEvent($firstName, $lastName, $manufacturer, $model, $serialNumber, $updatedAt)
        );
    }

    private function deleteUnassignedAssets(ContractOwner $user, array $userAsserts) :void
    {
        $ids = array_map(fn(array $item) :int => $item['id'], $userAsserts);
        /** @var HardwareAssignmentRepository $hardwareRepo */
        $hardwareRepo = $this->em->getRepository(HardwareAssignment::class);
        $hardwareRepo->deleteUnassignedAssets($user, $ids);
    }
}
