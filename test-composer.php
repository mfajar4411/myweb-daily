<?php

require 'vendor/autoload.php';

use Carbon\Carbon;

$sekarang = Carbon::now();

echo "Sekarang: $sekarang <br>";
echo "Umur saya: " . Carbon::createFromDate(2000, 1, 1)->age . "<br>";
echo "Besok: " . $sekarang->addDay() ."<br>";

?>