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

namespace BannerBundle\Form\Admin\Banner;

use CommonBundle\Component\OldForm\Admin\Element\Checkbox,
    CommonBundle\Component\OldForm\Bootstrap\Element\File,
    CommonBundle\Component\OldForm\Admin\Element\Text,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    Doctrine\ORM\EntityManager,
    BannerBundle\Entity\Node\Banner,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Banner
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\OldForm\Admin\Form
{

    const BANNER_WIDTH = 940;
    const BANNER_HEIGHT = 100;
    const BANNER_FILESIZE = '10MB';

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int             $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $this->setAttribute('id', 'uploadBanner');
        $this->setAttribute('enctype', 'multipart/form-data');

        $field = new Text('name');
        $field->setLabel('Name')
            ->setAttribute('data-help', 'The name of the banner (only shown in the admin).')
            ->setRequired(true);
        $this->add($field);

        $field = new Text('start_date');
        $field->setLabel('Start Date')
            ->setAttribute('data-help', 'The start date for showing this banner, overrulled by "active".')
            ->setAttribute('placeholder', 'dd/mm/yyyy hh:mm')
            ->setAttribute('data-datepicker', true)
            ->setAttribute('data-timepicker', true)
            ->setRequired();
        $this->add($field);

        $field = new Text('end_date');
        $field->setLabel('End Date')
            ->setAttribute('data-help', 'The end date for showing this banner, overrulled by "active".')
            ->setAttribute('placeholder', 'dd/mm/yyyy hh:mm')
            ->setAttribute('data-datepicker', true)
            ->setAttribute('data-timepicker', true)
            ->setRequired();
        $this->add($field);

        $field = new Checkbox('active');
        $field->setLabel('Active')
            ->setAttribute('data-help', 'Flag whether the banner will be shown on the website.');
        $this->add($field);

        $field = new File('file');
        $field->setLabel('Image (' . self::BANNER_WIDTH . ' x ' . self::BANNER_HEIGHT . ')')
            ->setAttribute('data-help', 'The image for the banner. The maximum file size is ' . self::BANNER_FILESIZE . '. This must be a valid image (jpg, png, ...). The image must have a width of  ' . self::BANNER_WIDTH . 'px and a height of ' . self::BANNER_HEIGHT . 'px.')
            ->setRequired(true);
        $this->add($field);

        $field = new Text('url');
        $field->setLabel('URL')
            ->setAttribute('data-help', 'The url to open after clicking on the banner');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'banner_add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'start_date',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'date',
                            'options' => array(
                                'format' => 'd/m/Y H:i',
                            ),
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'end_date',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'date',
                            'options' => array(
                                'format' => 'd/m/Y H:i',
                            ),
                        ),
                        new DateCompareValidator('start_date', 'd/m/Y H:i'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'name',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'url',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'file',
                    'required' => false,
                    'validators'  => array(
                        array(
                            'name' => 'filefilessize',
                            'options' => array(
                                'max' => self::BANNER_FILESIZE,
                            ),
                        ),
                        array(
                            'name' => 'fileisimage',
                        ),
                        array(
                            'name' => 'fileimagesize',
                            'options' => array(
                                'minwidth'  => self::BANNER_WIDTH,
                                'maxwidth'  => self::BANNER_WIDTH,
                                'minheight' => self::BANNER_HEIGHT,
                                'maxheight' => self::BANNER_HEIGHT,
                            )
                        ),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
