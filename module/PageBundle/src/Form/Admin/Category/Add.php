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

namespace PageBundle\Form\Admin\Category;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    CommonBundle\Component\Form\Admin\Element\Tabs,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabContent,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabPane,
    Doctrine\ORM\EntityManager,
    PageBundle\Entity\Category,
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text;

/**
 * Add Category
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
     * @param mixed $opts The form's options
     */
    public function __construct(EntityManager $entityManager, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;

        $tabs = new Tabs('languages');
        $this->addElement($tabs);

        $tabContent = new TabContent();

        foreach($this->getLanguages() as $language) {
            $tabs->addTab(array($language->getName() => '#tab_' . $language->getAbbrev()));

            $pane = new TabPane('tab_' . $language->getAbbrev());

            $name = new Text('name_' . $language->getAbbrev());
            $name->setLabel('Name')
                ->setDecorators(array(new FieldDecorator()));
            $pane->addElement($name);

            $tabContent->addSubForm($pane, 'tab_' . $language->getAbbrev());
        }

        $this->addSubForm($tabContent, 'tab_content');

        $field = new Submit('submit');
        $field->setLabel('Add')
            ->setAttrib('class', 'category_add')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }

    protected function getLanguages()
    {
        return $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
    }

    public function isValid($data)
    {
        $valid = parent::isValid($data);

        $form = $this->getSubForm('tab_content');

        $validTranslations = false;
        foreach($this->getLanguages() as $language) {
            $name = $form->getSubForm('tab_' . $language->getAbbrev())->getElement('name_' . $language->getAbbrev());

            if ($language == \Zend\Registry::get('Litus_Localization_FallbackLanguage')) {
                if ('' == $name->getValue()) {
                    $name->addError('The translation in this language is required');
                    $valid = false;
                }
            }

            if ('' != $name->getValue()) {
                $validTranslations = true;
                break;
            }
        }

        if (!$validTranslations) {
            foreach($this->getLanguages() as $language) {
                $name = $form->getSubForm('tab_' . $language->getAbbrev())->getElement('name_' . $language->getAbbrev());
                $name->addError('At least one translation is required');
            }

            $valid = false;
        }

        return $valid;
    }
}
