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

namespace FormBundle\Form\Admin\Group;

use CommonBundle\Component\OldForm\Admin\Element\Select,
    CommonBundle\Component\OldForm\Admin\Element\Tabs,
    CommonBundle\Component\OldForm\Admin\Form\SubForm\TabContent,
    CommonBundle\Component\OldForm\Admin\Form\SubForm\TabPane,
    CommonBundle\Component\OldForm\Admin\Element\Text,
    CommonBundle\Component\OldForm\Admin\Element\Textarea,
    Doctrine\ORM\EntityManager,
    FormBundle\Entity\Node\Form,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Group
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\OldForm\Admin\Form\Tabbable
{
    /**
     * @var EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $tabs = new Tabs('languages');
        $this->add($tabs);

        $tabContent = new TabContent('tab_content');

        foreach ($this->getLanguages() as $language) {
            $tabs->addTab(array($language->getName() => '#tab_' . $language->getAbbrev()));

            $pane = new TabPane('tab_' . $language->getAbbrev());

            $field = new Text('title_' . $language->getAbbrev());
            $field->setLabel('Title')
                ->setRequired($language->getAbbrev() == \Locale::getDefault());
            $pane->add($field);

            $field = new Textarea('introduction_' . $language->getAbbrev());
            $field->setLabel('Introduction')
                ->setAttribute('class', 'md')
                ->setAttribute('rows', 20)
                ->setRequired($language->getAbbrev() == \Locale::getDefault());
            $pane->add($field);

            $tabContent->add($pane);
        }

        $this->add($tabContent);

        $field = new Select('start_form');
        $field->setLabel('Start Form')
            ->setAttribute('options', $this->getActiveForms());
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'form_add');
        $this->add($field);
    }

    protected function getLanguages()
    {
        return $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
    }

    private function getActiveForms()
    {
        $forms = $this->_entityManager
            ->getRepository('FormBundle\Entity\Node\Form')
            ->findAllActive();

        $language = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev('en');

        $options = array();
        foreach ($forms as $form) {
            $group = $this->_entityManager
                ->getRepository('FormBundle\Entity\Node\Group\Mapping')
                ->findOneByForm($form);

            if (null == $group)
                $options[$form->getId()] = $form->getTitle($language);
        }

        return $options;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        foreach ($this->getLanguages() as $language) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'title_' . $language->getAbbrev(),
                        'required' => $language->getAbbrev() == \Locale::getDefault(),
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'introduction_' . $language->getAbbrev(),
                        'required' => $language->getAbbrev() == \Locale::getDefault(),
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );
        }

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'start_form',
                    'required' => true,
                )
            )
        );

        return $inputFilter;
    }
}
