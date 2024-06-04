<?php

namespace App\Controller\Userbackend;

use App\Entity\WishElectionPoster;
use App\Form\WishElectionPosterType;
use App\Security\Permissions;
use App\Service\Domain\WishElectionPosterLocationHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('user/plakate-wunsch')]
class WishElectionPosterAdminController extends AbstractController
{
    #[Route('/edit/{id}', name: 'wish_plakat_admin_edit')]
    public function editWishElectionPoster(
        Request $request,
        WishElectionPoster $wishElectionPoster,
        WishElectionPosterLocationHandler $handler
    ): Response {
        $this->denyAccessUnlessGranted(Permissions::USER_FULLY_ACTIVE);
        $form = $this->createForm(WishElectionPosterType::class, $wishElectionPoster);
        $form->handleRequest($request);

        if ($form->isSubmitted()
            && $this->isCsrfTokenValid('wish_election_poster_edit', $request->request->get('wish_election_poster')['token']) /* @phpstan-ignore-line */
            && $form->getErrors()->count() === 0
        ) {
            $wishElectionPoster->setCreatedAt(
                \DateTimeImmutable::createFromFormat('m.d.Y H:i', $request->request->get('wish_election_poster')['createdAt']) /* @phpstan-ignore-line */
            );
            $wishElectionPoster->setActive(array_key_exists('active', $request->request->get('wish_election_poster'))); /* @phpstan-ignore-line */
            $handler->saveEntity($wishElectionPoster);

            $this->addFlash('success', 'success');
        }

        return $this->render(
            'plakatorte/wishes/edit.htm.twig',
            [
                'plakat' => $wishElectionPoster,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route('/meine/{activeOnly}', name: 'wish_plakate_my', defaults: ['activeOnly' => ''])]
    public function myPostersMenu(string $activeOnly, WishElectionPosterLocationHandler $handler): Response
    {
        return $this->render(
            'plakatorte/wishes/overview_my.htm.twig',
            [
                'activeOnly' => $activeOnly,
                'posters' => $handler->findAllWishPlacesByUser($this->getUser()?->getUserIdentifier(), $activeOnly),
            ]
        );
    }
}
