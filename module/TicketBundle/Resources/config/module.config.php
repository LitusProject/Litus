<?php

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
                'activity'           => Component\Validator\Activity::class,
                'Activity'           => Component\Validator\Activity::class,
                'bookingsclosedate'  => Component\Validator\BookingsCloseDate::class,
                'bookingsCloseDate'  => Component\Validator\BookingsCloseDate::class,
                'bookingCloseDate'   => Component\Validator\BookingsCloseDate::class,
                'bookingCloseData'   => Component\Validator\BookingsCloseDate::class,
                'BookingsCloseDate'  => Component\Validator\BookingsCloseDate::class,
                'BookingCloseDate'   => Component\Validator\BookingsCloseDate::class,
                'BookingCloseData'   => Component\Validator\BookingsCloseDate::class,
                'numbertickets'      => Component\Validator\NumberTickets::class,
                'numberTickets'      => Component\Validator\NumberTickets::class,
                'NumberTickets'      => Component\Validator\NumberTickets::class,
                'NumberTicketsGuest' => Component\Validator\NumberTicketsGuest::class,
                'InvoiceId'          => Component\Validator\InvoiceId::class,
                'invoiceId'          => Component\Validator\InvoiceId::class,
                'invoiceid'          => Component\Validator\InvoiceId::class,
                'InvoiceBase'        => Component\Validator\InvoiceId::class,
                'invoiceBase'        => Component\Validator\InvoiceId::class,
                'invoicebase'        => Component\Validator\InvoiceId::class,
                'OrderId'            => Component\Validator\OrderId::class,
                'orderId'            => Component\Validator\OrderId::class,
                'orderid'            => Component\Validator\OrderId::class,
                'OrderBase'          => Component\Validator\OrderId::class,
                'orderBase'          => Component\Validator\OrderId::class,
                'orderbase'          => Component\Validator\OrderId::class,
                'payid'              => Component\Validator\PayId::class,
                'payId'              => Component\Validator\PayId::class,
                'PayId'              => Component\Validator\PayId::class,
                'UrlValid'           => Component\Validator\UrlValid::class,
            ),
        ),
    )
);
