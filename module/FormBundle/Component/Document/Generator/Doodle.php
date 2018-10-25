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

namespace FormBundle\Component\Document\Generator;

use CommonBundle\Entity\General\Language;
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

            $maxSlots = max(sizeof($entry->getFieldEntries()), $maxSlots);
            foreach ($entry->getFieldEntries() as $fieldEntry) {
                $result[] = $fieldEntry->getField()->getStartDate()->format('d/m/Y H:i');
                $result[] = $fieldEntry->getField()->getEndDate()->format('d/m/Y H:i');
            }
            $results[] = $result;
        }

        for ($i = 0 ; $i < $maxSlots ; $i++) {
            $headers[] = 'Slot ' . ($i + 1) . ' Start';
            $headers[] = 'Slot ' . ($i + 1) . ' End';
        }

        parent::__construct($headers, $results);
    }
}
