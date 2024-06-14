<?php

declare(strict_types=1);

namespace App\IntegrationTests;
use App\Options\States;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class HomePageTest extends WebTestCase
{
    public function testHomepage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h5', 'Suche Wahlkreis');
        self::assertAnySelectorTextContains('.card-text', 'Wahlkreise (AGH und BTW) anhand einer Adresse');

        $states = States::STATES;
        $crawler->filter('select[id=state]')->children('option')->each(
            function (Crawler $crawler) use (&$states) {
                if (in_array($crawler->text(), $states)) {
                    $states[$crawler->text()] = true;
                }
            }
        );
        $this->assertEmpty(array_filter($states, fn ($v) => $v !== true), 'At least one state was not found in the list');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        while (true) {
            $previousHandler = set_exception_handler(static fn() => null);

            restore_exception_handler();

            if ($previousHandler === null) {
                break;
            }

            restore_exception_handler();
        }
    }
}
