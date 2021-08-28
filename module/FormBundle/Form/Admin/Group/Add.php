<?php

namespace FormBundle\Form\Admin\Group;

use CommonBundle\Component\Form\FieldsetInterface;
use CommonBundle\Entity\General\Language;

/**
 * Add Group
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    protected $hydrator = 'FormBundle\Hydrator\Node\Group';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'start_form',
                'label'      => 'Start Form',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->getActiveForms(),
                ),
            )
        );

        $this->addSubmit('Add', 'form_add');
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(
            array(
                'type'     => 'text',
                'name'     => 'title',
                'label'    => 'Title',
                'required' => $isDefault,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $container->add(
            array(
                'type'       => 'textarea',
                'name'       => 'introduction',
                'label'      => 'Introduction',
                'required'   => $isDefault,
                'attributes' => array(
                    'class' => 'md',
                    'rows'  => 20,
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
    }

    private function getActiveForms()
    {
        $forms = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Form')
            ->findAllActive();

        $language = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev('en');

        $options = array();
        foreach ($forms as $form) {
            $group = $this->getEntityManager()
                ->getRepository('FormBundle\Entity\Node\Form\GroupMap')
                ->findOneByForm($form);

            if ($group == null) {
                $options[$form->getId()] = $form->getTitle($language);
            }
        }

        return $options;
    }
}
