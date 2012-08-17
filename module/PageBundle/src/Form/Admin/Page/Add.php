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

namespace PageBundle\Form\Admin\Page;

use CommonBundle\Component\Form\Bootstrap\SubForm\TabContent,
    CommonBundle\Component\Form\Bootstrap\SubForm\TabPane,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Component\Form\Bootstrap\Element\Tabs,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    CommonBundle\Component\Form\Bootstrap\Element\Textarea,
    Doctrine\ORM\EntityManager,
    PageBundle\Component\Validator\Name as PageNameValidator,
    PageBundle\Entity\Nodes\Page;

/**
 * Add a page.
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form\Tabbable
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param mixed $opts The validator's options
     */
    public function __construct(EntityManager $entityManager, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;

        $tabs = new Tabs('languages');
        $this->addElement($tabs);

        $tabContent = new TabContent();

        foreach($this->_getLanguages() as $language) {
            $tabs->addTab(array($language->getName() => '#tab_' . $language->getAbbrev()));

            $pane = new TabPane('tab_' . $language->getAbbrev());

            $title = new Text('title_' . $language->getAbbrev());
            $title->setLabel('Title')
                ->setAttrib('class', $title->getAttrib('class') . ' input-xxlarge')
                ->setRequired()
                ->addValidator(new PageNameValidator($entityManager));
            $pane->addElement($title);

            $content = new Textarea('content_' . $language->getAbbrev());
            $content->setLabel('Content')
                ->setRequired()
                ->setAttrib('rows', 20);
            $pane->addElement($content);

            $tabContent->addSubForm($pane, 'tab_' . $language->getAbbrev());
        }

        $this->addSubForm($tabContent, 'tab-content');

        $field = new Submit('submit');
        $field->setLabel('Add');
        $this->addElement($field);

        $this->setActionsGroup(array('submit'));
    }

    public function populateFromPage(Page $page)
    {
        $data = array();
        foreach($this->_getLanguages() as $language) {
            $data['content_' . $language->getAbbrev()] = $page->getContent($language);
            $data['title_' . $language->getAbbrev()] = $page->getTitle($language);
        }
        $this->populate($data);
    }

    protected function _getLanguages()
    {
        return $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
    }
}
