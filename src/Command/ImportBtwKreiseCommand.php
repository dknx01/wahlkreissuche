<?php

namespace App\Command;

use App\Entity\Wahlkreis;
use App\Repository\WahlkreisRepository;
use Brick\Geo\IO\GeoJSON\FeatureCollection;
use Brick\Geo\IO\GeoJSONReader;
use Brick\Geo\Polygon;
use LongitudeOne\Spatial\PHP\Types\Geometry\MultiPolygon;
use LongitudeOne\Spatial\PHP\Types\Geometry\Polygon as DatabasePolygon;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

use function Symfony\Component\String\u;

#[AsCommand(name: 'app:import:btw-kreise', )]
class ImportBtwKreiseCommand extends Command
{
    public function __construct(
        private readonly WahlkreisRepository $wahlkreisRepo,
        /** @var array<string, string> */
        private readonly array $dataFiles,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Importing BTW wahlkreise');

        $choices = array_keys($this->dataFiles);

        $question = new ChoiceQuestion('Which data should be imported?', $choices);
        $question->setAutocompleterValues(array_keys($this->dataFiles))
            ->setNormalizer(fn ($value) => is_int($value) ? $choices[$value] : $value)
            ->setValidator(
                fn ($answer) => in_array($answer, $choices, true) ? $answer : throw new \RuntimeException('The value "%s" is invalid. Please choose from the provided list.')
            );

        $dataFile = $this->dataFiles[$io->askQuestion($question)];

        $fs = new Filesystem();
        $jsonFile = u($dataFile)->replace('.shp', '.geojson');
        if (!$fs->exists($jsonFile)) {
            $io->error('No JSON'); // @Todo import shp file
        }

        $reader = new GeoJSONReader();
        /** @var FeatureCollection $data */
        $data = $reader->read(file_get_contents($dataFile));

        $progressBar = $io->createProgressBar();

        foreach ($data->getFeatures() as $feature) {
            if ($feature->getGeometry() === null) {
                continue;
            }

            $geometry = new Wahlkreis\Geometry(
                $feature->getGeometry()->geometryType(),
                $feature->getGeometry()->toArray()
            );
            $btw = new Wahlkreis\Btw(
                number: $feature->getProperties()->WKR_NR,
                name: $feature->getProperties()->WKR_NAME,
                stateName: $feature->getProperties()->LAND_NAME,
                stateNumber: $feature->getProperties()->LAND_NR
            );

            if ($feature->getGeometry()->geometryType() === 'MultiPolygon') {
                $multiPolygon = new MultiPolygon([]);
                /** @var Polygon $item */
                foreach ($feature->getGeometry() as $item) {
                    $ring = $item->withoutZ()->toArray();
                    $polygonType = new DatabasePolygon($ring);
                    $multiPolygon->addPolygon($polygonType);
                }
                $geometry->setGeometry($multiPolygon);
            } else {
                $polygon = new DatabasePolygon(
                    $feature->getGeometry()->withoutZ()->toArray()
                );
                $geometry->setGeometry($polygon);
            }

            $wahlkreis = new Wahlkreis(
                $geometry,
                'BTW',
                $btw->getStateName(),
                new Wahlkreis\Agh(),
                $btw,
                new Wahlkreis\GenericWahlKreis()
            );

            $this->wahlkreisRepo->save($wahlkreis);

            $progressBar->display();
            $progressBar->advance();
        }
        $progressBar->finish();
        $io->newLine();

        return Command::SUCCESS;
    }
}
