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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

return array(
    array(
        'key'         => 'news.rss_title',
        'value'       => 'Student IT',
        'description' => 'The title of the RSS feed',
    ),
    array(
        'key'   => 'news.rss_description',
        'value' => serialize(
            array(
                'nl' => 'RSS Feed van de Student IT',
                'en' => 'RSS Feed of the Student IT',
            )
        ),
        'description' => 'The description of the RSS feed',
    ),
    array(
        'key'         => 'news.rss_image_link',
        'value'       => '/_site/img/logo.png',
        'description' => 'The image of the RSS feed',
    ),
    array(
        'key'         => 'news.max_age_site',
        'value'       => 'P3M',
        'description' => 'The maximum age of news items shown on the homepage',
    ),
);
