<?php

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Dev\Downloadexcel\Excel;

$excel = new Excel();
$excel->addData();