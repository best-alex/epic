<?php

namespace Epic;

class Task
{
    private array $data = [];
    private string $delim;

    public function __construct($csvDelimiter = ';')
    {
        $this->delim = $csvDelimiter;
    }

    private function prepareData(array $headerColNum): void
    {
        $data = $this->data;
        unset($data[0]);
        $this->data = [];
        foreach ($data as $row) {
            $this->data[$row[$headerColNum['ID']]] = $row;
        }
    }

    private function getDuplicateChains(array $headerColNum): array
    {
        $duplicateCheck = ['EMAIL', 'CARD', 'PHONE'];
        $duplicate = [];
        foreach ($duplicateCheck as $name) {
            $idNum = $headerColNum['ID'];
            $num = $headerColNum[$name];
            $duplicate[$name] = [];
            foreach ($this->data as $row) {
                $duplicate[$name][$row[$num]][] = $row[$idNum];
            }
        }
        return $duplicate;
    }

    private function crossDuplicateAndSetMinParent(array $duplicates): array
    {
        $result = [];
        foreach ($duplicates as $info) {
            foreach ($info as $relates) {
                $min = min($relates);
                foreach ($relates as $id) {
                    if (!isset($result[$id]['parent'])) {
                        $result[$id]['parent'] = $min;
                        $result[$id]['relative'] = $relates;
                    }
                    $result[$id]['relative'] = array_unique(array_merge($result[$id]['relative'], $relates));
                    foreach ($result[$id]['relative'] as $n) {
                        $result[$n]['relative'] = !empty($result[$n]['relative']) ? array_unique(
                            array_merge($result[$id]['relative'], $result[$n]['relative'])
                        ) : $result[$id]['relative'];
                        $result[$n]['parent'] = min($result[$n]['relative']);
                    }
                }
            }
        }
        ksort($result);
        return $result;
    }

    private function writeOut(array $crossed): void
    {
        $data = [];
        $data[] = ['ID', 'PARENT_ID'];
        foreach ($crossed as $id => $detail) {
            $data[] = [$id, $detail['parent']];
        }
        (new Writer($this->delim))->write($data);
    }

    public function run(): void
    {
        $this->data = (new Reader($this->delim))->getData();
        $headerColNum = $this->getHeaderColNums($this->data[0]);
        $this->prepareData($headerColNum);
        $duplicates = $this->getDuplicateChains($headerColNum);
        print_r($duplicates);
        unset($this->data);
        $crossed = $this->crossDuplicateAndSetMinParent($duplicates);
        $this->writeOut($crossed);
    }

    /**
     * @param array $headerData
     * @return array
     * @example  [ID] => 0, [PARENT_ID] => 1, [EMAIL] => 2, [CARD] => 3, [PHONE] => 4, [TMP] => 5
     */
    private function getHeaderColNums(array $headerData): array
    {
        $h = ['ID', 'PARENT_ID', 'EMAIL', 'CARD', 'PHONE', 'TMP'];
        $res = [];
        foreach ($h as $c) {
            foreach ($headerData as $num => $name) {
                if (strtoupper($name) === $c) {
                    $res[$c] = $num;
                }
            }
        }
        return $res;
    }
}
