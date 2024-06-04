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
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import:agh-kreise',
)]
class ImportAghKreiseCommand extends Command
{
    public function __construct(
        private readonly WahlkreisRepository $wahlkreisRepo,
        private readonly string $dataFile,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Importing AGH wahlkreise');

        $reader = new GeoJSONReader();
        /** @var FeatureCollection $data */
        $data = $reader->read(file_get_contents($this->dataFile));

        $progressBar = $io->createProgressBar();

        foreach ($data->getFeatures() as $feature) {
            if (null === $feature->getGeometry()) {
                continue;
            }

            $geometry = new Wahlkreis\Geometry(
                $feature->getGeometry()->geometryType(),
                $feature->getGeometry()->toArray()
            );
            $agh = new Wahlkreis\Agh(
                wahlkreisLong: $feature->getProperties()->AWK,
                wahlkreisShort: $feature->getProperties()->AWK2,
                bezirk: $this->getBezirk($feature->getProperties()->BEZ)
            );

            if ('MultiPolygon' === $feature->getGeometry()->geometryType()) {
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
                'AGH',
                'Berlin',
                $agh,
                new Wahlkreis\Btw(),
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

    private function getBezirk(string $bezNumber): string
    {
        return match ($bezNumber) {
            '01' => 'Mitte',
            '02' => 'Friedrichshain-Kreuzberg',
            '03' => 'Pankow',
            '04' => 'Charlottenburg-Wilmersdorf',
            '05' => 'Spandau',
            '06' => 'Steglitz-Zehlendorf',
            '07' => 'Tempelhof-Schöneberg',
            '08' => 'Neukölln',
            '09' => 'Treptow-Köpenick',
            '10' => 'Marzahn-Hellersdorf',
            '11' => 'Lichtenberg',
            '12' => 'Reinickendorf',
            default => '',
        };
    }
}
