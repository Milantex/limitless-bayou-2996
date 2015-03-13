<?php
    ApiMaps::addMap('blog.user', 'Blog users');
    ApiMaps::getMap('blog.user')->addField('user_id', new NumericApiMapField('User ID', TRUE, 1));
    ApiMaps::getMap('blog.user')->addField('created_at', new DateTimeApiMapField('Date of creation'));
    ApiMaps::getMap('blog.user')->addField('username', new TextualApiMapField('Username', '/^[a-z0-9_\-\.]{5,64}$/'));
    ApiMaps::getMap('blog.user')->addField('password', new TextualApiMapField('Base64 of password SHA512 hash', '/^[A-z0-9\+=\/]{88}$/'));
    ApiMaps::getMap('blog.user')->addField('active', new NumericApiMapField('User status', TRUE, 0, 1));
    ApiMaps::getMap('blog.user')->setIdentifier('user_id');

    ApiMaps::addMap('blog.post', 'Blog posts');
    ApiMaps::getMap('blog.post')->addField('post_id', new NumericApiMapField('Post ID', TRUE, 1));
    ApiMaps::getMap('blog.post')->addField('created_at', new DateTimeApiMapField('Date of creation'));
    ApiMaps::getMap('blog.post')->addField('user_id', new NumericApiMapField('User (Post author) ID', TRUE, 1));
    ApiMaps::getMap('blog.post')->addField('title', new TextualApiMapField('Post title', '/^.{128}$/'));
    ApiMaps::getMap('blog.post')->addField('link', new TextualApiMapField('Post SEO link', '/^.{128}$/'));
    ApiMaps::getMap('blog.post')->addField('content', new TextualApiMapField('Post content'));
    ApiMaps::getMap('blog.post')->addField('visible', new NumericApiMapField('Post visibility', TRUE, 0, 1));
