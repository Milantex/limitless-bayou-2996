<?php
    namespace Milantex\LimitlessBayou\Sys;

    use Milantex\LimitlessBayou\Sys\Map\ApiMap as ApiMap;
    use Milantex\LimitlessBayou\Sys\ApiResponse as ApiResponse;
    use Milantex\LimitlessBayou\Sys\DataBase as DataBase;

    final class LimitlessBayou {
        private $dbHost, $dbName, $dbUser, $dbPass;
        private $mapsPath;
        private $startTime;

        /**
         * The DataBase object singleton instance holder
         * @var DataBase
         */
        private $database = NULL;

        function getDbHost() {
            return $this->dbHost;
        }

        function getDbName() {
            return $this->dbName;
        }

        function getDbUser() {
            return $this->dbUser;
        }

        function getDbPass() {
            return $this->dbPass;
        }

        function getStartTime() {
            return $this->startTime;
        }

        /**
         * Returns an instance of the database (the connections is opened once)
         * @return DataBase
         */
        public function getDatabase() : DataBase {
            if ($this->database == NULL) {
                $this->database = new DataBase($this->getDbHost(), $this->getDbName(), $this->getDbUser(), $this->getDbPass());
            }

            return $this->database;
        }

        public function __construct(string $dbHost, string $dbName, string $dbUser, string $dbPass, string $mapsPath) {
            $this->dbHost = $dbHost;
            $this->dbName = $dbName;
            $this->dbUser = $dbUser;
            $this->dbPass = $dbPass;
            $this->mapsPath = $mapsPath;

            if (!file_exists($mapsPath) or !is_dir($mapsPath)) {
                $this->respondWithError('Service missconfigured. The map directory does not exist or is not a directory.');
            }
        }

        /**
         * The destructor is used solely to close the database connection
         */
        public function __destruct() {
            $this->database = NULL;
        }

        public function start() {
            $this->startTime = microtime(true);

            $mapName = $this->getMapName();
            $mapPath = $this->getMapPathIfValid($mapName);

            $map = require_once $mapPath;

            $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');

            if ($method == 'POST') {
                $request = file_get_contents("php://input");
                $json = json_decode($request);

                if (!$json or !is_object($json)) {
                    $this->respondWithError("The request data is not a valid JSON structure.");
                }

                $map->handle($json, $this);
            } else {
                $this->respondWithInfo($map->describe());
            }
        }

        private function getMapName() : string {
            $mapName = filter_input(INPUT_GET, 'map', FILTER_SANITIZE_STRING);

            if (!preg_match(ApiMap::MAP_NAME_PATTERN, $mapName)) {
                $this->respondWithError("Invalid map name or map name not supplied.");
            }

            return $mapName;
        }

        private function getMapPathIfValid($mapName) {

            $mapPath = $this->mapsPath . '/' . $mapName . '.map.php';
            if (!file_exists($mapPath)) {
                $this->respondWithError("The requested map does not exist on this domain.");
            }

            return $mapPath;
        }

        public function respondWithError($content = []) {
            new ApiResponse($this, ApiResponse::STATUS_ERROR, $content);
        }

        public function respondWithOk($content = []) {
            new ApiResponse($this, ApiResponse::STATUS_OK, $content);
        }

        public function respondWithInfo($content = []) {
            new ApiResponse($this, ApiResponse::STATUS_INFO, $content);
        }
    }
