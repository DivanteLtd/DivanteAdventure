<?php
/**
 * @package  Divante\Bundle
 * @author BH <bh@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Auth\UserPersistor;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AuthController
 *
 * @Route("api/auth")
 */
class AuthController extends FOSRestController
{
    private UserPersistor $userPersistor;

    public function __construct(UserPersistor $userPersistor)
    {
        $this->userPersistor = $userPersistor;
    }

    /**
     * @Route("/persist/{id}", name="auth_code_validation")
     * @Method("POST")
     * @Security("is_granted('EMPLOYEE_SELF', employee)")
     *
     * @param Request $request
     * @param Employee $employee
     * @return View
     */
    public function persistUserAction(Request $request, Employee $employee) : View
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data) || !isset($data['exp'], $data['code'])) {
            return $this->view(
                null,
                Response::HTTP_BAD_REQUEST
            );
        }

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository(User::class);
        /** @var User|null $user */
        $user = $userRepo->findOneBy(['employeeId' => $employee->getId()]);

        if (null !== $user) {
            if (!$this->isCodeValid($employee, $data)) {
                $user = $this->userPersistor->increaseErrorCounter($user);

                if ($user->getLoginErrors() >= User::MAX_ERROR_COUNT) {
                    $this->userPersistor->lockUser($user);
                }

                return $this->view(
                    [
                        'errors'   => $user->getLoginErrors(),
                        'message'  => 'Invalid code',
                        'isLocked' => $user->getIsLocked(),
                    ],
                    Response::HTTP_OK
                );
            }

            $this->userPersistor->persist($user, $data['exp']);
        }

        return $this->view(
            ['success' => true],
            Response::HTTP_OK
        );
    }

    /**
     * @param Employee $employee
     * @param array<string,mixed> $data
     * @return bool
     */
    private function isCodeValid(Employee $employee, $data): bool
    {
        return (substr($employee->getPrivatePhone(), -3) === $data['code']);
    }

    /**
     * @Route("/unlock/{id}", name="auth_unlock")
     * @Method("POST")
     * @Security("is_granted('EMPLOYEE_SELF', employee)")
     *
     * @param Request $request
     * @return View
     */
    public function unlockUserAction(Request $request) : View
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data) || !isset($data['exp'], $data['lock'])) {
            return $this->view(
                [],
                Response::HTTP_BAD_REQUEST
            );
        }

        $user = $this->getUser();

        if (null !== $user) {
            if (!$this->isLockValid($user, $data)) {
                return $this->view(
                    [
                        'message'  => 'Invalid code',
                    ],
                    Response::HTTP_OK
                );
            }

            $this->userPersistor->persist($user, $data['exp']);
        }

        return $this->view(
            ['success' => true],
            Response::HTTP_OK
        );
    }

    /**
     * @param User $user
     * @param array<string,mixed> $data
     * @return bool
     */
    private function isLockValid(User $user, array $data): bool
    {
        return $user->getLocked() === $data['lock'];
    }

    /**
     * @Route("/login/{id}", name="auth_user_persist")
     * @Method("POST")
     * @Security("is_granted('EMPLOYEE_SELF', employee)")
     *
     * @param Request $request
     * @return View
     */
    public function loginUserAction(Request $request) : View
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data) || empty($data['exp'])) {
            return $this->view(
                null,
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->view(
            [],
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/logout/{id}", name="auth_user_logout")
     * @Security("is_granted('EMPLOYEE_SELF', employee)")
     * @Method("POST")
     *
     * @return View
     * @param Employee $employee
     */
    public function logoutUserAction(Employee $employee) : View
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository(User::class);

        /** @var User|null $user */
        $user = $userRepo->findOneBy(['employeeId' => $employee->getId()]);

        if (null !== $user) {
            $user->setLoginExpiration(null);
            $em->merge($user);
            $em->flush();
        }

        return $this->view(
            [],
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/confirmed/{id}", name="auth_user_is_confirmed")
     * @Method("GET")
     * @Security("is_granted('EMPLOYEE_SELF', employee)")
     *
     * @param Employee $employee
     * @return View
     * @throws \Exception
     */
    public function isConfirmed(Employee $employee) : View
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository(User::class);

        /** @var User|null $user */
        $user = $userRepo->findOneBy(['employeeId' => $employee->getId()]);

        $nowDateTime = new \DateTime();

        return $this->view(
            ['success' => (null !== $user && $nowDateTime <= $user->getLoginExpiration())],
            Response::HTTP_OK
        );
    }
}
