<?php
    $map = new ApiMap('blog.post', 'Blog posts');

    $map->addField('post_id', new NumericApiMapField('Post ID', TRUE, 1));
    $map->addField('created_at', new DateTimeApiMapField('Date of creation'));
    $map->addField('user_id', new NumericApiMapField('User (Post author) ID', TRUE, 1));
    $map->addField('title', new TextualApiMapField('Post title', '/^.{128}$/'));
    $map->addField('link', new TextualApiMapField('Post SEO link', '/^.{128}$/'));
    $map->addField('content', new TextualApiMapField('Post content'));
    $map->addField('visible', new NumericApiMapField('Post visibility', TRUE, 0, 1));

    $map->setIdentifier('post_id');

    return $map;
