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

namespace CudiBundle\Form\Isic;

class Order extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = 'CudiBundle\Hydrator\Isic\Order';

    const ISIC_PHOTO_WIDTH = 140;
    const ISIC_PHOTO_HEIGHT = 200;
    const ISIC_PHOTO_FILE_SIZE = '1MB';

    public function init()
    {
        parent::init();

        $this->setAttribute('id', 'isic-order');

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'personal_info',
                'label'    => 'Personal Info',
                'elements' => array(
                    array(
                        'type'       => 'text',
                        'name'       => 'first_name',
                        'label'      => 'First Name',
                        'required'   => true,
                        'attributes' => array(
                            'id' => 'first_name',
                        ),
                        'options' => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'text',
                        'name'       => 'last_name',
                        'label'      => 'Last Name',
                        'required'   => true,
                        'attributes' => array(
                            'id' => 'last_name',
                        ),
                        'options' => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'text',
                        'name'       => 'birthday',
                        'label'      => 'Birthday',
                        'required'   => true,
                        'attributes' => array(
                            'data-help'   => 'The birthday of the user.',
                            'placeholder' => 'dd/mm/yyyy',
                        ),
                        'options' => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array(
                                        'name'    => 'Date',
                                        'options' => array(
                                            'format' => 'd/m/Y',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'select',
                        'name'       => 'sex',
                        'label'      => 'Sex',
                        'required'   => true,
                        'attributes' => array(
                            'options' => array(
                                'M' => 'M',
                                'F' => 'F',
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'select',
                        'name'       => 'language',
                        'label'      => 'Language',
                        'required'   => true,
                        'attributes' => array(
                            'options' => array(
                                'NL' => 'NL',
                                'EN' => 'EN',
                                'FR' => 'FR',
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'common_address_add',
                'name'     => 'address',
                'label'    => 'Home Address',
                'required' => true,
            )
        );

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'contact_details',
                'label'    => 'Contact Details',
                'elements' => array(
                    array(
                        'type'     => 'text',
                        'name'     => 'email',
                        'label'    => 'E-mail',
                        'required' => true,
                        'options'  => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array(
                                        'name' => 'EmailAddress',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'text',
                        'name'       => 'phone_number',
                        'label'      => 'Phone Number',
                        'attributes' => array(
                            'placeholder' => '+CCAAANNNNNN',
                        ),
                        'options' => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array('name' => 'PhoneNumber'),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'studies',
                'label'    => 'Studies',
                'elements' => array(
                    array(
                        'type'       => 'select',
                        'name'       => 'course',
                        'label'      => 'Course',
                        'required'   => true,
                        'attributes' => array(
                            'options' => unserialize(
                                $this->getEntityManager()
                                    ->getRepository('CommonBundle\Entity\General\Config')
                                    ->getConfigValue('cudi.isic_studies')
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'select',
                        'name'       => 'year',
                        'label'      => 'Year',
                        'required'   => true,
                        'attributes' => array(
                            'options' => array(
                                '1' => '1e Bachelor',
                                '2' => '2e Bachelor',
                                '3' => '3e Bachelor',
                                '4' => '1e Master',
                                '5' => '2e Master',
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'select',
                        'name'       => 'school',
                        'label'      => 'School',
                        'required'   => true,
                        'attributes' => array(
                            'options' => unserialize(
                                $this->getEntityManager()
                                    ->getRepository('CommonBundle\Entity\General\Config')
                                    ->getConfigValue('cudi.isic_schools')
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'select',
                        'name'       => 'student_city',
                        'label'      => 'Student City',
                        'required'   => true,
                        'attributes' => array(
                            'options' => unserialize(
                                $this->getEntityManager()
                                    ->getRepository('CommonBundle\Entity\General\Config')
                                    ->getConfigValue('cudi.isic_student_cities')
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'photo_group',
                'label'    => 'Photo',
                'elements' => array(
                    array(
                        'type'       => 'file',
                        'name'       => 'photo',
                        'label'      => 'Photo  (' . self::ISIC_PHOTO_WIDTH . ' x ' . self::ISIC_PHOTO_HEIGHT . ')',
                        'required'   => true,
                        'attributes' => array(
                            'data-help' => 'The image for the photo on your card. The maximum file size is ' . self::ISIC_PHOTO_FILE_SIZE . '. This must be a valid image (jpg, png, ...). The image must have a width of  ' . self::ISIC_PHOTO_WIDTH . 'px and a height of ' . self::ISIC_PHOTO_HEIGHT . 'px.',
                        ),
                        'options' => array(
                            'input' => array(
                                'validators' => array(
                                    array(
                                        'name' => 'FileIsImage',
                                    ),
                                    array(
                                        'name'    => 'FileSize',
                                        'options' => array(
                                            'max' => self::ISIC_PHOTO_FILE_SIZE,
                                        ),
                                    ),
                                    array(
                                        'name'    => 'FileImageSize',
                                        'options' => array(
                                            'minwidth'  => self::ISIC_PHOTO_WIDTH,
                                            'maxwidth'  => self::ISIC_PHOTO_WIDTH,
                                            'minheight' => self::ISIC_PHOTO_HEIGHT,
                                            'maxheight' => self::ISIC_PHOTO_HEIGHT,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $newsletterMandatory = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.isic_newsletter_mandatory');

        $partnerMandatory = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.isic_partner_mandatory');

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'optins',
                'label'    => 'Newsletter',
                'elements' => array(
                    array(
                        'type'       => 'checkbox',
                        'name'       => 'newsletter',
                        'label'      => 'Receive the ISIC/Club newsletter',
                        'value'      => $newsletterMandatory,
                        'attributes' => array(
                            'id'       => 'newsletter',
                            'disabled' => $newsletterMandatory == 1,
                        ),
                    ),
                    array(
                        'type'       => 'checkbox',
                        'name'       => 'partners',
                        'label'      => 'Receive information Guido NV',
                        'value'      => $partnerMandatory,
                        'attributes' => array(
                            'id'       => 'partners',
                            'disabled' => $partnerMandatory == 1,
                        ),
                    ),
                ),

            )
        );

        $this->addSubmit('Order');
    }
}
