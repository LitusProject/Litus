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

namespace ShopBundle\Form\Shop;

use Zend\Form\Element;

/**
 * Reserve
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class Sessions extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var SalesSession[]
     */
    private $salesSessions = array();

    public function init()
    {
        parent::init();
        $this->add(array(
            'type'       => 'select',
            'name'       => 'salesSession',
            'label'      => 'Sales Session',
            'required'   => true,
            'escape'     => false,
            'attributes' => array(
                'options' => $this->createSalesSessionsArray(),
            ),
        ));
        $this->addSubmit('Reserve', 'submit');
    }

    /**
     * @return SalesSession[]
     */
    private function createSalesSessionsArray()
    {
        $translator = $this->getServiceLocator()->get('translator');

        $result = array();
        foreach ($this->salesSessions as $session) {
            $result[$session->getId()] = $translator->translate($session->getStartDate()->format('l')) . ' ' . $session->getStartDate()->format('d/m/Y H:i') . ' - ' . $session->getEndDate()->format('H:i');
        }

        return $result;
    }

    /**
     * @param SalesSession[] $salesSessions
     */
    public function setSalesSessions($salesSessions)
    {
        $this->salesSessions = $salesSessions;
    }
}
