<?php
require 'vendor/autoload.php';
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', "'01123");
$sheet->setCellValue('A2', "01123");
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save('test.xlsx');

$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
$spreadsheet2 = $reader->load('test.xlsx');
$rows = $spreadsheet2->getActiveSheet()->toArray();
var_dump($rows);
