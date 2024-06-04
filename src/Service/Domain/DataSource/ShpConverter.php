<?php

namespace App\Service\Domain\DataSource;

use Shapefile\Shapefile;
use Shapefile\ShapefileReader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\UnicodeString;

use function Symfony\Component\String\u;

class ShpConverter
{
    /** @var array<string, string> */
    private array $unicodeReplace = [
        '\u00fc' => 'ü',
        '\u2013' => '-',
        '\u00f6' => 'ö',
        '\u00e4' => 'ä',
        '\u00df' => 'ß',
    ];

    public function __construct(private Filesystem $fs)
    {
    }

    /**
     * @throws \RuntimeException
     */
    public function convert(string $input, string $output, Config $config): void
    {
        if (!$this->fs->exists($input)) {
            throw new \RuntimeException(sprintf('File %s does not exists', $input));
        }
        $shapefileReader = new ShapefileReader(
            $input,
            [
                Shapefile::OPTION_SUPPRESS_Z,
                Shapefile::OPTION_DBF_CONVERT_TO_UTF8,
            ]
        );

        while ($geometry = $shapefileReader->fetchRecord()) {
            // Skip the record if marked as "deleted"
            if ($geometry->isDeleted()) {
                continue;
            }

            $content = match ($config->outputFormat) {
                Config::AS_GEOJSON => u($geometry->getGeoJSON(flag_bbox: false, flag_feature: true)),
                Config::AS_WKT => $geometry->getArray(),
                Config::AS_ARRAY => $geometry->getWKT(),
                default => throw new \Exception('Unknown config type format ' . $config->outputFormat)
            };

            if ($content instanceof UnicodeString) {
                foreach ($this->unicodeReplace as $unicode => $replace) {
                    $content = $content->replace($unicode, $unicode);
                }
            }

            $this->fs->appendToFile($output, $content);

            if ($config->withDbfData) {
                $this->fs->appendToFile(
                    $output . '.dbf_data',
                    var_export($geometry->getDataArray(), true)
                );
            }
        }
    }
}
