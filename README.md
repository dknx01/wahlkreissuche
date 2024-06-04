# Wahlkreissuche

**NUR ZU DEMO-ZWECKEN!**'

**Es sind nicht alle Änderungen aus der eigentlichen App drin bzw. auf ihre vollständige Funktionalität geprüft.**

ToDo:
* Erhöhung der Test-Coverage (WIP) 

Eine Anwendung/Webseite zur Unterstützung bei der Suche nach
und Darstellung von Wahlkreisen

* Anzeige der Wahlkreise und Suche, ob in welchem eine Adresse liegt
  * z.Z. vorrangig Berlin
* Erfassen von Standorten von gehangenen Plakaten


## Einrichtung
1. Anforderungen:
    * PHP 8.3+
    * Node.js/Yarn
    * Alternativ: Docker mit docker-compose (empfohlen)
2. Installation mittels Docker
    ```shell
    docker-compose up
    ```

    1. Installation im Docker Container "PHP"
       * Log-in in den PHP-Container
         * `docker ps` -> ID herausfinden
         * `docker exec -it <mycontainer> bash`
       * `composer install`
       * `bin/console doc:mig:mig -n`
       * `yarn install`
    * Log-in in den Mysql-Container
       * `docker ps` -> ID herausfinden
       * `docker exec -it <mycontainer> bash`
         * `mysql -u partei-wahlen -p partei-wahlen < data/wahlkreis_wahllokal.sql`
         * `mysql -u partei-wahlen -p partei-wahlen < data/wahlkreis_plakat_orte.sql`
         * eventuell noch mehr SQL-Dateien importieren, wenn nötig

3. Importieren der Wahlkreise
    `bin/console app:import:agh-kreise`
    `bin/console app:import:btw-kreise`
4. Lokale Anzeige
   https://localhost:8986

## Konfiguration

* Configure file `./wks-config/config.yaml`.
* Adjust translations in `./wks-config/translation/`
* Replace default brand image: replace the used asset in `./templates/base.html.twig` line 26 and put the asset in `./assets/images/`
* 

### Env-Vars

| Variable                       | Beschreibung                   | Values                                                                | Beispiel                                                |
|--------------------------------|--------------------------------|-----------------------------------------------------------------------|---------------------------------------------------------|
| APP_ENV                        | Application environment        | dev, prod                                                             | APP_ENV=dev                                             |
| APP_SECRET                     | Application Secret             | random string                                                         | 177e38a2b4a46878d855578676ea5ddb                        |
| MAILER_DSN=smtp://mailhog:1025 | smtp url                       | see [Symfony Mailer](https://symfony.com/doc/current/mailer.html)     | MAILER_DSN=smtp://mailhog:1025                          |
| DATABASE_URL                   | database url                   | see [Symfony Doctrine](https://symfony.com/doc/current/doctrine.html) | DATABASE_URL=sqlite:///%kernel.project_dir%/var/data.db |
| CORS_ALLOW_ORIGIN              | allow urls for CORS header     |                                                                       | CORS_ALLOW_ORIGIN='^https?://(localhost)?$'             |
| MATOMO_SITE_ID                 | Matomo site id                 |                                                                       | MATOMO_SITE_ID=1                                        |
| MATOMO_API_TOKEN               | Matomo api token               |                                                                       |                                                         |
| MATOMO_URL                     | Url of the Matomo installation |                                                                       | MATOMO_URL=http://localhost                             |

### Geojson Format

Koordinatensystem EPSG:4326 
Konvertierung: https://mygeodata.cloud/

SHP To geojson:
https://products.aspose.app/gis/conversion/shapefile-to-geojson