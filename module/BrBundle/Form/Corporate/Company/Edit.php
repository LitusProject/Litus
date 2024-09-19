<?php

namespace BrBundle\Form\Corporate\Company;

use BrBundle\Entity\Company;

/**
 * Edit Company
 *
 */
class Edit extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Company';

    protected $company;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'name',
                'label'    => 'Company Name',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'CompanyName',
                                'options' => array(
                                    'company' => $this->company,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'sector',
                'label'      => 'Sector',
                'required'   => true,
                'attributes' => array(
                    'options' => Company::POSSIBLE_SECTORS,
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'vat_number',
                'label'    => 'VAT Number',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'phone_number',
                'label'      => 'Phone Number',
                'required'   => false,
                'attributes' => array(
                    'placeholder' => '+CCAAANNNNNN',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'PhoneNumber'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'website',
                'label'    => 'Website',
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
                'name'       => 'cvbook',
                'label'      => 'CV Book',
                'required'   => false,
                'attributes' => array(
                    'multiple'  => true,
                    'options'   => $this->getCvBookYears(),
                    'data-help' => 'The selected years will be visible in the corporate app of this company. The archived ones are downloadable in pdf format.',
                    'disabled'  => true,
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'common_address_add',
                'name'     => 'address',
                'label'    => 'Address',
                'required' => true,
            )
        );

        $this->add(
            array(
                'type'       => 'fieldset',
                'name'       => 'invoice',
                'label'      => 'Invoice Data',
                'attributes' => array(
                    'id' => 'invoice_form',
                ),
                'elements'   => array(
                    array(
                        'type'     => 'text',
                        'name'     => 'invoice_name',
                        'label'    => 'Company Invoice Name',
                        'required' => false,
                        'options'  => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array(
                                        'name'    => 'CompanyName',
                                        'options' => array(
                                            'company' => $this->company,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),

                    array(
                        'type'     => 'text',
                        'name'     => 'invoice_vat_number',
                        'label'    => 'VAT Number',
                        'required' => false,
                        'options'  => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                        ),
                    ),

                    array(
                        'type'     => 'common_address_add',
                        'name'     => 'invoice_address',
                        'label'    => 'Invoice Address',
                        'required' => false,
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'fieldset',
                'name'       => 'page',
                'label'      => 'Page',
                'attributes' => array(
                    'id' => 'page_form',
                ),
                'elements'   => array(
                    array(
                        'type'       => 'textarea',
                        'name'       => 'description',
                        'label'      => 'Description',
                        'attributes' => array(
                            'id' => 'description',
                        ),
                        'options'    => array(
                            'input'    => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),

                            'multiple' => true,
                            'options'  => $this->getYears(),
                            'disabled' => true,
                        ),
                    ),
                    array(
                        'type'       => 'textarea',
                        'name'       => 'short_description',
                        'label'      => 'Short Description',
                        'attributes' => array(
                            'id' => 'shortDescription',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                            'validators' => array(
                                array(
                                    'name'    => 'StringLength',
                                    'options' => array(
                                        'max' => '250',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'text',
                        'name'       => 'youtube_url',
                        'label'      => 'YouTube video URL',
                        'required'   => false,
                        'attributes' => array(
                            'id'          => 'youtubeURL',
                            'placeholder' => 'https://www.youtube.com/watch?v=AVJHzfv-Yis',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array('name' => 'Uri'),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Save', 'company_edit');

        if ($this->company !== null) {
            $this->bind($this->company);
        }
    }

    /**
     * @return array
     */
    private function getYears()
    {
        $years = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $options = array();
        foreach ($years as $year) {
            $options[$year->getId()] = $year->getCode();
        }

        return $options;
    }

    /**
     * @return array
     */
    private function getArchiveYears()
    {
        $years = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.cv_archive_years')
        );

        $options = array();
        foreach ($years as $code => $year) {
            $options['archive-' . $code] = $year['full_year'] . ' (Archive)';
        }

        return $options;
    }

    /**
     * @return array
     */
    private function getCvBookYears()
    {
        $cvYears = $this->getArchiveYears();
        $years = $this->getYears();
        foreach ($years as $key => $year) {
            $shortCode = substr($year, 2, 2) . substr($year, 7, 2);
            if (isset($cvYears['archive-' . $shortCode])) {
                continue;
            }
            $cvYears['year-' . $key] = $year;
        }
        asort($cvYears);

        return $cvYears;
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
