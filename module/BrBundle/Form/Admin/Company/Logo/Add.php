<?php

namespace BrBundle\Form\Admin\Company\Logo;

use BrBundle\Entity\Company;
use BrBundle\Entity\Company\Logo;

/**
 * Add Logo
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Company\Logo';

    const FILE_SIZE = '10MB';

    /**
     * @var Company The company to add the logo
     */
    private $company;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'file',
                'name'       => 'logo',
                'label'      => 'Logo',
                'required'   => true,
                'attributes' => array(
                    'data-help' => 'The logo must be an image with a file size limit of ' . self::FILE_SIZE . '.',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name' => 'FileIsImage',
                            ),
                            array(
                                'name'    => 'FileSize',
                                'options' => array(
                                    'max' => self::FILE_SIZE,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'url',
                'label'    => 'URL',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Uri'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'type',
                'label'      => 'Type',
                'required'   => true,
                'attributes' => array(
                    'options'   => Logo::$possibleTypes,
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
                                'name'    => 'LogoType',
                                'options' => array(
                                    'company' => $this->company,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'logo_add');
    }

    /**
     * @param  Company $company
     * @return self
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;

        return $this;
    }
}
