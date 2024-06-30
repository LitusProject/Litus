<?php

namespace LogisticsBundle\Form\Catalog\InventoryArticle;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;
use LogisticsBundle\Entity\InventoryArticle;
use LogisticsBundle\Entity\InventoryCategory;

/**
 * Form used to add an InventoryArticle
 *
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = \LogisticsBundle\Hydrator\InventoryArticle::class;

    /**
     * @var Academic
     */
    protected Academic $academic;

    /**
     * @var AcademicYear
     */
    protected AcademicYear $academicYear;

    public function init(): void
    {
        parent::init();

        $this->add(
            array(
                'type'        => 'text',
                'name'        => 'name',
                'label'       => 'Name',
                'required'    => true,
                'attributes'    => array(
                    'placeholder'   => 'Router',
                ),
                'options' => array(
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
                'type'        => 'text',
                'name'        => 'amount',
                'label'       => 'Amount',
                'required'    => true,
                'attributes'    => array(
                    'placeholder'   => '0',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'location',
                'label'      => 'Location',
                'required'   => true,
                'attributes'    => array(
                    'placeholder'   => 'Loods',
                ),
                'options' => array(
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
                'type'        => 'text',
                'name'        => 'spot',
                'label'       => 'Spot',
                'required'    => true,
                'attributes'    => array(
                    'placeholder'   => 'Rek',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        if ($this->academic->getUnit($this->academicYear) 
            && $this->academic->getUnit($this->academicYear)->getName() === 'Logistiek'
        ) {
            $this->add(
                array(
                    'type'       => 'select',
                    'name'       => 'category',
                    'label'      => 'Category',
                    'required'   => true,
                    'options' => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                    'attributes' => array(
                        'options'  => $this->createCategoriesArray(),
                    ),
                )
            );
        }

        if ($this->academic->getUnit($this->academicYear)) {
            $this->add(
                array(
                    'type'       => 'select',
                    'name'       => 'unit',
                    'label'      => 'Unit',
                    'required'   => true,
                    'options' => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                    'attributes' => array(
                        'options'  => $this->createUnitsArray($this->academic),
                        'value'    => $this->academic->getUnit($this->academicYear)->getId(),
                    ),
                )
            );
        }

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'visibility',
                'label'      => 'Visibility',
                'required'   => true,
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
                'attributes' => array(
                    'options'  => InventoryArticle::$VISIBILITIES,
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'status',
                'label'      => 'Status',
                'required'   => true,
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
                'attributes' => array(
                    'options'  => InventoryArticle::$STATES,
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'warranty_date',
                'label'    => 'Warranty date',
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'Date',
                                'options' => array(
                                    'format' => 'd/m/Y H:i',
                                ),
                            ),
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'now',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'        => 'text',
                'name'        => 'deposit',
                'label'       => 'Deposit',
                'attributes'    => array(
                    'placeholder'   => '0',
                    'value'         => '0',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'        => 'text',
                'name'        => 'rent',
                'label'       => 'Rent',
                'attributes'    => array(
                    'placeholder'   => '0',
                    'value'         => '0',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'textarea',
                'name'       => 'external_comment',
                'label'      => 'External comment',
                'attributes'  => array(
                    'rows'    => 2,
                ),
                'options' => array(
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
                'type'       => 'textarea',
                'name'       => 'internal_comment',
                'label'      => 'Internal comment',
                'attributes'  => array(
                    'rows'    => 2,
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'btn btn-primary pull-right');
    }

    /**
     * @param Academic $academic
     * @return self
     */
    public function setAcademic(Academic $academic): self
    {
        $this->academic = $academic;

        return $this;
    }

    /**
     * @param AcademicYear $academicYear
     * @return self
     */
    public function setAcademicYear(AcademicYear $academicYear): self
    {
        $this->academicYear = $academicYear;

        return $this;
    }

    /**
     * @param $academic
     * @return array
     */
    protected function createCategoriesArray(): array
    {
        $categories = $this->getEntityManager()
            ->getRepository(InventoryCategory::class)
            ->findAll();

        $categoriesArray = array();
        foreach ($categories as $category) {
            $categoriesArray[$category->getId()] = $category->getName();
        }

        return $categoriesArray;
    }
}
