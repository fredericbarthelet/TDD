<?php

$number='22?@5?@3';
$delimiter='?@';
$result = substr($number, 0, strpos($number, $delimiter));
$rest = substr($number, strpos($number, $delimiter) + strlen($delimiter));

print_r($rest);


