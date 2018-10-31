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

namespace LogisticsBundle\Form\Admin\Driver;

/**
 * The form used to add a new Driver
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'LogisticsBundle\Hydrator\Driver';

    public function init()
    {
        parent::init();

        $years = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $yearnames = array();
        foreach ($years as $year) {
            $yearnames[$year->getId()] = $year->getCode();
        }

        $this->add(
            array(
                'type'     => 'typeahead',
                'name'     => 'person',
                'label'    => 'Name',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadDriver'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'color',
                'label'      => 'Color',
                'value'      => '#888888',
                'attributes' => array(
                    'id' => 'color',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'Regex',
                                'options' => array(
                                    'pattern' => '/^#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$/',
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
                'name'       => 'years',
                'label'      => 'Years',
                'attributes' => array(
                    'multiple' => true,
                    'options'  => $yearnames,
                ),
            )
        );

        $this->addSubmit('Add', 'driver_add');
    }
}
