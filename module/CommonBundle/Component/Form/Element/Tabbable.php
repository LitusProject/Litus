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

namespace CommonBundle\Component\Form\Element;

use CommonBundle\Component\Form\Element\TabContent;
use CommonBundle\Component\Form\Element\TabPane;
use CommonBundle\Component\Form\FieldsetInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\DoctrineTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use CommonBundle\Entity\General\Language;
use Locale;
use RuntimeException;
use Zend\Form\FormInterface;

/**
 * Extending Zend's fieldset component, so that our forms look the way we want
 * them to.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
abstract class Tabbable extends \CommonBundle\Component\Form\Fieldset implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    use DoctrineTrait;

    public function init()
    {
        $languages = $this->getLanguages();

        if (count($languages) == 0) {
            throw new RuntimeException('No languages found!');
        }

        if (count($languages) == 1) {
            $this->initBeforeTabs();
            $this->addTab($this, $languages[0], true);
        } else {
            $defaultLanguage = Locale::getDefault();

            $this->add(
                array(
                    'type'       => 'tabs',
                    'name'       => 'languages',
                    'attributes' => array(
                        'id' => 'languages',
                    ),
                )
            );

            $tabs = $this->get('languages');
            $tabContent = $this->createTabContent();

            $this->initBeforeTabs();

            foreach ($languages as $language) {
                $abbrev = $language->getAbbrev();
                $pane = $this->createTabPane($tabContent, 'tab_' . $abbrev);

                $this->addTab($pane, $language, $abbrev == $defaultLanguage);

                $tabs->addTab(
                    array(
                        $language->getName() => '[' . $tabContent->getName() . '][' . 'tab_' . $abbrev . ']'
                    )
                );
            }
        }

        $this->initAfterTabs();
    }

    /**
     * @return array
     */
    protected function getLanguages()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
    }

    /**
     * @return TabContent
     */
    private function createTabContent()
    {
        $this->add(
            array(
                'type' => 'tabcontent',
                'name' => 'tab_content',
            )
        );

        return $this->get('tab_content');
    }

    /**
     * @param  TabContent $tabContent
     * @param  string     $name
     * @return TabPane
     */
    private function createTabPane(TabContent $tabContent, $name)
    {
        $tabContent->add(
            array(
                'type' => 'tabpane',
                'name' => $name,
            )
        );

        return $tabContent->get($name);
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
     * Prepare the form element (mostly used for rendering purposes)
     *
     * @param  FormInterface $form
     * @return mixed
     */
    public function prepareElement(FormInterface $form)
    {
        $tabs = $this->get('languages')->getAttribute('tabs');
        foreach ($tabs as $language => $tab) {
            $tabs[$language] = $this->escapeTabContentId('#' . $this->getName() . $tab);
        }

        $this->get('languages')->setAttribute('tabs', $tabs);

        parent::prepareElement($form);

        $this->get('languages')->setAttribute('id', $this->get('languages')->getName());
    }

    /**
     * Take all actions that init() should perform before adding the tabbed fields.
     */
    protected function initBeforeTabs()
    {
    }

    /**
     * Take all actions that init() should perform after adding the tabbed fields.
     */
    protected function initAfterTabs()
    {
    }

    /**
     * @param  FieldsetInterface $container The tab
     * @param  Language          $language  The language of the tab
     * @param  boolean           $isDefault Whether the language is the default langauge
     * @return null
     */
    abstract protected function addTab(FieldsetInterface $container, Language $language, $isDefault);
}
