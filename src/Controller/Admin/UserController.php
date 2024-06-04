<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\Permissions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/users')]
class UserController extends AbstractController
{
    #[Route('', name: 'users_overview')]
    public function overview(UserRepository $userRepo): Response
    {
        $this->denyAccessUnlessGranted(Permissions::USER_ADMIN);

        return $this->render(
            'admin/user/overview.html.twig',
            [
                'users' => $userRepo->findAll(),
            ]
        );
    }

    #[Route('/{id}', name: 'user_edit')]
    public function edit(User $user, Request $request, UserRepository $userRepo): Response
    {
        $this->denyAccessUnlessGranted(Permissions::USER_ADMIN);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $errors = null;
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $roles = [];
                /* @phpstan-ignore-next-line */
                foreach ($request->request->get('user')['roles'] as $value) {
                    $roles[] = $value;
                }
                $user->setRoles($roles);
                $userRepo->save($user);

                $this->addFlash('success', 'success');
            } else {
                $errors = $form->getErrors();
            }
        }

        return $this->render(
            'admin/user/edit.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
                'errors' => $errors,
            ]
        );
    }
}
