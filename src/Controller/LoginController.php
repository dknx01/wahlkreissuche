<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\LoginLink\LoginLinkHandler;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginController extends AbstractController
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/login/check', name: 'login_check_2')]
    public function check(Request $request, LoginLinkHandler $loginLinkHandler): RedirectResponse
    {
        try {
            /** @var User $user */
            $user = $loginLinkHandler->consumeLoginLink($request);
            if ($user->getActive() && $user->isVerified()) {
                return $this->redirectToRoute('search');
            }
            throw new UnsupportedUserException($this->translator->trans(id: 'user.not_active', domain: 'flash_error'));
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());

            return $this->redirectToRoute('search');
        }
    }

    #[Route('/login/link', name: 'login_link')]
    public function requestLoginLink(LoginLinkHandler $loginLinkHandler, UserRepository $userRepository, Request $request): Response
    {
        // check if login form is submitted
        if ($request->isMethod('POST')) {
            // load the user in some way (e.g. using the form input)
            $email = $request->request->get('email');
            $user = $userRepository->findOneByEmail($email);

            if (!$user instanceof User) {
                $this->addFlash('error', $this->translator->trans(id: 'user.not_found_email', domain: 'flash_error'));

                return $this->redirectToRoute('app_login');
            }

            // create a login link for $user this returns an instance
            // of LoginLinkDetails
            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);

            //            $emailTemplate = (new TemplatedEmail())
            //                ->from($this->fromAddress)
            //                ->to(new Address($user->getEmail()))
            //                ->subject($this->translator->trans('login_link.subject', [], 'mail'))
            //
            //                // path of the Twig template to render
            //                ->htmlTemplate('mails/login_link.html.twig')
            //
            //                // pass variables (name => value) to the template
            //                ->context([
            //                    'signedUrl' => $loginLinkDetails,
            //                ])
            //                ->textTemplate('mails/login_link.txt.twig')
            //            ;
            //            $mailer->send($emailTemplate);
            // render a "Login link is sent!" page
            return $this->render('login/login_link_sent.html.twig', ['link' => $loginLinkDetails]);
        }

        // if it's not submitted, render the "login" form
        return $this->render('login/index.html.twig', [
            'last_username' => '',
            'error' => null,
        ]);
    }
}
