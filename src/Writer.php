<?php

namespace Epic;

class Writer
{
    private string $csvDelimiter;

    public function __construct(string $delim = ';')
    {
        $this->csvDelimiter = $delim;
    }

    public function write(array $data): void
    {
        $f = fopen('php://stdout', 'w+');
        foreach ($data as $row) {
            fputcsv($f, $row, $this->csvDelimiter);

        }
        fclose($f);
    }
}
