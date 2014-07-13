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
 *
 * @license http://litus.cc/LICENSE
 */

namespace MailBundle\Form\Admin\Bakske;

use CommonBundle\Entity\General\AcademicYear;

/**
 * Send Mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Mail extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'select',
            'name'       => 'edition',
            'label'      => 'Edition',
            'required'   => true,
            'options'    => array(
                'options' => $this->_createEditionsArray(),
            ),
        ));

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'test',
            'label'      => 'Test Mail',
        ));

        $this->add(array(
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
        ));

        $this->addSubmit('Send', 'mail');
    }

    private function _createEditionsArray()
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

        if (empty($editions))
            throw new \RuntimeException('There needs to be at least one edition before you can mail it');

        $editionsArray = array();
        foreach ($editions as $edition)
            $editionsArray[$edition->getId()] = $edition->getTitle();

        return $editionsArray;
    }

    /**
     * @param  AcademicYear $academicYear
     * @return self
     */
    public function setAcademicYear(AcademicYear $academicYear)
    {
        $this->_academicYear = $academicYear;
        return $this;
    }

    /**
     * @return AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->_academicYear;
    }
}
