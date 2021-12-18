<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__.'/../config/pathServer.php';

use Restfull\Plugins\Abstration;

$plugin = new Abstration();
$plugin->startClass('Gmaps', [4]);
echo $plugin->treatment('render', ['']);
