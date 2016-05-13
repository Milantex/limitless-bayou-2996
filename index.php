<?php
    require_once './sys/BayouCore.php';

    define('REQUEST_TIME', microtime(true));

    $mapName = filter_input(INPUT_GET, 'map', FILTER_SANITIZE_STRING);
    if (!preg_match(ApiMap::MAP_NAME_PATTERN, $mapName)) {
        new ApiResponse(ApiResponse::STATUS_ERROR, "Invalid map name or map name not supplied.");
    }

    $mapPath = 'app/maps/' . $mapName . '.map.php';
    if (!file_exists($mapPath)) {
        new ApiResponse(ApiResponse::STATUS_ERROR, "The requested map does not exist on this domain.");
    }

    $map = require_once $mapPath;

    $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');

    if ($method == 'POST') {
        $request = file_get_contents("php://input");
        $json = json_decode($request);

        if (!$json or !is_object($json)) {
            new ApiResponse(ApiResponse::STATUS_ERROR, "The request data is not a valid JSON structure.");
        }

        $map->handle($json);
    } else {
        new ApiResponse(ApiResponse::STATUS_INFO, $map->describe());
    }
