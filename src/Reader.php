<?php

namespace Epic;

class Reader
{
    private string $csvDelimiter;

    public function __construct(string $delim = ';')
    {
        $this->csvDelimiter = $delim;
    }

    public function getData(): array
    {
        $f = fopen('php://stdin', 'r');
        $dataSet = [];
        while (($data = fgetcsv($f, 1000, $this->csvDelimiter)) !== FALSE) {
            $dataSet[] = $data;
        }
        fclose($f);
        return $dataSet;
    }
}
