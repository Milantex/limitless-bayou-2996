<?php
    require_once './sys/BayouCore.php';

    ob_clean();
    header('Content-type: text/json; charset=utf-8');
    $data = [
        'timestamp' => date('r'),
        'reference' => rand(1000000, 9999999),
        'ipaddress' => $_SERVER['REMOTE_ADDR']
    ];
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit();
