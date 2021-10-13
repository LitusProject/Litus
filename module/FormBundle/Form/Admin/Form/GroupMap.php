<?php

namespace FormBundle\Form\Admin\Form;

/**
 * Add GroupMap
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class GroupMap extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'form',
                'label'      => 'Form',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->getActiveForms(),
                ),
            )
        );

        $this->addSubmit('Add', 'form_add');
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
