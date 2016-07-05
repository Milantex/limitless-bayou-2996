<?php
    require_once '../../vendor/autoload.php';

    $api = new Milantex\LimitlessBayou\LimitlessBayou('localhost', 'bayou', 'root', '', 'maps');
    $api->start();
