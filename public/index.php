<?php

use Hive\App;
use Hive\Database;
use Hive\Session;

require_once('../vendor/autoload.php');

const TEMPLATE_DIR = __DIR__ . '/../templates';

$dotenv = new Symfony\Component\Dotenv\Dotenv();
$dotenv->load(__DIR__.'/../.env');

$app = App::getInstance();
$app->setDatabase(new Database());
$app->setSession(new Session());
$app->handle();
