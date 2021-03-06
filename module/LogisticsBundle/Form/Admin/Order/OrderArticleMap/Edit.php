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

namespace LogisticsBundle\Form\Admin\Order\OrderArticleMap;

use LogisticsBundle\Entity\Order\OrderArticleMap;

/**
 * Edit OrderArticleMap
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var OrderArticleMap
     */
    private $orderArticleMap;

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

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'status',
                'label'      => 'Status',
                'required'   => true,
                'attributes' => array(
                    'options' => OrderArticleMap::$POSSIBLE_STATUSES,
                ),
            )
        );

        $this->addSubmit('Save Changes', 'article_edit');

        if ($this->orderArticleMap !== null) {
            $this->bind($this->orderArticleMap);
        }
    }

    public function setOrderArticleMap(OrderArticleMap $orderArticleMap)
    {
        $this->orderArticleMap = $orderArticleMap;

        return $this;
    }
}
