# INSTALL:

* docker compose up --detach --force-recreate --build
* docker exec app composer install

# SCRAPE CLI COMMANDS:

* docker exec app bin/console app:scrape-rekvizitai
* docker exec app bin/console app:scrape-fsaskaita

## Input file:
    input/companies.csv
## Output dir:
    output/
