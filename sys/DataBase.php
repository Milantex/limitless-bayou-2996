<?php
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
         * Returns an instance of the PDO object for a particular configured
         * database connection. If the connection has not yet been made it opens
         * a new connection and stores it for later reuse.
         * @param string $connection The name of the connection configuration
         * @return PDO
         */
        public static function getInstance($connection = DEFAULT_DATABASE_CONNECTION) : PDO {
            if (!isset(static::$connections[$connection])) {
                static::$connections[$connection] = new PDO(DATABASE_CONNECTIONS[$connection]['DB_CONN'], DATABASE_CONNECTIONS[$connection]['DB_USER'], DATABASE_CONNECTIONS[$connection]['DB_PASS']);
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
                return $prep->fetch(PDO::FETCH_OBJ);
            } else {
                return $prep->fetchAll(PDO::FETCH_OBJ);
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

            return $prep->execute($parameters);
        }
    }
