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

namespace TicketBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('ticket', 'validator'),
        'has_layouts'       => true,
    ),
    array(
        'validators' => array(
            'aliases' => array(
                'activity'          => Component\Validator\Activity::class,
                'Activity'          => Component\Validator\Activity::class,
                'bookingsclosedate' => Component\Validator\BookingsCloseDate::class,
                'bookingsCloseDate' => Component\Validator\BookingsCloseDate::class,
                'bookingCloseDate' => Component\Validator\BookingsCloseDate::class,
                'bookingCloseData' => Component\Validator\BookingsCloseDate::class,
                'BookingsCloseDate' => Component\Validator\BookingsCloseDate::class,
                'BookingCloseDate' => Component\Validator\BookingsCloseDate::class,
                'BookingCloseData' => Component\Validator\BookingsCloseDate::class,
                'numbertickets'     => Component\Validator\NumberTickets::class,
                'numberTickets'     => Component\Validator\NumberTickets::class,
                'NumberTickets'     => Component\Validator\NumberTickets::class,
                'InvoiceId'     => Component\Validator\InvoiceId::class,
                'invoiceId'     => Component\Validator\InvoiceId::class,
                'invoiceid'     => Component\Validator\InvoiceId::class,
                'InvoiceBase'     => Component\Validator\InvoiceId::class,
                'invoiceBase'     => Component\Validator\InvoiceId::class,
                'invoicebase'     => Component\Validator\InvoiceId::class,
                'OrderId'     => Component\Validator\OrderId::class,
                'orderId'     => Component\Validator\OrderId::class,
                'orderid'     => Component\Validator\OrderId::class,
                'OrderBase'     => Component\Validator\OrderId::class,
                'orderBase'     => Component\Validator\OrderId::class,
                'orderbase'     => Component\Validator\OrderId::class,
            ),
        ),
    )
);
