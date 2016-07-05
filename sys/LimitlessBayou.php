<?php
    namespace Milantex\LimitlessBayou;

    use Milantex\LimitlessBayou\Map\ApiMap as ApiMap;
    use Milantex\LimitlessBayou\ApiResponse as ApiResponse;
    use Milantex\DAW\DataBase as DataBase;

    /**
     * The main logic of the LimitlessBayou application is located in this class
     */
    final class LimitlessBayou {
        /**
         * The database host name
         * @var string
         */
        private $dbHost;

        /**
         * The name of the database whose tables are mapped by the API maps
         * @var string
         */
        private $dbName;

        /**
         * The username to use when connecting to the database
         * @var stirng
         */
        private $dbUser;

        /**
         * The password of the specified database user
         * @var string
         */
        private $dbPass;

        /**
         * The path where the application stores API map files .map.php
         * @var string
         */
        private $mapsPath;

        /**
         * The time when the application starts
         * @var int
         */
        private $startTime;

        /**
         * The DataBase object singleton instance holder
         * @var DataBase
         */
        private $database = NULL;

        /**
         * Returns the database host name
         * @return string
         */
        function getDbHost() {
            return $this->dbHost;
        }

        /**
         * Returns the database name
         * @return string
         */
        function getDbName() {
            return $this->dbName;
        }

        /**
         * Returns the database user
         * @return string
         */
        function getDbUser() {
            return $this->dbUser;
        }

        /**
         * Returns the database user's password
         * @return string
         */
        function getDbPass() {
            return $this->dbPass;
        }

        /**
         * Returns the time when the request handling had begun
         * @return int
         */
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

        /**
         * The LimitIterator application constructor.
         * This constructor takes the connection parameters for the database and
         * the path to the directory where API map files (.map.php) are stored.
         * @param string $dbHost
         * @param string $dbName
         * @param string $dbUser
         * @param string $dbPass
         * @param string $mapsPath
         */
        public function __construct(string $dbHost, string $dbName, string $dbUser, string $dbPass, string $mapsPath) {
            $this->dbHost = $dbHost;
            $this->dbName = $dbName;
            $this->dbUser = $dbUser;
            $this->dbPass = $dbPass;
            $this->mapsPath = $mapsPath;
            $this->startTime = microtime(true);

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

        /**
         * Begin handling the received request from the client
         */
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

        /**
         * Retrieve the name of the requested map from the clients API request
         * @return string
         */
        private function getMapName() : string {
            $mapName = filter_input(INPUT_GET, 'map', FILTER_SANITIZE_STRING);

            if (!preg_match(ApiMap::MAP_NAME_PATTERN, $mapName)) {
                $this->respondWithError("Invalid map name or map name not supplied.");
            }

            return $mapName;
        }

        /**
         * Check if the requested map file exists. If it does, return its path.
         * @param string $mapName
         * @return string
         */
        private function getMapPathIfValid($mapName) {

            $mapPath = $this->mapsPath . '/' . $mapName . '.map.php';
            if (!file_exists($mapPath)) {
                $this->respondWithError("The requested map does not exist on this domain.");
            }

            return $mapPath;
        }

        /**
         * Send an API response with the status 'error'
         * @param mixed $content
         */
        public function respondWithError($content = []) {
            (new ApiResponse($this, ApiResponse::STATUS_ERROR, $content))->send();
        }

        /**
         * Send an API response with the status 'ok'
         * @param mixed $content
         */
        public function respondWithOk($content = []) {
            (new ApiResponse($this, ApiResponse::STATUS_OK, $content))->send();
        }

        /**
         * Send an API response with the status 'info'
         * @param mixed $content
         */
        public function respondWithInfo($content = []) {
            (new ApiResponse($this, ApiResponse::STATUS_INFO, $content))->send();
        }
    }
