<?php

return array(
    array(
        'key'         => 'news.rss_title',
        'value'       => 'Vlaamse Technische Kring',
        'description' => 'The title of the RSS feed',
    ),
    array(
        'key'         => 'news.rss_description',
        'value'       => serialize(
            array(
                'nl' => 'RSS Feed van de Vlaamse Technische Kring',
                'en' => 'RSS Feed of the Vlaamse Technische Kring',
            )
        ),
        'description' => 'The description of the RSS feed',
    ),
    array(
        'key'         => 'news.rss_image_link',
        'value'       => '/_site/img/logo.png',
        'description' => 'The image of the RSS feed',
    ),
);
