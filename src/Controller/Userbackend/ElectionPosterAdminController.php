<?php

namespace App\Controller\Userbackend;

use App\Form\ElectionPosterType;
use App\Repository\ElectionPosterRepository;
use App\Security\Permissions;
use App\Service\Domain\ElectionPosterHandler;
use App\Service\Domain\ImageUploader;
use App\Service\Domain\ManualLocationHandler;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('user/plakate')]
class ElectionPosterAdminController extends AbstractController
{
    public function __construct(
        private readonly string $uploadFolder,
        private readonly LoggerInterface $logger,
        private readonly string $varFolder
    ) {
    }

    #[Route('/edit/{id}', name: 'plakat_admin_edit')]
    public function editPlakat(
        string $id,
        Request $request,
        ElectionPosterRepository $electionPosterRepo,
        ElectionPosterHandler $plakatOrteHandler,
        ImageUploader $imageUploader,
        ManualLocationHandler $locationHandler,
    ): Response {
        $this->denyAccessUnlessGranted(Permissions::USER_FULLY_ACTIVE);

        $electionPoster = $electionPosterRepo->find($id);
        if (!$electionPoster) {
            $this->addFlash('error', 'Poster not found.');
            $this->redirectToRoute('search');
        }
        $thumbnailFileNameString = null;
        if ($electionPoster->getThumbnailFilename() !== null) {
            $thumbnailFileNameString = $electionPoster->getThumbnailFilename();
            $electionPoster->setThumbnailFilename(
                new File($this->uploadFolder . $electionPoster->getThumbnailFilename())
            );
        }
        if ($request->getMethod() === Request::METHOD_POST) {
            $formFields = $request->get('election_poster');
            $locationHandler->setDistrict(array_key_exists('district', $formFields) ? $formFields['district'] : '');
            $locationHandler->setCity(array_key_exists('city', $formFields) ? $formFields['city'] : '');
        }
        $form = $this->createForm(ElectionPosterType::class, $electionPoster);
        $form->handleRequest($request);

        if ($form->isSubmitted()
            && $this->isCsrfTokenValid('election_poster_edit', $request->request->get('election_poster')['token']) /* @phpstan-ignore-line */
            && $form->getErrors()->count() === 0
        ) {
            $electionPoster->setCreatedAt(
                \DateTimeImmutable::createFromFormat('m.d.Y H:i', $request->request->get('election_poster')['createdAt']) /* @phpstan-ignore-line */
            );
            $electionPoster->setActive(array_key_exists('active', $request->request->get('election_poster'))); /* @phpstan-ignore-line */
            /** @var UploadedFile|null $imagePath */
            $imagePath = $form->get('thumbnailFilename')->getData();
            if ($imagePath instanceof UploadedFile) {
                $newFilename = $imageUploader->uploadImagesAsThumbnail(
                    $imagePath,
                    $thumbnailFileNameString
                );
                $electionPoster->setThumbnailFilename($newFilename);
            } else {
                $electionPoster->setThumbnailFilename($thumbnailFileNameString);
            }
            $plakatOrteHandler->saveEntity($electionPoster);

            $this->addFlash('success', 'success');
        }

        return $this->render(
            'plakatorte/edit.htm.twig',
            [
                'plakat' => $electionPoster,
                'form' => $form->createView(),
                'thumbnail' => $imageUploader->thumbnailAsBase64Data($thumbnailFileNameString),
            ]
        );
    }

    #[Route('uebersicht', name: 'plakate_overview')]
    public function overviewMenu(): Response
    {
        return $this->render('plakatorte/overview_menu.htm.twig');
    }

    #[Route('meine/{activeOnly}', name: 'plakate_my', defaults: ['activeOnly' => ''])]
    public function myPostersMenu(Request $request, string $activeOnly, ElectionPosterRepository $repo): Response
    {
        $year = $request->query->get('?year');

        return $this->render(
            'plakatorte/overview_my.htm.twig',
            [
                'activeOnly' => $activeOnly,
                'posters' => $repo->findAllByUser(
                    $this->getUser()?->getUserIdentifier(),
                    $activeOnly,
                    $year
                ),
                'years' => $repo->findAllYearsByUser($this->getUser()?->getUserIdentifier(), $activeOnly),
                'filteredYear' => $year,
            ]
        );
    }

    #[Route('meine/export/{activeOnly}', name: 'plakate_my_export', defaults: ['activeOnly' => ''])]
    public function myPostersExport(Request $request, string $activeOnly, ElectionPosterRepository $repo): RedirectResponse|BinaryFileResponse
    {
        $year = $request->get('year');
        $data = $repo->findAllByUser($this->getUser()?->getUserIdentifier(), $activeOnly, $year);
        try {
            $fs = new Filesystem();
            if (!$fs->exists($this->varFolder)) {
                $fs->mkdir($this->varFolder);
            }
            $fileName = $this->varFolder . Uuid::uuid4() . '.csv';
            $fs->touch($fileName);
            $writer = Writer::createFromPath($fileName, 'w+');
            $writer->setDelimiter(';');
            $writer->insertOne(['Address', 'Description', 'Active', 'Created At']);
            foreach ($data as $poster) {
                $writer->insertOne([
                    sprintf(
                        'Address: %s, City: %s, District: %s, State: %s, Long: %s, Lat: %s ',
                        $poster->getAddress()->getAddress(),
                        $poster->getAddress()->getCity(),
                        $poster->getAddress()->getDistrict(),
                        $poster->getAddress()->getState(),
                        $poster->getAddress()->getLongitude(),
                        $poster->getAddress()->getLatitude(),
                    ),
                    $poster->getDescription(),
                    $poster->isActive() ? 'Y' : 'N',
                    $poster->getCreatedAt()->format('Y-m-d H:i:s'),
                ]);
            }
        } catch (CannotInsertRecord $e) {
            $this->logger->error($e->getRecord());
            $this->addFlash('error', 'Cannot Download file');

            return $this->redirectToRoute('plakate_my');
        }

        return $this->file(new File($fileName), 'Plakate.csv');
    }
}
