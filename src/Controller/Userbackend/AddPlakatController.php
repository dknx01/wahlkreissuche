<?php

namespace App\Controller\Userbackend;

use App\Entity\ElectionPoster;
use App\Form\ElectionPosterManualType;
use App\Form\ElectionPosterType;
use App\Repository\ElectionPosterRepository;
use App\Security\Permissions;
use App\Service\Domain\ImageUploader;
use App\Service\Domain\ManualLocationHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/plakat')]
class AddPlakatController extends AbstractController
{
    public function __construct(
        private readonly ImageUploader $imageUploader,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route('/hinzufuegen', name: 'add_plakat2')]
    public function add2(
        Request $request,
        ManualLocationHandler $locationHandler,
        ElectionPosterRepository $electionPosterRepo,
    ): Response {
        $this->denyAccessUnlessGranted(Permissions::USER_FULLY_ACTIVE);

        $errors = null;

        $electionPoster = new ElectionPoster(
            $this->getUser()?->getUserIdentifier(),
            new \DateTimeImmutable(),
            ElectionPoster\Address::createEmpty()
        );
        $electionPoster->setActive(true);

        if ($request->getMethod() === Request::METHOD_POST) {
            $formFields = $request->get('election_poster');
            $locationHandler->setCity($formFields['city']);
            $locationHandler->setDistrict(array_key_exists('district', $formFields) ? $formFields['district'] : '');
        }
        $form = $this->createForm(ElectionPosterType::class, $electionPoster);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                /** @var UploadedFile|null $imagePath */
                $imagePath = $form->get('thumbnailFilename')->getData();
                if ($imagePath instanceof UploadedFile) {
                    $newFilename = $this->imageUploader->uploadImagesAsThumbnail($imagePath);
                    $electionPoster->setThumbnailFilename($newFilename);
                }
                $electionPosterRepo->save($electionPoster);
                $this->addFlash('success', $this->translator->trans(id: 'success', domain: 'flash_success'));

                return $this->redirectToRoute('');
            }

            $errors = $form->getErrors();
        }

        $thumbnailData = null;

        if ($electionPoster->getThumbnailFilename() !== null) {
            $thumbnailData = $this->imageUploader
                ->thumbnailAsBase64Data($electionPoster->getThumbnailFilename());
        }

        return $this->render(
            'plakatorte/add2.html.twig',
            [
                'form' => $form,
                'errors' => $errors,
                'thumbnail' => $thumbnailData,
            ]
        );
    }

    #[Route('/hinzufuegen/manuell', name: 'add_plakat_manual')]
    public function addManual(Request $request, ElectionPosterRepository $electionPosterRepo, ManualLocationHandler $locationHandler): Response
    {
        $this->denyAccessUnlessGranted(Permissions::USER_FULLY_ACTIVE);
        $errors = null;

        $electionPoster = new ElectionPoster(
            $this->getUser()?->getUserIdentifier(),
            new \DateTimeImmutable(),
            ElectionPoster\Address::createEmpty()
        );

        if ($request->getMethod() === Request::METHOD_POST) {
            $formFields = $request->get('election_poster_manual');
            $locationHandler->setCity($formFields['city']);
            $locationHandler->setDistrict(array_key_exists('district', $formFields) ? $formFields['district'] : '');
        }
        $form = $this->createForm(ElectionPosterManualType::class, $electionPoster);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                /** @var UploadedFile|null $imagePath */
                $imagePath = $form->get('thumbnailFilename')->getData();
                if ($imagePath instanceof UploadedFile) {
                    $newFilename = $this->imageUploader->uploadImagesAsThumbnail($imagePath);
                    $electionPoster->setThumbnailFilename($newFilename);
                }
                $electionPosterRepo->save($electionPoster);

                $this->addFlash('success', $this->translator->trans(id: 'success', domain: 'flash_success'));
            } else {
                $errors = $form->getErrors();
            }
        }

        return $this->render(
            'plakatorte/add_manual.html.twig',
            [
                'form' => $form,
                'errors' => $errors,
                'thumbnail' => $this->imageUploader->thumbnailAsBase64Data($electionPoster->getThumbnailFilename()),
            ]
        );
    }
}
