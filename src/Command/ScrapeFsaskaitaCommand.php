<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:scrape-fsaskaita')]
class ScrapeFsaskaitaCommand extends ScrapeCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputFileName = $this->getOutputFileName('fsaskaita');
        $handle = fopen("/app/output/$outputFileName", 'a');
        $output->writeln("Output file: $outputFileName");
        $this->writeToFile($handle, "Name,Code,Director,Phone number,Created at\n");
        $companies = $this->getCompanies();
        $url = 'https://fsaskaita.lt/imones';

        foreach ($companies as $company) {
            $client = new HttpBrowser();
            $crawler = $client->request('GET', $url);
            $form = $crawler->selectButton('Ieškoti')->form();
            $crawler = $client->submit($form, ['search' => $company]);
            $companyUrl = $crawler->filter('.text-gray-900.text-xl.font-semibold')->first()->attr('href');
            $crawler = $client->request('GET', $companyUrl);
            $companyName = $crawler->filter('h1.text-2xl.font-semibold')->first()->text();
            $values = $crawler->filter('.text-sm.font-regular.w-full.mt-1')->each(function ($node) {return $node->text();});
            $this->writeToFile($handle, "\"$companyName\",$values[0],$values[1],$values[3],$values[4]\n");
        }

        fclose($handle);

        return Command::SUCCESS;
    }
}
