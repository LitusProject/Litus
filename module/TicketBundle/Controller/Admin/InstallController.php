<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace TicketBundle\Controller\Admin;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig()
    {
        $this->installConfig(
            array(
            )
        );
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'ticketbundle' => array(
                    'ticket_admin_event' => array(
                        'add', 'delete', 'edit', 'manage', 'old'
                    ),
                    'ticket_admin_ticket' => array(
                        'manage'
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
                        'event'
                    ),
                )
            )
        );
    }
}
