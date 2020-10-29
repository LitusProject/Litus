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

use CommonBundle\Component\Util\AcademicYear as AcademicYearUtil;
use CommonBundle\Entity\General\Language;
use Doctrine\ORM\EntityManager;
use FormBundle\Entity\ViewerMap;

/**
 * Form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Form extends \CommonBundle\Component\Document\Generator\Csv
{
    /**
     * @param EntityManager $entityManager
     * @param ViewerMap     $viewerMap
     * @param Language      $language
     */
    public function __construct(EntityManager $entityManager, ViewerMap $viewerMap, Language $language)
    {
        $headers = array('ID', 'Submitter', 'Submitted', 'Username', 'Organization status');
        if ($viewerMap->isMail()) {
            $headers[] = 'Email';
        }

        $fields = $viewerMap->getForm()->getFields();
        foreach ($fields as $field) {
            $headers[] = $field->getLabel($language);
        }

        $entries = $entityManager
            ->getRepository('FormBundle\Entity\Node\Entry')
            ->findAllByForm($viewerMap->getForm());

        $academicYear = AcademicYearUtil::getOrganizationYear($entityManager, null);

        $results = array();
        foreach ($entries as $entry) {
            $result = array($entry->getId(), $entry->getPersonInfo()->getFirstName() . ' ' . $entry->getPersonInfo()->getLastName(), $entry->getCreationTime()->format('d/m/Y H:i'));

            $result[] = $entry->getPersonInfo()->getUsername();

            if (!($status = $entry->getPersonInfo()->getOrganizationStatus($academicYear))) {
                $status = '';
            }


            if (!is_string($status)) {
                $status = $status->getStatus();
            }
            $result[] = $status;

            if ($viewerMap->isMail()) {
                $result[] = $entry->getPersonInfo()->getEmail();
            }

            foreach ($fields as $field) {
                $fieldEntry = $entityManager
                    ->getRepository('FormBundle\Entity\Entry')
                    ->findOneByFormEntryAndField($entry, $field);
                if ($fieldEntry) {
                    $result[] = $fieldEntry->getValueString($language);
                } else {
                    $result[] = '';
                }
            }
            $results[] = $result;
        }

        parent::__construct($headers, $results);
    }
}
