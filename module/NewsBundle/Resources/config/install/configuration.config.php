<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

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
