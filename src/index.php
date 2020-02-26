<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use VendingMachine\Prompt;
use VendingMachine\VendingMachine;

$vm = new VendingMachine(false);
$prompt = new Prompt($vm);

$prompt->start();