<?php

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
            $result = array(
                $entry->getId(),
                $entry->getPersonInfo()->getFirstName() . ' ' . $entry->getPersonInfo()->getLastName(),
                $entry->getCreationTime()->format('d/m/Y H:i'),
            );

            $result[] = $entry->getPersonInfo()->getUsername();

            $status = $entry->getPersonInfo()->getOrganizationStatus($academicYear);
            if ($status === null) {
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
