<?php declare(strict_types=1);

use SepMsi\Framework\Http\Kernel;
use SepMsi\Framework\Http\Request;

define('BASE_PATH', dirname(__DIR__));

require_once dirname(__DIR__) . '/vendor/autoload.php';

$request = Request::globalsPack();

$kernel = new Kernel();

$response = $kernel->handle($request);

$response->send();