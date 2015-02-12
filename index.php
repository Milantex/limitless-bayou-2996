<?php
	require_once './app/Configuration.php';
	require_once './sys/DataBase.php';

	# DataBase::execute('INSERT INTO post (user_id, title, link, content) VALUES (?, ?, ?, ?);', [1, 'Another post in the database', 'another-post-in-the-database', 'This is the content of the second post in the Limitless Bayou API project.']);

	$data = DataBase::selectMany('SELECT * FROM post ORDER BY created_at DESC LIMIT 0, 20;');
	print_r($data);
	exit;

	ob_clean();
	header('Content-type: text/json; charset=utf-8');
	$data = [
		'timestamp' => date('r'),
		'reference' => rand(1000000, 9999999),
		'ipaddress' => $_SERVER['REMOTE_ADDR']
	];
	echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
	exit();
