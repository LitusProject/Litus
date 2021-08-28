<?php

return array(
    'submenus' => array(
        'Publications' => array(
            'subtitle'    => array('Editions', 'Publications'),
            'items'       => array(
                'publication_admin_publication' => array('title' => 'Publications'),
            ),
            'controllers' => array(
                'publication_admin_edition_html',
                'publication_admin_edition_pdf',
            ),
        ),
    ),
);
