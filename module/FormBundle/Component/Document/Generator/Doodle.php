<?php

namespace FormBundle\Component\Document\Generator;

use Doctrine\ORM\EntityManager;
use FormBundle\Entity\ViewerMap;

/**
 * Doodle
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Doodle extends \CommonBundle\Component\Document\Generator\Csv
{
    /**
     * @param EntityManager $entityManager
     * @param ViewerMap     $viewerMap
     */
    public function __construct(EntityManager $entityManager, ViewerMap $viewerMap)
    {
        $headers = array('ID', 'Submitter', 'Submitted');
        if ($viewerMap->isMail()) {
            $headers[] = 'Email';
        }

        $entries = $entityManager
            ->getRepository('FormBundle\Entity\Node\Entry')
            ->findAllByForm($viewerMap->getForm());

        $maxSlots = 0;
        $results = array();
        foreach ($entries as $entry) {
            $result = array($entry->getId(), $entry->getPersonInfo()->getFullName(), $entry->getCreationTime()->format('d/m/Y H:i'));
            if ($viewerMap->isMail()) {
                $result[] = $entry->getPersonInfo()->getEmail();
            }

            $maxSlots = max(count($entry->getFieldEntries()), $maxSlots);
            foreach ($entry->getFieldEntries() as $fieldEntry) {
                $result[] = $fieldEntry->getField()->getStartDate()->format('d/m/Y H:i');
                $result[] = $fieldEntry->getField()->getEndDate()->format('d/m/Y H:i');
            }
            $results[] = $result;
        }

        for ($i = 0; $i < $maxSlots; $i++) {
            $headers[] = 'Slot ' . ($i + 1) . ' Start';
            $headers[] = 'Slot ' . ($i + 1) . ' End';
        }

        parent::__construct($headers, $results);
    }
}
