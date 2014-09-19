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

namespace CudiBundle\Component\WebSocket\Sale;

use CommonBundle\Entity\User\Person\Academic,
    CudiBundle\Entity\Sale\QueueItem as EntityQueueItem,
    Doctrine\ORM\EntityManager;

class Printer
{
    /**
     * @param EntityManager   $entityManager
     * @param string          $printer
     * @param EntityQueueItem $queueItem
     * @param array           $bookings
     */
    public static function signInTicket(EntityManager $entityManager, $printer, EntityQueueItem $queueItem, $bookings)
    {
        $academic = $queueItem->getPerson();
        if (!($academic instanceof Academic))
            return;

        $printCollect = (int) $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.print_collect_as_signin');
        if (1 === $printCollect) {
            return self::collectTicket($entityManager, $printer, $queueItem, $bookings);
        }

        $articles = array();
        $totalPrice = 0;
        foreach ($bookings as $booking) {
            $articles[] = array(
                'title' => $booking->getArticle()->getMainArticle()->getTitle(),
                'price' => (string) number_format($booking->getArticle()->getSellPrice() / 100, 2),
                'barcode' => substr($booking->getArticle()->getBarcode(), 7),
                'number' => $booking->getNumber(),
            );
            $totalPrice += $booking->getArticle()->getSellPrice() * $booking->getNumber();
        }

        $data = array(
            'id' => $academic->getUniversityIdentification(),
            'barcode' => (int) $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.queue_item_barcode_prefix') + $queueItem->getId(),
            'name' => $academic->getFullName(),
            'queuenumber' => $queueItem->getQueueNumber(),
            'totalAmount' => (string) number_format($totalPrice / 100, 2),
            'items' => $articles,
            'type' => 1,
        );

        self::_print($entityManager, $printer, $data);
    }

    /**
     * @param EntityManager   $entityManager
     * @param string          $printer
     * @param EntityQueueItem $queueItem
     * @param array           $bookings
     */
    public static function collectTicket(EntityManager $entityManager, $printer, EntityQueueItem $queueItem, $bookings)
    {
        $academic = $queueItem->getPerson();
        if (!($academic instanceof Academic))
            return;

        $articles = array();
        $totalPrice = 0;
        foreach ($bookings as $booking) {
            $articles[] = array(
                'title' => $booking->getArticle()->getMainArticle()->getTitle(),
                'price' => (string) number_format($booking->getArticle()->getSellPrice() / 100, 2),
                'barcode' => substr($booking->getArticle()->getBarcode(), 7),
                'number' => $booking->getNumber(),
            );
            $totalPrice += $booking->getArticle()->getSellPrice() * $booking->getNumber();
        }

        $data = array(
            'id' => $academic->getUniversityIdentification(),
            'barcode' => (int) $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.queue_item_barcode_prefix') + $queueItem->getId(),
            'name' => $academic->getFullName(),
            'queuenumber' => $queueItem->getQueueNumber(),
            'totalAmount' => (string) number_format($totalPrice / 100, 2),
            'items' => $articles,
            'type' => 2,
        );

        self::_print($entityManager, $printer, $data);
    }

    /**
     * @param EntityManager   $entityManager
     * @param string          $printer
     * @param EntityQueueItem $queueItem
     * @param array           $saleItems
     */
    public static function saleTicket(EntityManager $entityManager, $printer, EntityQueueItem $queueItem, $saleItems)
    {
        $academic = $queueItem->getPerson();
        if (!($academic instanceof Academic))
            return;

        $articles = array();
        $totalPrice = 0;
        foreach ($saleItems as $saleItem) {
            $articles[] = array(
                'title' => $saleItem->getArticle()->getMainArticle()->getTitle(),
                'price' => (string) number_format($saleItem->getPrice() / 100, 2),
                'barcode' => substr($saleItem->getArticle()->getBarcode(), 7),
                'number' => $saleItem->getNumber(),
            );
            $totalPrice += $saleItem->getPrice();
        }

        $data = array(
            'id' => $academic->getUniversityIdentification(),
            'barcode' => (int) $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.queue_item_barcode_prefix') + $queueItem->getId(),
            'name' => $academic->getFullName(),
            'queuenumber' => $queueItem->getQueueNumber(),
            'totalAmount' => (string) number_format($totalPrice / 100, 2),
            'items' => $articles,
            'type' => 3,
        );

        self::_print($entityManager, $printer, $data);
    }

    /**
     * @param EntityManager $entityManager
     * @param string        $printer
     * @param array         $data
     */
    private static function _print(EntityManager $entityManager, $printer, array $data)
    {
        $enablePrinters = $entityManager->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_printers');

        if (!$enablePrinters)
            return;

        $printers = unserialize(
            $entityManager->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.printers')
        );

        if (!isset($printers[$printer]))
            return;

        $data['title'] = $entityManager->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.ticket_title');

        $data = json_encode(
            (object) array(
                'command' => 'PRINT',
                'id' => $printers[$printer],
                'ticket' => $data,
                'key' => $entityManager->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.printer_socket_key'),
            )
        );

        $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
        @socket_connect(
            $socket,
            $entityManager->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.print_socket_address'),
            $entityManager->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.print_socket_port')
        );
        @socket_write($socket, $data);
        socket_close($socket);
    }
}
