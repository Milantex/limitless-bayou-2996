<?php
    $map = new ApiMap('blog.user', 'user', 'Blog users');

    $map->addField('user_id', new NumericApiMapField('User ID', TRUE, 1));
    $map->addField('created_at', new DateTimeApiMapField('Date of creation'));
    $map->addField('username', new TextualApiMapField('Username', '/^[a-z0-9_\-\.]{5,64}$/'));
    $map->addField('password', new TextualApiMapField('Base64 of password SHA512 hash', '/^[A-z0-9\+=\/]{88}$/'));
    $map->addField('active', new NumericApiMapField('User status', TRUE, 0, 1));

    $map->setIdentifier('user_id');

    return $map;
