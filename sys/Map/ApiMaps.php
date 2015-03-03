<?php
    final class ApiMaps {
        private static $maps = array();

        public static function addMap($name, $description) {
            static::$maps[$name] = new ApiMap($name, $description);
        }

        public static function getMap($name) {
            if (isset(static::$maps[$name])) {
                return static::$maps[$name];
            } else {
                return FALSE;
            }
        }

        public static function getMaps() {
            return static::$maps;
        }
    }
