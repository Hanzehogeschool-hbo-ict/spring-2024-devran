<?php

use Hive\App;

require_once('../vendor/autoload.php');

const TEMPLATE_DIR = __DIR__ . '/../templates';

$dotenv = new Symfony\Component\Dotenv\Dotenv();
$dotenv->load(__DIR__.'/../.env');

$app = App::getInstance();
$app->handle();

?>