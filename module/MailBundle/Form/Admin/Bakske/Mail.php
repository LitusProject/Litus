<?php

namespace MailBundle\Form\Admin\Bakske;

use CommonBundle\Entity\General\AcademicYear;
use RuntimeException;

/**
 * Send Mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Mail extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var AcademicYear
     */
    private $academicYear;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'select',
                'name'     => 'edition',
                'label'    => 'Edition',
                'required' => true,
                'options'  => array(
                    'options' => $this->createEditionsArray(),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'test',
                'label' => 'Test Mail',
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'subject',
                'label'      => 'Subject',
                'required'   => true,
                'attributes' => array(
                    'style' => 'width: 400px;',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Send', 'mail');
    }

    private function createEditionsArray()
    {
        $publicationId = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('publication.bakske_id');

        $publication = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Publication')
            ->findOneById($publicationId);

        $editions = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Edition\Html')
            ->findAllByPublicationAndAcademicYear($publication, $this->getAcademicYear());

        if (count($editions) == 0) {
            throw new RuntimeException('There needs to be at least one edition before you can mail it');
        }

        $editionsArray = array();
        foreach ($editions as $edition) {
            $editionsArray[$edition->getId()] = $edition->getTitle();
        }

        return $editionsArray;
    }

    /**
     * @param  AcademicYear $academicYear
     * @return self
     */
    public function setAcademicYear(AcademicYear $academicYear)
    {
        $this->academicYear = $academicYear;

        return $this;
    }

    /**
     * @return AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }
}
