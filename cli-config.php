<?php

// cli-config.php
require_once 'vendor/autoload.php';
use Root\Application;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

$app = new Application();

return ConsoleRunner::createHelperSet($app->loadEntityManagerConfiguration());

