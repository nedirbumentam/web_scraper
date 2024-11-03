<?php

declare(strict_types=1);

namespace App\Command;

use DateTime;
use Symfony\Component\Console\Command\Command;

abstract class ScrapeCommand extends Command
{
    protected function getOutputFileName(string $source): string
    {
        $timestamp = (new DateTime())->getTimestamp();

        return "$source-$timestamp.csv";
    }

    protected function getCompanies(): array
    {
        $companies = [];

        if (($handle = fopen('/app/input/companies.csv', 'r')) !== false) {
            while (($fields = fgetcsv($handle)) !== false) {
                foreach ($fields as $field) {
                    $companies[] = $field;
                }
            }

            fclose($handle);
        }

        return $companies;
    }

    protected function writeToFile($handle, string $text): void
    {
        fwrite($handle, $text);
    }
}
