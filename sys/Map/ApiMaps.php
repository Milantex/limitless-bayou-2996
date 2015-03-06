<?php
    final class ApiMaps {
        private static $maps = [];

        public static function addMap(string $name, string $description) {
            static::$maps[$name] = new ApiMap($name, $description);
        }

        public static function getMap(string $name) : ApiMap {
            return static::$maps[$name] ?? FALSE;
        }

        public static function getMaps() : array {
            return static::$maps;
        }
    }
