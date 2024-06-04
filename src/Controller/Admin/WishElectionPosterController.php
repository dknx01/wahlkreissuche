<?php

namespace App\Controller\Admin;

use App\Entity\WishElectionPoster;
use App\Form\WishElectionPosterType;
use App\Repository\WishElectionPosterRepository;
use App\Security\Permissions;
use App\Service\Domain\WishElectionPosterLocationHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/wunsch/election_posters')]
class WishElectionPosterController extends AbstractController
{
    #[Route('/alle/{activeOnly}', name: 'admin_wish_election_posters_all', defaults: ['activeOnly' => ''])]
    public function all(string $activeOnly, WishElectionPosterRepository $repo): Response
    {
        $this->denyAccessUnlessGranted(Permissions::USER_FULLY_ACTIVE);

        return $this->render(
            'plakatorte/wishes/overview_all.htm.twig',
            [
                'posters' => $repo->findAllForOverview($activeOnly),
                'activeOnly' => $activeOnly === 'active',
            ]
        );
    }

    #[Route('/edit/{id}', name: 'admin_wish_election_posters_edit')]
    public function editPlakat(Request $request, WishElectionPoster $wishElectionPoster, WishElectionPosterLocationHandler $plakatOrteHandler): Response
    {
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
            $plakatOrteHandler->saveEntity($wishElectionPoster);

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

    #[Route('/minimap/{id}', name: 'minimap_wish_poster-location')]
    public function minimap(WishElectionPoster $wishElectionPoster): Response
    {
        return $this->render(
            'plakatorte/wishes/minimap.htm.twig',
            [
                'places' => [
                    $wishElectionPoster->getAddress()->getDistrict() => [$wishElectionPoster],
                ],
                'state' => $wishElectionPoster->getAddress()->getState(),
                'mapData' => [
                    'zoom' => 16,
                    'lat' => $wishElectionPoster->getAddress()->getLatitude(),
                    'lon' => $wishElectionPoster->getAddress()->getLongitude(),
                ],
            ]
        );
    }
}
