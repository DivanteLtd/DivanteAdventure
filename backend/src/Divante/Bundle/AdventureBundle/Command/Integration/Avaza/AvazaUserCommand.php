<?php

namespace Divante\Bundle\AdventureBundle\Command\Integration\Avaza;

use Divante\Bundle\AdventureBundle\Command\ResponseConverterTrait;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Repository\EmployeeRepository;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\SystemConfig;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AvazaUserCommand extends Command
{
    /** @var string */
    private const ENTRYPOINT_URL = 'https://api.avaza.com/api/UserProfile';
    private const ALLOWED_ROLES = [ "Admin", "TimesheetUser" ];

    use ResponseConverterTrait;

    private string $token;
    private EntityManagerInterface $em;

    public function __construct(SystemConfig $config, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->token = $config->getValueOrDefault(SystemConfig::KEY_AVAZA_TOKEN, '');
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this->setName('adventure:avaza:users')
            ->setDescription("Synchronizes users' Avaza ID with instance");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (empty($this->token)) {
            $output->writeln("Token is not supplied; stopping");
            return 1;
        }
        $client = new Client();
        try {
            $response = $client->get(self::ENTRYPOINT_URL, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer $this->token",
                ],
            ]);
            $body = $response->getBody();
            $json = $this->streamInterfaceToString($body);
            $this->readJson(json_decode($json, true));
        } catch (GuzzleException $e) {
            $output->writeln("Guzzle exception: {$e->getMessage()}");
            return 2;
        }
        return 0;
    }

    private function readJson(array $json): void
    {
        $users = $json['Users'] ?? [];
        foreach ($users as $user) {
            /** @var array<int,array<string,string>> $roles */
            $roles = $user['Roles'];
            /** @var string|null $id */
            $id = $user['UserID'] ?? null;
            /** @var string $email */
            $email = $user['Email'] ?? '';
            /** @var boolean $teamMember */
            $teamMember = $user['isTeamMember'] ?? false;
            if ($teamMember && !is_null($id) && !empty($email) && $this->canUseTimesheets($roles)) {
                $this->updateId($email, $id);
            }
        }
        $this->em->flush();
    }

    /**
     * @param array<int,array<string,string>> $roles
     * @return bool
     */
    private function canUseTimesheets(array $roles): bool
    {
        /** @var bool[] $rolesAllowed */
        $rolesAllowed = array_map(
            function (array $entry): bool {
                return in_array($entry['RoleCode'], self::ALLOWED_ROLES);
            },
            $roles,
        );
        /** @var bool $result */
        $result = array_reduce($rolesAllowed, fn(bool $a, bool $b) => $a || $b, false);
        return $result;
    }

    private function updateId(string $email, string $id): void
    {
        /** @var EmployeeRepository $repo */
        $repo = $this->em->getRepository(Employee::class);
        $employee = $repo->findOneByEmailAddressUsername($email);
        if (!is_null($employee)) {
            $employee->setAvazaId($id);
        }
    }
}
