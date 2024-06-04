<?php

namespace App\Controller\Userbackend;

use App\Entity\ElectionPoster\Address;
use App\Entity\WishElectionPoster;
use App\Form\WishElectionPosterManualType;
use App\Form\WishElectionPosterType;
use App\Security\Permissions;
use App\Service\Domain\ManualLocationHandler;
use App\Service\Domain\WishElectionPosterLocationHandler;
use App\Service\Domain\WishManualLocationHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('plakatwuensche')]
class AddWishPlakatController extends AbstractController
{
    #[Route('/hinzufuegen', name: 'add_plakat_wish')]
    public function add(Request $request, ManualLocationHandler $locationHandler, WishElectionPosterLocationHandler $handler): Response
    {
        $this->denyAccessUnlessGranted(Permissions::USER_FULLY_ACTIVE);

        $wishElectionPoster = new WishElectionPoster(
            $this->getUser()?->getUserIdentifier(),
            new \DateTimeImmutable(),
            Address::createEmpty()
        );
        $wishElectionPoster->setActive(true);
        if ($request->getMethod() === Request::METHOD_POST) {
            $locationHandler->setCity($request->get('wish_election_poster')['city']);
            $locationHandler->setDistrict($request->get('wish_election_poster')['district']);
        }
        $form = $this->createForm(WishElectionPosterType::class, $wishElectionPoster);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $handler->saveEntity($wishElectionPoster);
            }
        }

        return $this->renderForm(
            'plakatorte/wishes/add.html.twig',
            [
                'form' => $form,
                'errors' => null,
            ]
        );
    }

    #[Route('/hinzufuegen/manuell', name: 'add_plakat_wish_manual')]
    public function addManual(Request $request, WishElectionPosterLocationHandler $handler, WishManualLocationHandler $wishManualLocationHandler): Response
    {
        $this->denyAccessUnlessGranted(Permissions::USER_FULLY_ACTIVE);

        $wishElectionPoster = new WishElectionPoster(
            $this->getUser()?->getUserIdentifier(),
            new \DateTimeImmutable(),
            Address::createEmpty()
        );

        if ($request->getMethod() === Request::METHOD_POST) {
            $wishManualLocationHandler->setCity($request->get('wish_election_poster_manual')['city']);
            $wishManualLocationHandler->setDistrict($request->get('wish_election_poster_manual')['district']);
        }
        $form = $this->createForm(WishElectionPosterManualType::class, $wishElectionPoster);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $handler->saveEntity($wishElectionPoster);
            }
        }

        return $this->renderForm(
            'plakatorte/wishes/add_manual.html.twig',
            [
                'form' => $form,
                'errors' => null,
            ]
        );
    }
}
