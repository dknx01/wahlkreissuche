<?php

namespace App\Controller\Userbackend;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Repository\UserRepository;
use App\Security\Permissions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user', name: 'user')]
class UserController extends AbstractController
{
    #[Route('password', name: 'password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepo): Response
    {
        $this->denyAccessUnlessGranted(Permissions::USER_FULLY_ACTIVE);
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        $errors = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            /** @var User $user */
            $user = $this->getUser();
            if (!$passwordHasher->isPasswordValid($user, $data['oldPassword'])) {
                $errors[] = 'wrongOldPassword';
            }
            if ($data['newPassword1'] !== $data['newPassword2']) {
                $errors[] = 'notSamePasswords';
            } else {
                $user->setPassword($passwordHasher->hashPassword($user, $data['newPassword1']));
                $userRepo->save($user);
                $this->addFlash('success', 'success');
            }
        }

        return $this->render(
            'user/change_password.html.twig',
            [
                'form' => $form->createView(),
                'errors' => $errors,
            ]
        );
    }
}
