<?php

namespace BrBundle\Repository\Event;

use BrBundle\Entity\Event;

/**
 * Subscription
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Subscription extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findAllByEventQuery(Event $event)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('s')
            ->from('BrBundle\Entity\Event\Subscription', 's')
            ->where(
                $query->expr()->eq('s.event', ':event')
            )
            ->setParameter('event', $event->getId())
            ->getQuery();
    }

    public function findOneByQREvent(Event $event, string $qrCode)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('s')
            ->from('BrBundle\Entity\Event\Subscription', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('s.event', ':event'),
                    $query->expr()->eq('s.qrCode', ':qr_code')
                )
            )
            ->setParameter('qr_code', $qrCode)
            ->setParameter('event', $event->getId())
            ->getQuery()
            ->getResult();
    }

    public function findAllByEventAndNameSearchQuery(Event $event, string $name)
    {
        $query = $this->getEntityManager()->createQueryBuilder();

        return $query->select('s')
            ->from('BrBundle\Entity\Event\Subscription', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('s.event', ':event'),
                    $query->expr()->orX(
                        $query->expr()->like(
                            $query->expr()->concat(
                                $query->expr()->lower($query->expr()->concat('s.firstName', "' '")),
                                $query->expr()->lower('s.lastName')
                            ),
                            ':name'
                        ),
                        $query->expr()->like(
                            $query->expr()->concat(
                                $query->expr()->lower($query->expr()->concat('s.lastName', "' '")),
                                $query->expr()->lower('s.firstName')
                            ),
                            ':name'
                        )
                    ),
                )
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->setParameter('event', $event->getId())
            ->getQuery();
    }
}