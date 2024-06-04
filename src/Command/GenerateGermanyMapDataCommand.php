<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Domain\WahlkreisHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:generate:btw-kreise-germany', )]
class GenerateGermanyMapDataCommand extends Command
{
    public function __construct(
        private readonly WahlkreisHandler $wahlkreisHandler,
        private readonly string $mapFile,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Generating Germany maps data');

        file_put_contents(
            $this->mapFile,
            $this->wahlkreisHandler->getBtwWahlkreiseDeutschlandAsString()
        );

        return self::SUCCESS;
    }
}
