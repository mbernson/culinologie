<?php

require 'script/parser.php';

// Import

$params = [
    'cookbook' => 'elBulli1998-2002',
    'language' => 'uk',
];

$path = "/Volumes/{$params['cookbook']}/{$params['language']}/data/";
$contents = file_get_contents($path.'478.dat');

$recipe = new ElBulliRecipe($contents);
var_dump($recipe);
/* $recipe->insert(); */

exit;

$directory = new DirectoryIterator($path);
$i = 455;

foreach($directory as $file) {
    if($i >= 825) break;

    if(!$file->isDot() && $file->getExtension() == 'dat') {
        echo "Reading file {$file->getPathname()}\r\n";
        $contents = file_get_contents($file->getPathname());
        $recipe = new ElBulliRecipe($contents, $params);
        var_dump($recipe->toArray());
        echo "\n";
    } else {
        echo "Skipping file {$file->getPathname()}\r\n";
    }
    $i++;
}
