<?php

namespace Divante\Bundle\AdventureBundle\Command;

use Divante\Bundle\AdventureBundle\Entity\Company;
use Divante\Bundle\AdventureBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RoleChecker extends ContainerAwareCommand
{
    /** @inheritdoc */
    protected function configure() : void
    {
        $this
            ->setName('adventure:role')
            ->setDescription('Manipulates roles for given user')
            ->setHelp(<<<HELP
                If 'role' argument is supplied and correct and user with given username exists, set user's role to that
                role. If 'role' argument is not supplied and user with given username exists, displays table with user's
                roles. In any other case displays error message.
                HELP
            )
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                "Username, i.e. 'jsmith' if user's email is 'jsmith@example.com'"
            )
            ->addArgument(
                'role',
                InputArgument::OPTIONAL,
                "User's new role: ".implode(', ', array_keys(User::ROLES_LEVELS))
            );
    }

    /** @inheritdoc */
    public function run(InputInterface $input, OutputInterface $output) : int
    {
        $username = $input->getArgument('username');
        if (empty($username)) {
            $output->writeln('username argument is required');
            return 1;
        }

        $user = $this->getUser($username);
        if (is_null($user)) {
            $output->writeln("User with login $username has not been found");
            return 1;
        }

        $newRole = $input->getArgument('role');
        if (empty($newRole)) {
            $this->displayRoles($output, $user);
        } else {
            $this->setRole($output, $user, $newRole);
        }
        return 0;
    }

    private function getUser(string $username) : ?User
    {
        $userRepo = $this->getEntityManager()->getRepository(User::class);
        $companyRepo = $this->getEntityManager()->getRepository(Company::class);
        $companyDomain = $companyRepo->find(1)->getEmailDomain();
        /** @var User|null $user */
        $user = $userRepo->findOneBy([
            'username' => $username.$companyDomain
        ]);
        return $user;
    }

    private function setRole(OutputInterface $output, User $user, string $role) : void
    {
        if (!in_array($role, User::ROLES_LEVELS)) {
            $output->writeln("Role $role is not a correct role.");
            $output->writeln("Correct roles: ".implode(', ', User::ROLES_LEVELS));
        } else {
            $user->setRoles([$role]);
            $this->getEntityManager()->flush();
        }
    }

    private function getEntityManager() : EntityManagerInterface
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        return $em;
    }

    private function displayRoles(OutputInterface $output, User $user) : void
    {
        $roles = array_map(
            function (string $entry) {
                return [$entry];
            },
            $user->getRoles()
        );

        (new Table($output))
            ->setHeaders(['Roles'])
            ->addRows($roles)
            ->render();
    }
}
