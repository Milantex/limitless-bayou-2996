<?php
	final class DataBase {
		private static $connections = array();

		public static function getInstance($connection = DEFAULT_DATABASE_CONNECTION) {
			if (!isset(static::$connections[$connection])) {
				static::$connections[$connection] = new PDO(DATABASE_CONNECTIONS[$connection]['DB_CONN'], DATABASE_CONNECTIONS[$connection]['DB_USER'], DATABASE_CONNECTIONS[$connection]['DB_PASS']);
			}

			return static::$connections[$connection];
		}

		private static function select($sql, $parameters = [], $connection = DEFAULT_DATABASE_CONNECTION, $return_one) {
			$con = static::getInstance($connection);
			if (!$con) {
				return ($return_one == TRUE)?NULL:array();
			}

			$prep = $con->prepare($sql);
			if (!$prep) {
				return ($return_one == TRUE)?NULL:array();
			}

			$res = $prep->execute($parameters);
			if (!$res) {
				return ($return_one == TRUE)?NULL:array();
			}

			if ($return_one == TRUE) {
				return $prep->fetch(PDO::FETCH_OBJ);
			} else {
				return $prep->fetchAll(PDO::FETCH_OBJ);
			}
		}

		public static function selectOne($sql, $parameters = [], $connection = DEFAULT_DATABASE_CONNECTION) {
			return static::select($sql, $parameters, $connection, TRUE);
		}

		public static function selectMany($sql, $parameters = [], $connection = DEFAULT_DATABASE_CONNECTION) {
			return static::select($sql, $parameters, $connection, FALSE);
		}

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
