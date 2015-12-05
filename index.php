<?php
    require_once './sys/BayouCore.php';

    $mapName = filter_input(INPUT_GET, 'map', FILTER_SANITIZE_STRING);
    if (!preg_match(ApiMap::MAP_NAME_PATTERN, $mapName)) {
        new ApiResponse(ApiResponse::STATUS_ERROR, "Invalid map name or map name not supplied.");
    }

    $mapPath = 'app/maps/' . $mapName . '.map.php';
    if (!file_exists($mapPath)) {
        new ApiResponse(ApiResponse::STATUS_ERROR, "The requested map does not exist on this domain.");
    }

    $map = require_once $mapPath;

    new ApiResponse();

