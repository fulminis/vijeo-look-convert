<?php
include 'parse.php';
$projectPath = realpath('../data/');
$outPath = realpath('../dataOut/');

$imagesSrc = $projectPath . '/Bitmap Files/';
$imagesDest = $outPath . '/img/';
$paleteFile = file_get_contents($projectPath . '/Config Files/palcol.dat');
foreach (glob($projectPath . '/Mimic Files/' .'*') as $mimic) {
   $data = file($mimic);
    @list($encoding, $day, $month, $year, $time) = explode(',', $data[0]);

    if($encoding == 'ASCII32') {
    	echo 'Parsing file: ', $mimic, PHP_EOL;
    } else {
    	echo 'Skiping file: ', $mimic, PHP_EOL;
    	continue;
    }

    $svg = parse($data, $svg);

    $dom = new DOMDocument("1.1");
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($svg->asXML());
    //echo $dom->saveXML();
    $outFile = $outPath . '/' . basename($mimic) . '.html';
    $html = file_get_contents('template.html');
    $html = str_replace('~~TO_REPLACE~~', $dom->saveXML($dom->documentElement), $html);
    $html = str_replace('~~STYLE_TO_REPLACE~~{}', $fonts, $html);
    //var_dump($x);
    if(file_put_contents($outFile, $html)) {
    	echo 'File: ', $outFile, ' saved', PHP_EOL;
    } else {
    	echo 'Error on saving: ', $outFile;
    }
}

// $v = new visioConvert('wmf/');
// $v->convert();
echo PHP_EOL;