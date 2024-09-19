<?php

return array(
    'submenus' => array(
        'Publications' => array(
            'subtitle'    => array('Editions', 'Publications', 'Videos'),
            'items'       => array(
                'publication_admin_publication' => array('title' => 'Publications'),
                'publication_admin_video'       => array('title' => 'Videos'),
            ),
            'controllers' => array(
                'publication_admin_edition_html',
                'publication_admin_edition_pdf',
                'publication_admin_video',
            ),
        ),
    ),
);
