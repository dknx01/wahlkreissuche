<?php

namespace App\Command;

use App\Service\Domain\DataSource\Config;
use App\Service\Domain\DataSource\ShpConverter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand('app:shp2geojson')]
class ConvertShpFilesCommand extends Command
{
    public function __construct(private readonly string $path, private readonly ShpConverter $converter)
    {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this->addArgument(
            'file',
            InputArgument::OPTIONAL,
            'file to be converted'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fs = new Filesystem();
        $path = $this->path;
        if ($file = $input->getArgument('file')) {
            $path = $file;
        }
        $out = str_replace('.shp', '.geosjon', $path);
        $fs->remove($out);

        $this->converter->convert($this->path, $out, new Config(Config::AS_GEOJSON));

        return Command::SUCCESS;
    }
}
