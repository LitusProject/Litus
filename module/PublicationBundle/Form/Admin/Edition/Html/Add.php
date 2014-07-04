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

namespace PublicationBundle\Form\Admin\Edition\Html;

use CommonBundle\Component\Form\Admin\Element\File,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityManager,
    PublicationBundle\Component\Validator\Title\Edition\Html as TitleValidator,
    PublicationBundle\Entity\Publication,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * The form used to add a new Publication
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @var Publication The publication
     */
    private $_publication = null;

    /**
     * @var AcademicYear The current academic year
     */
    private $_academicYear = null;

    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param Publication     $publication   The publication to add an edition to.
     * @param AcademicYear    $academicYear  The current academic year.
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Publication $publication, AcademicYear $academicYear, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_publication = $publication;
        $this->_academicYear = $academicYear;

        $this->setAttribute('id', 'uploadFile');
        $this->setAttribute('enctype', 'multipart/form-data');

        $field = new Text('title');
        $field->setLabel('Title')
            ->setRequired(true);
        $this->add($field);

        $field = new Select('pdf_version');
        $field->setLabel('PDF Version')
            ->setAttribute('options', $this->getPDFEditions())
            ->setRequired();
        $this->add($field);

        $field = new Textarea('html');
        $field->setLabel('HTML')
            ->setAttribute('rows', 20)
            ->setRequired();
        $this->add($field);

        $field = new Text('date');
        $field->setLabel('Date')
            ->setAttribute('placeholder', 'dd/mm/yyyy')
            ->setAttribute('data-datepicker', true)
            ->setRequired();
        $this->add($field);

        $field = new File('file');
        $field->setLabel('Images Archive')
            ->setRequired();
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'html_add');
        $this->add($field);
    }

    public function getPDFEditions()
    {
        $pdfs = $this->_entityManager
            ->getRepository('PublicationBundle\Entity\Edition\Pdf')
            ->findAllByPublicationAndAcademicYear($this->_publication, $this->_academicYear);

        $options = array();
        foreach ($pdfs as $pdf) {
            $options[$pdf->getId()] = $pdf->getTitle();
        }

        return $options;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'title',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new TitleValidator($this->_entityManager, $this->_publication, $this->_academicYear)
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'date',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'date',
                            'options' => array(
                                'format' => 'd/m/Y',
                            ),
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'file',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'filefilessize',
                            'options' => array(
                                'max' => '30MB',
                            ),
                        ),
                        array(
                            'name' => 'fileextension',
                            'options' => array(
                                'extension' => 'zip',
                            ),
                        ),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
