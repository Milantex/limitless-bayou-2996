<?php
	/**
	 * Database connection parameters
	 */
	define('DATABASE_CONNECTIONS', array(
		'main' => array(
			'DB_CONN' => 'mysql:hostname=localhost;dbname=bayou;charset=utf8',
			'DB_USER' => 'root',
			'DB_PASS' => ''
		),
		'remote' => array(
			'DB_CONN' => 'mysql:hostname=db5.secret-domain.com;dbname=bayou_main;charset=utf8',
			'DB_USER' => 'bayou_user',
			'DB_PASS' => '1234567890'
		)
	));

	define('DEFAULT_DATABASE_CONNECTION', 'main');
