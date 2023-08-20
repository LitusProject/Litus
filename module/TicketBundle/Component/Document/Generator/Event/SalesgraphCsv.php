<?php

namespace TicketBundle\Component\Document\Generator\Event;

use DateTime;
use Doctrine\ORM\EntityManager;

/**
 * Salesgraph Csv
 */
class SalesgraphCsv extends \CommonBundle\Component\Document\Generator\Csv
{
    /**
     * @param EntityManager $entityManager
     * @param array         $salesGraphData
     */
    public function __construct(EntityManager $entityManager, array $salesGraphData)
    {
        $headers = array('Timestamp', 'Amount Sold');

        $labels = $salesGraphData['labels'];
        $data = $salesGraphData['dataset'];

        $result = array();
        for ($i = 0; $i < sizeof($labels); $i++) {
            $result[] = array(
                date('d/m/Y H:i', $labels[$i]),
                $data[$i],
            );
        }

        parent::__construct($headers, $result);
    }
}
