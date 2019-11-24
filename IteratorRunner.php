<?php

require 'SeekableIteratorFileReader.php';

$sifr = new SeekableIteratorFileReader('hugetextfile.txt');

$sifr->open();
$sifr->setBlockSize(1024);
$sifr->seek(10);
$sifr->readBlock();
echo $sifr->current();
$sifr->close();