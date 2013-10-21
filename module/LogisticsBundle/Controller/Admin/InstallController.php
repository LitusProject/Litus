<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace LogisticsBundle\Controller\Admin;

use Exception,
    LogisticsBundle\Entity\Reservation\VanReservation,
    LogisticsBundle\Entity\Reservation\PianoReservation,
    LogisticsBundle\Entity\Reservation\ReservableResource;

/**
 * InstallController for the LogisticsBundle
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig()
    {
        $this->installConfig(
            array(
                array(
                    'key'         => 'logistics.piano_auto_confirm_deadline',
                    'value'       => 'P1D',
                    'description' => 'The deadline for auto confirm a piano reservation',
                ),
                array(
                    'key'         => 'logistics.piano_time_slot_duration',
                    'value'       => '30',
                    'description' => 'Duration of one time slot for a piano reservation in minutes',
                ),
                array(
                    'key'         => 'logistics.piano_time_slot_max_duration',
                    'value'       => '90',
                    'description' => 'Maximum duration of one time slot for a piano reservation in minutes',
                ),
                array(
                    'key'         => 'logistics.piano_reservation_max_in_advance',
                    'value'       => 'P30D',
                    'description' => 'Maximum days a reservation is possible in advance',
                ),
                array(
                    'key'         => 'logistics.piano_time_slots',
                    'value'       => serialize(
                        array(
                            '1' => array(
                                array('start' => '19:00', 'end' => '22:00')
                            ), // Monday
                            '2' => null, // Tuesday
                            '3' => null, // Wednesday
                            '4' => array(
                                array('start' => '19:00', 'end' => '22:00')
                            ), // Thursday
                            '5' => null, // Friday
                            '6' => null, // Saturday
                            '7' => null, // Sunday
                        )
                    ),
                    'description' => 'Available time slots for a piano reservation',
                ),
                array(
                    'key'         => 'logistics.piano_mail_to',
                    'value'       => 'vice@vtk.be',
                    'description' => 'The mail address piano reservation mails are send to',
                ),
                array(
                    'key'         => 'logistics.piano_new_reservation',
                    'value'       => serialize(
                        array(
                            'en' => array(
                                'subject' => 'New Piano Reservation',
                                'content' => 'Dear,

A new piano reservation was made:
{{ name }} from {{ start }} until {{ end }}.

VTK

-- This is an automatically generated email, please do not reply --'
                            ),
                        )
                    ),
                    'description' => 'The mail sent when a new piano reservation is created'
                ),
                array(
                    'key'         => 'logistics.piano_new_reservation_confirmed',
                    'value'       => serialize(
                        array(
                            'en' => array(
                                'subject' => 'New Piano Reservation',
                                'content' => 'Dear,

A new piano reservation was made and confirmed:
{{ name }} from {{ start }} until {{ end }}.

VTK

-- This is an automatically generated email, please do not reply --'
                            ),
                        )
                    ),
                    'description' => 'The mail sent when a new piano reservation is created and confirmed'
                ),
                array(
                    'key'         => 'logistics.icalendar_uid_suffix',
                    'value'       => 'logistics.vtk.be',
                    'description' => 'The suffix of an iCalendar event uid',
                ),
            )
        );

        $this->_installResources();
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'logisticsbundle' => array(
                    'logistics_admin_driver' => array(
                        'add', 'delete', 'edit', 'manage'
                    ),
                    'logistics_admin_piano_reservation' => array(
                        'add', 'delete', 'edit', 'manage', 'old'
                    ),
                    'logistics_admin_van_reservation' => array(
                        'add', 'delete', 'edit', 'manage', 'old'
                    ),
                    'logistics_admin_lease' => array(
                        'add', 'delete', 'edit', 'manage'
                    ),
                    'logistics_index' => array(
                        'add', 'delete', 'edit', 'export', 'fetch', 'index', 'move'
                    ),
                    'logistics_lease' => array(
                        'availabilityCheck', 'history', 'index', 'show', 'typeahead'
                    ),
                    'logistics_auth' => array(
                        'login', 'logout', 'shibboleth',
                    ),
                    'logistics_piano' => array(
                        'index'
                    ),
                )
            )
        );

        $this->installRoles(
            array(
                'guest' => array(
                    'system' => true,
                    'parents' => array(
                    ),
                    'actions' => array(
                        'logistics_index' => array(
                            'fetch', 'index'
                        ),
                        'logistics_auth' => array(
                            'login', 'logout', 'shibboleth',
                        ),
                    ),
                ),
            )
        );
    }

    private function _installResources()
    {
        $resources = array(VanReservation::VAN_RESOURCE_NAME, PianoReservation::PIANO_RESOURCE_NAME);

        foreach($resources as $name) {
            $resource = $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Reservation\ReservableResource')
                ->findOneByName($name);

            if (null == $resource) {
                $this->getEntityManager()->persist(new ReservableResource($name));
            }
        }
    }
}
