<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:scrape-rekvizitai')]
class ScrapeRekvizitaiCommand extends ScrapeCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputFileName = $this->getOutputFileName('rekvizitai');
        $handle = fopen("/app/output/$outputFileName", 'a');
        $output->writeln("Output file: $outputFileName");
        $this->writeToFile($handle, "Name,Code,Director,Phone number,Created at\n");
        $companies = $this->getCompanies();
        $url = 'https://rekvizitai.vz.lt/';

        foreach ($companies as $company) {
            $client = new HttpBrowser();
            $crawler = $client->request('GET', $url);
            $form = $crawler->selectButton('Ieškoti')->form();
            $crawler = $client->submit($form, ['name' => $company]);
            $companyUrl = $crawler->filter('.company-title')->first()->attr('href');
            $crawler = $client->request('GET', $companyUrl);
            $companyName = $crawler->filter('h1.title')->first()->text();
            $companyCode = $crawler->filter('span#ccode')->first()->text();
            $values = $crawler->filter('td.value')->each(function ($node) {
                if (str_contains($node->text(), ', direktorius') || str_contains($node->text(), ', direktorė')) {
                    return $node->text();
                }
            });
            preg_match(
                '/[0-9]{4}-[0-9]{2}-[0-9]{2}/',
                $crawler->filter('.companyDescription')->first()->text(),
                $matches,
            );
            $director = explode(',', $values[1])[0];
            $phoneNumber = 'https://rekvizitai.vz.lt' .$crawler->filter('.marginTop3')->first()->attr('src');
            $createdAt = $matches[0];
            $this->writeToFile($handle, "\"$companyName\",$companyCode,$director,$phoneNumber,$createdAt\n");
        }

        fclose($handle);

        return Command::SUCCESS;
    }
}
