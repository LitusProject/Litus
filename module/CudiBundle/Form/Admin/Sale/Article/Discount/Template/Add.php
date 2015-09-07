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

namespace CudiBundle\Form\Admin\Sale\Article\Discount\Template;

use CudiBundle\Entity\Sale\Article\Discount\Discount;

/**
 * Add Template
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Dario  Incalza <dario.incalza@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'CudiBundle\Hydrator\Sale\Article\Discount\Template';

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'text',
            'name'       => 'name',
            'label'      => 'Name',
            'required'   => true,
            'attributes' => array(
                'id' => 'name',
            ),
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'value',
            'label'      => 'Value',
            'required'   => true,
            'attributes' => array(
                'id' => 'value',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'price'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'method',
            'label'      => 'Method',
            'required'   => true,
            'attributes' => array(
                'data-help' => 'The method of this discount:
                    <ul>
                        <li><b>Percentage:</b> the value will used as the percentage to substract from the real price</li>
                        <li><b>Fixed:</b> the value will be subtracted from the real price</li>
                        <li><b>Override:</b> the value will be used as the new price</li>
                    </ul>',
                'id'        => 'method',
                'options'   => Discount::$possibleMethods,
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'type',
            'label'      => 'Type',
            'required'   => true,
            'attributes' => array(
                'id'      => 'type',
                'options' => Discount::$possibleTypes,
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'organization',
            'label'      => 'Organization',
            'required'   => true,
            'attributes' => array(
                'id'      => 'organization',
                'options' => $this->getOrganizations(),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'rounding',
            'label'      => 'Rounding',
            'required'   => true,
            'attributes' => array(
                'id'      => 'rounding',
                'options' => $this->getRoundings(),
            ),
        ));

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'apply_once',
            'label'      => 'Apply Once',
            'attributes' => array(
                'data-help' => 'Enabling this option will allow apply this discount only once to every user.',
                'id'        => 'apply_once',
            ),
        ));

        $this->addSubmit('Add', 'add');
    }

    private function getRoundings()
    {
        $roundings = array();
        foreach (Discount::$possibleRoundings as $key => $rounding) {
            $roundings[$key] = $rounding['name'];
        }

        return $roundings;
    }

    private function getOrganizations()
    {
        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $organizationsOptions = array(0 => 'All');
        foreach ($organizations as $organization) {
            $organizationsOptions[$organization->getId()] = $organization->getName();
        }

        return $organizationsOptions;
    }
}
