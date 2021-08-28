<?php

namespace ApiBundle\Controller;

use DateInterval;
use DateTime;
use Laminas\View\Model\ViewModel;

/**
 * ConfigController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ConfigController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function entriesAction()
    {
        $this->initJson();

        $entries = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->findByPublished(true);

        $result = array();
        foreach ($entries as $entry) {
            try {
                $now = new DateTime();

                $nowWithInterval = clone $now;
                $nowWithInterval->add(new DateInterval($entry->getValue()));

                $value = $nowWithInterval->getTimestamp() - $now->getTimestamp();
            } catch (\Throwable $e) {
                try {
                    $value = unserialize($entry->getValue());
                } catch (\Throwable $e) {
                    $value = $entry->getValue();
                }
            }

            $result[$entry->getKey()] = $value;
        }

        return new ViewModel(
            array(
                'result' => (object) $result,
            )
        );
    }
}
