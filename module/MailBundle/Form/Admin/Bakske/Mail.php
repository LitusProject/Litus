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

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit,
    Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter;

/**
 * Send Mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Mail extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var AcademicYear The current academic year
     */
    private $_academicYear;

    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param AcademicYear    $academicYear  The current academic year
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, AcademicYear $academicYear, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_academicYear = $academicYear;

        $field = new Select('edition');
        $field->setLabel('Edition')
            ->setAttribute('options', $this->_createEditionsArray())
            ->setRequired();
        $this->add($field);

        $field = new Checkbox('test');
        $field->setLabel('Test Mail');
        $this->add($field);

        $field = new Text('subject');
        $field->setLabel('Subject')
            ->setAttribute('style', 'width: 400px;')
            ->setRequired();
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Send')
            ->setAttribute('class', 'mail');
        $this->add($field);
    }

    private function _createEditionsArray()
    {
        $publicationId = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('publication.bakske_id');

        $publication = $this->_entityManager
            ->getRepository('PublicationBundle\Entity\Publication')
            ->findOneById($publicationId);

        $editions = $this->_entityManager
            ->getRepository('PublicationBundle\Entity\Edition\Html')
            ->findAllByPublicationAndAcademicYear($publication, $this->_academicYear);

        if (empty($editions)) {
            throw new \RuntimeException('There needs to be at least one edition before you can mail it');
        }

        $editionsArray = array();
        foreach ($editions as $edition) {
            $editionsArray[$edition->getId()] = $edition->getTitle();
        }

        return $editionsArray;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'subject',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
