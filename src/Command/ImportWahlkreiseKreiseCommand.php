<?php

namespace App\Command;

use App\Entity\Wahlkreis;
use App\Options\States;
use App\Repository\WahlkreisRepository;
use App\Service\Import\Config\Config;
use Brick\Geo\IO\GeoJSON\FeatureCollection;
use Brick\Geo\IO\GeoJSONReader;
use Brick\Geo\Polygon;
use JsonSchema\Validator;
use LongitudeOne\Spatial\PHP\Types\Geometry\MultiPolygon;
use LongitudeOne\Spatial\PHP\Types\Geometry\Polygon as DatabasePolygon;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import:wahl-kreise',
)]
class ImportWahlkreiseKreiseCommand extends Command
{
    public function __construct(
        private readonly string $schemaPath,
        private readonly string $baseDir,
        private readonly WahlkreisRepository $wahlkreisRepo,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addOption(
            'config',
            'c',
            InputOption::VALUE_REQUIRED,
            'the configuration file for the import'
        )
        ->addArgument(
            'file',
            InputArgument::REQUIRED,
            'the geojson file to be imported'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Importing Wahlkreise');
        $config = $input->getOption('config');

        $config = json_decode(file_get_contents($this->baseDir . $config), false, 512, JSON_THROW_ON_ERROR);

        $validator = new Validator();
        $validator->validate($config, (object) ['$ref' => 'file://' . $this->schemaPath]);

        if (!$validator->isValid()) {
            $io->error(sprintf('The config file %s is invalid', 'dfs'));
            /** @var array<string, mixed> $error */
            foreach ($validator->getErrors() as $error) {
                $io->error(
                    sprintf(
                        'Property: %s %sError: %s',
                        $error['property'] ?? '',
                        PHP_EOL,
                        $error['message'] ?? ''
                    )
                );
            }

            return Command::FAILURE;
        }
        $config = new Config($config);

        $dataFile = $input->getArgument('file');

        $reader = new GeoJSONReader();
        /** @var FeatureCollection $data */
        $data = $reader->read(file_get_contents($dataFile));

        $progressBar = $io->createProgressBar();

        foreach ($data->getFeatures() as $feature) {
            if (null === $feature->getGeometry()) {
                continue;
            }

            $geometry = new Wahlkreis\Geometry(
                $feature->getGeometry()->geometryType(),
                $feature->getGeometry()->toArray()
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

            $genericWahlkreis = new Wahlkreis\GenericWahlKreis(
                $config->wkLongDescription ? $feature->getProperties()->{$config->wkLongDescription} : null,
                $config->wkShortDescription ? $feature->getProperties()->{$config->wkShortDescription} : null,
                $feature->getProperties()->{$config->wkName},
                $feature->getProperties()->{$config->wkNr},
            );
            $wahlkreis = new Wahlkreis(
                $geometry,
                $config->type,
                array_search($config->state, States::STATES, true),
                new Wahlkreis\Agh(),
                new Wahlkreis\Btw(),
                $genericWahlkreis
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
