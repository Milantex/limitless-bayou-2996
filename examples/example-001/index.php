<?php
    require_once '../../vendor/autoload.php';

    $api = new Milantex\LimitlessBayou\Sys\LimitlessBayou('localhost', 'bayou', 'root', '', 'maps');
    $api->start();
