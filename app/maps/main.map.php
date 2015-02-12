<?php
	ApiMaps::addMap('blog.user', 'Blog users');
	ApiMaps::getMap('blog.user')->addField('user_id', new NumericApiMapField('User ID', TRUE, 1));
	ApiMaps::getMap('blog.user')->addField('created_at', new DateTimeApiMapField('Date of creation'));
	ApiMaps::getMap('blog.user')->addField('username', new TextualApiMapField('Username', '/^[a-z0-9_\-\.]{5,64}$/'));
	ApiMaps::getMap('blog.user')->addField('password', new TextualApiMapField('Base64 of password SHA512 hash', '/^[A-z0-9\+=\/]{88}$/'));
	ApiMaps::getMap('blog.user')->addField('active', new NumericApiMapField('User status', TRUE, 0, 1));
	ApiMaps::getMap('blog.user')->setIdentifier('user_id');
