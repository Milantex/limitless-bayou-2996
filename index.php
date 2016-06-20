<?php
    require_once __DIR__ . '/vendor/autoload.php';

    $api = new Milantex\LimitlessBayou\Sys\LimitlessBayou('localhost', 'bayou', 'root', '', 'app/maps');
    $api->start();
