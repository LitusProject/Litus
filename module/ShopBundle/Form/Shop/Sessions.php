<?php

namespace ShopBundle\Form\Shop;

use CommonBundle\Component\ServiceManager\ServiceLocatorAware\TranslatorTrait;

/**
 * Sessions
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class Sessions extends \CommonBundle\Component\Form\Admin\Form
{
    use TranslatorTrait;

    /**
     * @var array
     */
    private $salesSessions = array();

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'salesSession',
                'label'      => 'Sales Session',
                'required'   => true,
                'escape'     => false,
                'attributes' => array(
                    'options' => $this->createSalesSessionsArray(),
                ),
            )
        );

        $this->addSubmit('Reserve', 'submit');
    }

    /**
     * @return array
     */
    private function createSalesSessionsArray()
    {
        $translator = $this->getTranslator();

        $result = array();
        foreach ($this->salesSessions as $session) {
            $result[$session->getId()] = $translator->translate($session->getStartDate()->format('l')) . ' ' . $session->getStartDate()->format('d/m/Y H:i') . ' - ' . $session->getEndDate()->format('H:i');
        }

        return $result;
    }

    /**
     * @param array $salesSessions
     */
    public function setSalesSessions($salesSessions)
    {
        $this->salesSessions = $salesSessions;
    }
}
