<?php
    namespace Milantex\LimitlessBayou\Sys;

    /**
     * The DataBase interface wrapper class
     */
    final class DataBase {
        /**
         * The list of open connections to different configured databases.
         * @var array
         */
        private static $connections = [];

        /**
         * Stores the error recorded after the last execute method was ran
         * @var array
         */
        private static $lastExecutionError = [];

        /**
         * Stores the number of rows affected by the last execute method
         * @var array
         */
        private static $lastAffectedRowCount = [];

        /**
         * Returns an instance of the PDO object for a particular configured
         * database connection. If the connection has not yet been made it opens
         * a new connection and stores it for later reuse.
         * @param string $connection The name of the connection configuration
         * @return PDO
         */
        public static function getInstance($connection = DEFAULT_DATABASE_CONNECTION) : \PDO {
            if (!isset(static::$connections[$connection])) {
                static::$connections[$connection] = new \PDO(DATABASE_CONNECTIONS[$connection]['DB_CONN'], DATABASE_CONNECTIONS[$connection]['DB_USER'], DATABASE_CONNECTIONS[$connection]['DB_PASS']);
            }

            return static::$connections[$connection];
        }

        /**
         * Performs a SELECT SQL query with given parameters for the selected
         * connection and can be made to fetch a single result or all results.
         * @param string $sql
         * @param array $parameters
         * @param string $connection
         * @param bool $return_one
         * @return NULL|stdClass|array
         */
        private static function select($sql, $parameters = [], $connection = DEFAULT_DATABASE_CONNECTION, $return_one = TRUE) {
            $con = static::getInstance($connection);
            if (!$con) {
                return ($return_one == TRUE) ? NULL : [];
            }

            $prep = $con->prepare($sql);
            if (!$prep) {
                return ($return_one == TRUE) ? NULL : [];
            }

            $res = $prep->execute($parameters);
            if (!$res) {
                return ($return_one == TRUE) ? NULL : [];
            }

            if ($return_one == TRUE) {
                return $prep->fetch(\PDO::FETCH_OBJ);
            } else {
                return $prep->fetchAll(\PDO::FETCH_OBJ);
            }
        }

        /**
         * Executes the select method of this class specifying the expectation
         * of a single record return value.
         * @param string $sql
         * @param array $parameters
         * @param string $connection
         * @return NULL|stdClass
         */
        public static function selectOne($sql, $parameters = [], $connection = DEFAULT_DATABASE_CONNECTION) {
            return static::select($sql, $parameters, $connection, TRUE);
        }

        /**
         * Executes the select method of this class specifying the expectation
         * of an array of records being returned.
         * @param string $sql
         * @param array $parameters
         * @param string $connection
         * @return NULL|stdClass
         */
        public static function selectMany($sql, $parameters = [], $connection = DEFAULT_DATABASE_CONNECTION) {
            return static::select($sql, $parameters, $connection, FALSE);
        }

        /**
         * Performs an execution of an SQL statement for the selected connection
         * and returns the result of the execution for processing to the caller.
         * @param string $sql
         * @param array $parameters
         * @param string $connection
         * @return type
         */
        public static function execute($sql, $parameters = [], $connection = DEFAULT_DATABASE_CONNECTION) {
            $con = static::getInstance($connection);
            if (!$con) {
                return NULL;
            }

            $prep = $con->prepare($sql);
            if (!$prep) {
                return NULL;
            }

            $res = $prep->execute($parameters);
            if (!$res) {
                static::$lastExecutionError[$connection] = $prep->errorInfo();
                static::$lastAffectedRowCount[$connection] = NULL;
            } else {
                static::$lastExecutionError[$connection] = NULL;
                static::$lastAffectedRowCount[$connection] = $prep->rowCount();
            }

            return $res;
        }

        /**
         * Returns the error recorded after the last execute method failure.
         * One error can be retrieved once. After it is returned, it is reset.
         * @param string $connection
         * @return array|NULL
         */
        public static function getLastExecutionError($connection = DEFAULT_DATABASE_CONNECTION) {
            if (!isset(static::$lastExecutionError[$connection])) {
                static::$lastExecutionError[$connection] = NULL;
            }

            $error = static::$lastExecutionError[$connection];
            static::$lastExecutionError[$connection] = NULL;
            return $error;
        }

        /**
         * Returns the affected row count after the last execute method success.
         * This method returns NULL if there was an error or if the execute
         * method was never ran. It can return 0 if no rows were affected.
         * @param string $connection
         * @return int|NULL
         */
        public static function getLastExecutionAffectedRownCount($connection = DEFAULT_DATABASE_CONNECTION) {
            if (!isset(static::$lastAffectedRowCount[$connection])) {
                static::$lastAffectedRowCount[$connection] = NULL;
            }

            $error = static::$lastAffectedRowCount[$connection];
            static::$lastAffectedRowCount[$connection] = NULL;
            return $error;
        }
    }
