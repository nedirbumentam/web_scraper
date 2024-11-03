# INSTALL:

* docker compose up --detach --force-recreate --build
* docker exec app composer install
* cd js
* npm i puppeteer

# SCRAPE CLI COMMANDS:

* docker exec app bin/console app:scrape-rekvizitai
* docker exec app bin/console app:scrape-fsaskaita
* node okredo.js

## Input file:
    input/companies.csv
## Output dir:
    output/
