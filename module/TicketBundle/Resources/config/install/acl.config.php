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
    'ticketbundle' => array(
        'ticket_admin_event' => array(
            'add', 'delete', 'edit', 'manage', 'old'
        ),
        'ticket_admin_ticket' => array(
            'export', 'manage', 'print', 'search'
        ),
        'ticket_sale_index' => array(
            'sale', 'validate'
        ),
        'ticket_sale_ticket' => array(
            'delete', 'overview', 'sale', 'unassign', 'undoSale'
        ),
        'ticket_sale_person' => array(
            'typeahead'
        ),
        'ticket' => array(
            'delete', 'event'
        ),
    ),
);
