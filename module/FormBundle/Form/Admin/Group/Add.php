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

        $this->add(array(
            'type'       => 'select',
            'name'       => 'start_form',
            'label'      => 'Start Form',
            'required'   => true,
            'attributes' => array(
                'options' => $this->getActiveForms(),
            ),
        ));

        $this->addSubmit('Add', 'form_add');
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(array(
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
        ));

        $container->add(array(
            'type'       => 'textarea',
            'name'       => 'introduction',
            'label'      => 'Introduction',
            'required'   => $isDefault,
            'attributes' => array(
                'class' => 'md',
                'rows'  => 20,
            ),
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));
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
                ->getRepository('FormBundle\Entity\Node\Group\Mapping')
                ->findOneByForm($form);

            if (null == $group) {
                $options[$form->getId()] = $form->getTitle($language);
            }
        }

        return $options;
    }
}
