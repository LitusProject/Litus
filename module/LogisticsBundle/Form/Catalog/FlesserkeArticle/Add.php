<?php

namespace LogisticsBundle\Form\Catalog\FlesserkeArticle;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;
use LogisticsBundle\Entity\FlesserkeArticle;
use LogisticsBundle\Entity\FlesserkeCategory;

/**
 * Form used to add an FlesserkeArticle
 *
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = \LogisticsBundle\Hydrator\FlesserkeArticle::class;

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
                'name'        => 'barcode',
                'label'       => 'Barcode',
                'required'    => true,
                'attributes'    => array(
                    'placeholder'   => '5400141087680',
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
                'type'        => 'text',
                'name'        => 'brand',
                'label'       => 'Brand',
                'required'    => true,
                'attributes'    => array(
                    'placeholder'   => 'Boni',
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

        $this->add(
            array(
                'type'        => 'text',
                'name'        => 'per_unit',
                'label'       => 'Per unit',
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
                    'options'  => FlesserkeArticle::$UNITS,
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'expiration_date',
                'label'    => 'Expiration date',
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'Date',
                                'options' => array(
                                    'format' => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

//        $this->add(
//            array(
//                'type'       => 'textarea',
//                'name'       => 'external_comment',
//                'label'      => 'External comment',
//                'attributes'  => array(
//                    'rows'    => 2,
//                ),
//                'options' => array(
//                    'input' => array(
//                        'filters' => array(
//                            array('name' => 'StringTrim'),
//                        ),
//                    ),
//                ),
//            )
//        );

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
            ->getRepository(FlesserkeCategory::class)
            ->findAll();

        $categoriesArray = array();
        foreach ($categories as $category) {
            $categoriesArray[$category->getId()] = $category->getName();
        }

        return $categoriesArray;
    }
}
