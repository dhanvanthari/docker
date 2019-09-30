<?php

namespace AppBundle\Serializer;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class XlsxEncoder implements EncoderInterface
{
    public const FORMAT = 'xlsx';
    public const HEADERS_KEY = 'export_file_headers';

    public function encode($data, $format, array $context = [])
    {
        if (!is_iterable($data)) {
            throw new \InvalidArgumentException('This method requires an array of data');
        }

        $data = $this->prepareData($data);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = [];

        if (isset($context[self::HEADERS_KEY]) && $headers = $context[self::HEADERS_KEY]) {
            $sheet->fromArray(array_values($headers));
        } elseif (isset($data[0]) && \is_array($data[0])) {
            $sheet->fromArray(array_keys($data[0]));
        }

        if (\count($data) > 0) {
            $sheet->fromArray(self::prepareArrayValues($data, $headers), null, 'A2');
        }

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $value = ob_get_contents();
        ob_end_clean();

        return $value;
    }

    public function supportsEncoding($format)
    {
        return self::FORMAT === $format;
    }

    /**
     * @return array[]
     */
    private static function prepareArrayValues(array $arrayObjectValues, array $headers = []): array
    {
        $result = [];

        foreach ($arrayObjectValues as $arrayValues) {
            if ($headers) {
                $line = $headers;
                foreach ($arrayValues as $name => $value) {
                    $line[$name] = $value;
                }
                $result[] = array_values($line);
            } else {
                $result[] = array_values($arrayValues);
            }
        }

        return $result;
    }

    private function prepareData(array $data): array
    {
        $newData = [];

        foreach ($data as $row) {
            if (!\is_array($row)) {
                throw new \InvalidArgumentException('This method requires an array of arrays');
            }

            $newData[] = $this->flattenArray('', $row);
        }

        return $newData;
    }

    private function flattenArray(string $prefix, array $data): array
    {
        $newData = [];

        foreach ($data as $key => $value) {
            $label = ($prefix ? $prefix.'.' : '').$key;

            if (\is_array($value)) {
                $newData += $this->flattenArray($label, $value);
            } else {
                $newData[$label] = $value;
            }
        }

        return $newData;
    }
}
