<?php

namespace CudiBundle\Component\Socket\Sale;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Barcode\Ean12;
use CommonBundle\Entity\User\Person\Academic;
use CudiBundle\Entity\Sale\QueueItem as QueueItemEntity;
use Doctrine\ORM\EntityManager;

class Printer
{
    /**
     * @param EntityManager   $entityManager
     * @param string          $printer
     * @param QueueItemEntity $queueItem
     * @param array           $bookings
     */
    public static function signInTicket(EntityManager $entityManager, $printer, QueueItemEntity $queueItem, $bookings)
    {
        $academic = $queueItem->getPerson();
        if (!($academic instanceof Academic)) {
            return;
        }

        $printCollect = (int) $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.print_collect_as_signin');
        if ($printCollect === 1) {
            return self::collectTicket($entityManager, $printer, $queueItem, $bookings);
        }

        $articles = array();
        $totalPrice = 0;
        foreach ($bookings as $booking) {
            $articles[] = array(
                'title'   => $booking->getArticle()->getMainArticle()->getTitle(),
                'price'   => (string) number_format($booking->getArticle()->getSellPrice() / 100, 2),
                'barcode' => substr($booking->getArticle()->getBarcode(), 7),
                'number'  => $booking->getNumber(),
            );
            $totalPrice += $booking->getArticle()->getSellPrice() * $booking->getNumber();
        }

        $data = array(
            'id'      => $academic->getUniversityIdentification(),
            'barcode' => (int) $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.queue_item_barcode_prefix') + $queueItem->getId(),
            'name'        => $academic->getFullName(),
            'queuenumber' => $queueItem->getQueueNumber(),
            'totalAmount' => (string) number_format($totalPrice / 100, 2),
            'items'       => $articles,
            'type'        => 1,
        );

        self::doPrint($entityManager, $printer, $data);
    }

    /**
     * @param EntityManager   $entityManager
     * @param string          $printer
     * @param QueueItemEntity $queueItem
     * @param array           $bookings
     */
    public static function collectTicket(EntityManager $entityManager, $printer, QueueItemEntity $queueItem, $bookings)
    {
        $academic = $queueItem->getPerson();
        if (!($academic instanceof Academic)) {
            return;
        }

        $articles = array();
        $totalPrice = 0;
        foreach ($bookings as $booking) {
            $articles[] = array(
                'title'   => $booking->getArticle()->getMainArticle()->getTitle(),
                'price'   => (string) number_format($booking->getArticle()->getSellPrice() / 100, 2),
                'barcode' => substr($booking->getArticle()->getBarcode(), 7),
                'number'  => $booking->getNumber(),
            );
            $totalPrice += $booking->getArticle()->getSellPrice() * $booking->getNumber();
        }

        $data = array(
            'id'      => $academic->getUniversityIdentification(),
            'barcode' => (int) $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.queue_item_barcode_prefix') + $queueItem->getId(),
            'name'        => $academic->getFullName(),
            'queuenumber' => $queueItem->getQueueNumber(),
            'totalAmount' => (string) number_format($totalPrice / 100, 2),
            'items'       => $articles,
            'type'        => 2,
        );

        self::doPrint($entityManager, $printer, $data);
    }

    /**
     * @param EntityManager   $entityManager
     * @param string          $printer
     * @param QueueItemEntity $queueItem
     * @param array           $saleItems
     */
    public static function saleTicket(EntityManager $entityManager, $printer, QueueItemEntity $queueItem, $saleItems)
    {
        $academic = $queueItem->getPerson();
        if (!($academic instanceof Academic)) {
            return;
        }

        $articles = array();
        $totalPrice = 0;
        foreach ($saleItems as $saleItem) {
            $articles[] = array(
                'title'   => $saleItem->getArticle()->getMainArticle()->getTitle(),
                'price'   => (string) number_format($saleItem->getPrice() / 100, 2),
                'barcode' => substr($saleItem->getArticle()->getBarcode(), 7),
                'number'  => $saleItem->getNumber(),
            );
            $totalPrice += $saleItem->getPrice();
        }

        $data = array(
            'id'      => $academic->getUniversityIdentification(),
            'barcode' => (int) $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.queue_item_barcode_prefix') + $queueItem->getId(),
            'name'        => $academic->getFullName(),
            'queuenumber' => $queueItem->getQueueNumber(),
            'totalAmount' => (string) number_format($totalPrice / 100, 2),
            'items'       => $articles,
            'type'        => 3,
        );

        self::doPrint($entityManager, $printer, $data);
    }

    /**
     * @param EntityManager $entityManager
     * @param string        $printer
     * @param Person        $person
     */
    public static function membershipCard(EntityManager $entityManager, $printer, Academic $academic, AcademicYear $year)
    {
        if (!($academic instanceof Academic)) {
            return;
        }

        $organization = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('organization_short_name');

        $comment = $organization . ' ' . $year->getCode();

        $barcode = $entityManager
            ->getRepository('CommonBundle\Entity\User\Barcode')
            ->findEan12ByPerson($academic);

        if ($barcode === null) {
            $barcode = new Ean12($academic, Ean12::generate($entityManager));
            $entityManager->persist($barcode);
        }

        $data = array(
            'id'        => $academic->getUsername(),
            'barcode'   => $barcode->getPrintableBarcode(),
            'firstName' => $academic->getFirstName(),
            'lastName'  => $academic->getLastName(),
            'comment'   => $comment,
            'type'      => 4,
        );

        $entityManager->flush();
        self::doPrint($entityManager, $printer, $data);
    }

    /**
     * @param EntityManager $entityManager
     * @param string        $printer
     * @param array         $data
     */
    private static function doPrint(EntityManager $entityManager, $printer, array $data)
    {
        $enablePrinters = $entityManager->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_printers');

        if (!$enablePrinters) {
            return;
        }

        $printers = unserialize(
            $entityManager->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.printers')
        );

        if (!isset($printers[$printer])) {
            return;
        }

        $data['title'] = $entityManager->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.ticket_title');

        $data = json_encode(
            (object) array(
                'command' => 'PRINT',
                'id'      => $printers[$printer],
                'ticket'  => $data,
                'key'     => $entityManager->getRepository('CommonBundle\Entity\General\Config')
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
