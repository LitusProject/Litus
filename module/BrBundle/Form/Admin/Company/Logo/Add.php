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

namespace BrBundle\Form\Admin\Company\Logo;

use BrBundle\Entity\Company,
    BrBundle\Entity\Company\Logo;

/**
 * Add Logo
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Company\Logo';

    const FILESIZE = '10MB';

    /**
     * @var Company The company to add the logo
     */
    private $_company;

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'file',
            'name'       => 'logo',
            'label'      => 'Logo',
            'required'   => true,
            'attributes' => array(
                'data-help' => 'The logo must be an image of max ' . self::FILESIZE . '.',
            ),
            'options'    => array(
                'input' => array(
                    'validators' => array(
                        array(
                            'name' => 'fileisimage',
                        ),
                        array(
                            'name' => 'filesize',
                            'options' => array(
                                'max' => self::FILESIZE,
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'text',
            'name'     => 'url',
            'label'    => 'URL',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'uri',
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'type',
            'label'      => 'Type',
            'required'   => true,
            'attributes' => array(
                'options' => Logo::$POSSIBLE_TYPES,
                'data-help' => 'The location where the logo will be used:
                    <ul>
                        <li><b>Homepage:</b> In the footer of the website</li>
                        <li><b>Cudi:<br> In the footer of the queue screen at Cudi</li>
                    </ul>',
            ),
            'options'    => array(
                'input' => array(
                    'validators' => array(
                        array(
                            'name' => 'company_logo_type',
                            'options' => array(
                                'company' => $this->_company,
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Add', 'logo_add');
    }

    /**
     * @param  Company $company
     * @return self
     */
    public function setCompany(Company $company)
    {
        $this->_company = $company;

        return $this;
    }
}
