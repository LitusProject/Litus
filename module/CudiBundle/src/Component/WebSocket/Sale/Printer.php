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

namespace CudiBundle\Component\WebSocket\Sale;

use Doctrine\ORM\EntityManager;

class Printer {
    public static function queuePrint(EntityManager $entityManger, $printer, $identification, $barcode, $queueNum, $totalPrice, $articles, $prices)
    {
        if (sizeof($articles) != sizeof($prices))
            return;

        $data = $identification . '##' . $barcode . '##' . $queueNum . '##' . number_format($totalPrice, 2) . '#';
        foreach($articles as $article)
            $data .= '#' . $article;
        $data .= '#';
        foreach($prices as $price)
            $data .= '#' . number_format($price, 2);
        self::_print($entityManger, $printer, $printer == 'collect' ? 2 : 1, $data);
    }

    public static function salePrint(EntityManager $entityManger, $printer, $identification, $barcode, $queueNum, $totalPrice, $articles, $prices)
    {
        if (sizeof($articles) != sizeof($prices))
            return;

        $data = $identification . '##' . $barcode . '##' . $queueNum . '##' . number_format($totalPrice, 2) . '#';
        foreach($articles as $article)
            $data .= '#' . $article;
        $data .= '#';
        foreach($prices as $price)
            $data .= '#' . number_format($price, 2);
        self::_print($entityManger, $printer, 2, $data);
    }

    private static function _print(EntityManager $entityManger, $printer, $type, $data)
    {
        $printers = unserialize(
            $entityManger->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.printers')
        );

        if (!isset($printers[$printer]))
            return;

        $data = 'PRINT ' . $printers[$printer] . ' ' . $type . ' ' . $data;
        $now = new DateTime();
        echo '[' . $now->format('d/m/Y H:i:s') . ']:' . $data . PHP_EOL;
        $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
        socket_connect(
            $socket,
            $entityManger->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.print_socket_address'),
            $entityManger->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.print_socket_port')
        );
        socket_write($socket, $data);
        socket_close($socket);
    }
}