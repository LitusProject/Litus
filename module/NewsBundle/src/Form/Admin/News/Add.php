<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace NewsBundle\Form\Admin\News;

use CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Component\Form\Admin\Element\Tabs,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabContent,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabPane,
    Doctrine\ORM\EntityManager,
    NewsBundle\Entity\Nodes\News,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add News
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $tabs = new Tabs('languages');
        $this->add($tabs);

        $tabContent = new TabContent('tab_content');

        foreach($this->getLanguages() as $language) {
            $tabs->addTab(array($language->getName() => '#tab_' . $language->getAbbrev()));

            $pane = new TabPane('tab_' . $language->getAbbrev());

            $field = new Text('title_' . $language->getAbbrev());
            $field->setLabel('Title')
                ->setAttribute('width', '400px')
                ->setRequired($language->getAbbrev() == \Locale::getDefault());

            $pane->add($field);

            $field = new Textarea('content_' . $language->getAbbrev());
            $field->setLabel('Content')
                ->setAttribute('rows', 20)
                ->setRequired($language->getAbbrev() == \Locale::getDefault());

            $pane->add($field);

            $tabContent->add($pane);
        }

        $this->add($tabContent);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'news_add');
        $this->add($field);
    }

    protected function getLanguages()
    {
        return $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            foreach($this->getLanguages() as $language) {
                if ($language->getAbbrev() !== \Locale::getDefault())
                    continue;

                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name'     => 'title_' . $language->getAbbrev(),
                            'required' => true,
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        )
                    )
                );

                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name'     => 'content_' . $language->getAbbrev(),
                            'required' => true,
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        )
                    )
                );
            }
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}
