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

namespace LogisticsBundle\Form\Catalog\Map;

use LogisticsBundle\Entity\Order\OrderArticleMap as Map;

/**
 * Edit OrderArticleMap
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var Map
     */
    private $map;

    protected $hydrator = 'LogisticsBundle\Hydrator\Order\OrderArticleMap';

    public function init()
    {
        parent::init();


        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'amount',
                'label'      => 'Amount',
                'required'  => true,
                'options'  => array(
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

        $this->addSubmit('Save Changes');

        if ($this->map !== null) {
            $this->bind($this->map);
        }
    }

    public function setMap(Map $map)
    {
        $this->map = $map;

        return $this;
    }
}
