<?php

namespace Tests\Entrypoints;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelBrowser;
use Divante\Bundle\AdventureBundle\Annotation\Exporter;

abstract class AbstractEntrypointTest extends WebTestCase
{
    protected ?EntityManagerInterface $em = null;
    private ?JWTEncoderInterface $jwtEncoder = null;
    
    protected function setUp(): void
    {
        parent::setUp();
        self::ensureKernelShutdown();
        self::bootKernel();
        $this->em = self::$container->get('doctrine')->getManager();
        $this->jwtEncoder = self::$container->get('lexik_jwt_authentication.encoder');
    }

    protected function tearDown() : void
    {
        $this->jwtEncoder = null;
        $this->em->close();
        $this->em = null;
        self::$kernel->shutdown();
        parent::tearDown();
    }

    protected function getToken(User $user) : string
    {
        return $this->jwtEncoder->encode([
            "iat" => 1,
            "exp" => PHP_INT_MAX,
            "roles" => $user->getRoles(),
            "username" => $user->getEmail(),
            "ip" => "127.0.0.1",
            "employeeId" => $user->getEmployee()->getId(),
            "pin" => $user->getEmployee()->getHashedPin(),
        ]);
    }

    /**
     * @param string[] $roles
     * @return User
     */
    public function generateFosUser(array $roles = []) : User
    {
        $user = new User();
        $email = "Random".rand(0, 99999)."Email".rand(0, 99999)."@divante.com";
        $user->setGoogleId("RandomGoogleId".rand(0, 99999))
            ->setLocked(null)
            ->setLoginExpiration(new DateTime())
            ->setLoginErrors(0)
            ->setLocked(null)
            ->setEnabled(true)
            ->setUsername($email)
            ->setEmail($email)
            ->setPassword("RandomPassword".rand(0, 99999))
            ->setRoles($roles);

        $this->em->persist($user);
        return $user;
    }

    public function generateTribe() : Tribe
    {
        $tribe = new Tribe();
        $tribe->setName("RandomTribeName".rand(0, 10000))
            ->setCreatedAt()
            ->setUpdatedAt();
        $this->em->persist($tribe);
        return $tribe;
    }

    public function generateEmployee(User $user, ?Tribe $tribe = null, ?int $contractId = null) : Employee
    {
        $employee = new Employee();
        $employee->setName("RandomName".rand(0, 10000))
            ->setLastName("RandomLastName".rand(0, 10000))
            ->setEmail($user->getEmail())
            ->setTribe($tribe)
            ->setPhoto("RandomPhoto".rand(0, 10000).'.jpg')
            ->setPhone("RandomPhone".rand(0, 10000))
            ->setPrivatePhone("RandomPrivatePhone".rand(0, 10000))
            ->setCar("RandomCar".rand(0, 10000))
            ->setContractId($contractId)
            ->setCity("RandomCity".rand(0, 10000))
            ->setWorkMode(rand(1, 2))
            ->setHiredAt(new DateTime())
            ->setHiredTo(null)
            ->setGender(rand(0, 1))
            ->setEmergencyFirstName("RandomName".rand(0, 10000))
            ->setEmergencyLastName("RandomLastName".rand(0, 10000))
            ->setEmergencyAddress("RandomAddress".rand(0, 10000))
            ->setEmergencyPhone("RandomPhone".rand(0, 10000))
            ->setAndHashPin("RandomHashed".rand(0, 10000))
            ->setDateOfBirth(new DateTime())
            ->setCreatedAt()
            ->setUpdatedAt();
        $user->setEmployee($employee);
        $this->em->persist($employee);
        return $employee;
    }

    private static ?HttpKernelBrowser $browser = null;

    public function request(
        string $method,
        string $url,
        ?User $user = null,
        array $parameters = [],
        array $files = []
    ) : Response {
        if (is_null(self::$browser)) {
            self::$browser = self::createClient();
        }
        $headers = [];
        if (!is_null($user)) {
            $headers['HTTP_Authorization'] = 'Bearer '.$this->getToken($user);
        }
        self::$browser->request($method, $url, $parameters, $files, $headers);
        return self::$browser->getResponse();
    }
}
