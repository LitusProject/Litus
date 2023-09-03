<?php

namespace LogisticsBundle\Form\Admin\Article;

class Csv extends \CommonBundle\Component\Form\Admin\Form
{
    const FILE_SIZE = '10MB';

    protected $hydrator = 'LogisticsBundle\Hydrator\Article';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'    => 'select',
                'name'    => 'unit',
                'label'   => 'Unit',
                'options' => array(
                    'input' => array(
                        'filter' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
                'required'   => true,
                'attributes' => array(
                    'options' => $this->createUnitsArray(),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'file',
                'name'       => 'file',
                'label'      => 'Article csv',
                //                'required'   => true,
                'attributes' => array(
                    'data-help' => 'The maximum file size is ' . self::FILE_SIZE . '.',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'FileSize',
                                'options' => array(
                                    'max' => self::FILE_SIZE,
                                ),
                            ),
                            array(
                                'name'    => 'FileExtension',
                                'options' => array(
                                    'extension' => 'csv',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'article_csv');
    }

    /**
     * @return array
     */
    private function createUnitsArray()
    {
        $units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAllActive();

        if (count($units) == 0) {
            throw new RuntimeException('There needs to be at least one unit before you can add a RegistrationShift');
        }

        $unitsArray = array();
        foreach ($units as $unit) {
            $unitsArray[$unit->getId()] = $unit->getName();
        }

        return $unitsArray;
    }
}