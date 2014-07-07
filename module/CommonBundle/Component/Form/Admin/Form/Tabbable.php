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

namespace CommonBundle\Component\Form\Admin\Form;

use CommonBundle\Component\Form\FieldsetInterface,
    CommonBundle\Entity\General\Language,
    Locale,
    RuntimeException,
    Zend\InputFilter\InputFilter;

/**
 * Extending Zend's form component, so that our forms look the way we want
 * them to.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
abstract class Tabbable extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var string The prefix of the tab elements
     */
    private $prefix = '';

    public function init()
    {
        $languages = $this->getLanguages();
        $prefix = $this->getPrefix();

        if (count($languages) === 0)
            throw new RuntimeException('No languages found!');

        if (count($languages) === 1) {
            $this->initBeforeTabs();

            $this->addTab($this, $languages[0], true);
        } else {
            $defaultLanguage = Locale::getDefault();

            $this->add(array(
                'type' => 'tabs',
                'name' => $prefix . 'languages',
            ));

            $tabs = $this->get($prefix . 'languages');
            $tabContent = $this->createTabContent();

            $this->initBeforeTabs();

            foreach ($languages as $language) {
                $abbrev = $language->getAbbrev();

                $pane = $this->createTabPane($prefix . 'tab_' . $abbrev);

                $this->addTab($pane, $language, $abbrev == $defaultLanguage);

                $tabs->addTab(array($language->getName() => $this->escapeTabContentId('#' . $tabContent->getName() . '[' . $prefix . 'tab_' . $abbrev . ']')));
                $tabContent->add($pane);
            }

            $this->add($tabContent);
        }

        $this->initAfterTabs();
    }

    /**
     * @return SubForm\TabContent
     */
    private function createTabContent()
    {
        $tabContent = new SubForm\TabContent($this->getPrefix() . 'tab_content');
        $this->initElement($tabContent);

        return $tabContent;
    }

    /**
     * @param  string          $name
     * @return SubForm\TabPane
     */
    private function createTabPane($name)
    {
        $tabPane = new SubForm\TabPane($name);
        $this->initElement($tabPane);

        return $tabPane;
    }

    /**
     * @param FieldsetInterface $element The fieldset to configure
     */
    private function initElement(FieldsetInterface $element)
    {
        $element->init();
        $this->getFormFactory()->configureFieldset($element, array());
    }

    /**
     * @param  string $id The id of the tab content
     * @return string
     */
    private function escapeTabContentId($id)
    {
        return str_replace(array('[', ']'), array('\\[', '\\]'), $id);
    }

    /**
     * @param  string $prefix
     * @return self
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function getPrefix()
    {
        if (null === $this->prefix || '' == $this->prefix)
            return '';
        return $this->prefix . '_';
    }

    /**
     * Take all actions that init() should perform before adding the tabbed fields.
     */
    protected function initBeforeTabs()
    {
        // NOP
    }

    /**
     * Take all actions that init() should perform after adding the tabbed fields.
     */
    protected function initAfterTabs()
    {
        // NOP
    }

    /**
     * @param FieldsetInterface $container The tab
     * @param Language          $language  The language of the tab
     * @param boolean           $isDefault Whether the language is the default langauge
     */
    abstract protected function addTab(FieldsetInterface $container, Language $language, $isDefault);

    protected function getLanguages()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
    }
}
