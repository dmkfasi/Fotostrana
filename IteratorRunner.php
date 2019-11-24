<?php

require 'SeekableIteratorFileReader.php';

$sifr = new SeekableIteratorFileReader('hugetextfile.txt');

$sifr->open();
$sifr->setBlockSize(32);
$sifr->seek(10);


for ($i = 0; $i < 8; $i++) {
  testPrint($sifr);
  $sifr->next();
}

for ($i = 0; $i < 16; $i++) {
  testPrint($sifr);
  $sifr->prev();
}

$sifr->close();


function testPrint(SeekableIteratorFileReader $sifr) {
  echo "[ Current position @{$sifr->key()} | Block content: `{$sifr->current()}` ]\n";
}