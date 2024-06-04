<?php

namespace App\Command;

use App\Entity\Wahllokal;
use App\Repository\WahllokalRepository;
use Brick\Geo\IO\GeoJSON\FeatureCollection;
use Brick\Geo\IO\GeoJSONReader;
use Ramsey\Collection\Collection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;

#[AsCommand(name: 'app:import:wahllokale:berlin', )]
class ImportWahllokaleBerlinCommand extends Command
{
    private const MAX_DISPLAY_COUNT = 10;

    public function __construct(
        private readonly WahllokalRepository $wahllokalRepo,
        private readonly string $dataFile,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Importing Wahllokale Berlin');

        $client = HttpClient::create();

        $reader = new GeoJSONReader();
        /** @var FeatureCollection $data */
        $data = $reader->read(file_get_contents($this->dataFile));

        $displayCounter = 0;
        $addressList = new Collection('string');

        $progressBar = $io->createProgressBar(count($data->getFeatures()));

        foreach ($data->getFeatures() as $feature) {
            if ($feature->getGeometry() === null) {
                continue;
            }

            $address =
            sprintf(
                '%s %s, %s Berlin',
                $feature->getProperty('STR'),
                $feature->getProperty('HNr'),
                $feature->getProperty('PLZ'),
            );

            $wahllokal = new Wahllokal();
            $wahllokal->setAdress($address);
            $wahllokal->setLatitude($feature->getGeometry()->withoutZ()->toArray()[1]);
            $wahllokal->setLongitude($feature->getGeometry()->withoutZ()->toArray()[0]);
            $wahllokal->setCity('Berlin');

            if ($addressList->contains($address)) {
                if ($displayCounter >= self::MAX_DISPLAY_COUNT) {
                    $progressBar->display();
                    $displayCounter = 0;
                } else {
                    ++$displayCounter;
                }
                $progressBar->advance();
                continue;
            }
            $response = $client->request(
                'GET',
                sprintf(
                    'https://nominatim.openstreetmap.org/reverse?lat=%s&lon=%s&format=jsonv2&limit=1',
                    $wahllokal->getLatitude(),
                    $wahllokal->getLongitude(),
                )
            );
            $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $district = '';
            $districtFields = ['borough', 'suburb'];

            foreach ($districtFields as $field) {
                if (!array_key_exists($field, $content['address'])) {
                    continue;
                }
                $district = $content['address'][$field];
                break;
            }

            $wahllokal->setDistrict($district);
            $wahllokal->setDescription($feature->getProperty('Bezeich1'));
            $wahllokal->setRadius(30);
            $this->wahllokalRepo->save($wahllokal);
            $addressList->add($wahllokal->getAdress());

            if ($displayCounter >= self::MAX_DISPLAY_COUNT) {
                $progressBar->display();
                $displayCounter = 0;
            } else {
                ++$displayCounter;
            }
            $progressBar->advance();
        }
        $progressBar->finish();
        $io->newLine();

        return Command::SUCCESS;
    }
}
