<?php

namespace App\Command;

use App\Repository\WahlkreisRepository;
use Brick\Geo\CoordinateSystem;
use Brick\Geo\Engine\GeometryEngineRegistry;
use Brick\Geo\Engine\SQLite3Engine;
use Brick\Geo\LineString;
use Brick\Geo\Point;
use LongitudeOne\Spatial\PHP\Types\Geometry\Polygon;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:search:btw-kreise', )]
class SearchBtwKreiseCommand extends Command
{
    public function __construct(
        private readonly WahlkreisRepository $wahlkreisRepo,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Search BTW wahlkreise');
        $point = Point::xy(13.23288546182314, 52.76553198819388);

        $this->wahlkreisRepo->findByPoint($point);

        return self::SUCCESS;
    }

    protected function findInPolygon(Polygon $geometry): void
    {
        $cs = new CoordinateSystem(false, false);
        $points = [];
        foreach ($geometry->toArray() as $ring) {
            foreach ($ring as $coordinates) {
                $points[] = new Point($cs, $coordinates[1], $coordinates[0]);
            }
        }
        $ls = new LineString($cs, ...$points);
        $polygon = new \Brick\Geo\Polygon($cs, $ls);

        $findingPoint = new Point($cs, 52.453044141936246, 13.569103074522788);
        // $findingPoint = Point::xy(52.75357916803428, 13.236185053057781);
        $sqlite3 = new \SQLite3(':memory:');
        $sqlite3->loadExtension('mod_spatialite.so');
        GeometryEngineRegistry::set(new SQLite3Engine($sqlite3));
        dump($polygon->contains($findingPoint));
    }
}
