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

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    CommonBundle\Component\Form\Admin\Element\Tabs,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabContent,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabPane,
    DateTime,
    Doctrine\ORM\EntityManager,
    NewsBundle\Entity\Nodes\News,
    Zend\Form\Element\Select,
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text,
    Zend\Form\Element\Textarea;

/**
 * Add News
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;
    
    /**
     * @var \NewsBundle\Entity\Nodes\News
     */
    protected $news;
    
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
        
        foreach($this->getLanguages() as $language) {
            $tabs->addTab(array($language->getName() => '#tab_' . $language->getAbbrev()));
            
            $pane = new TabPane('tab_' . $language->getAbbrev());
            
            $title = new Text('title_' . $language->getAbbrev());
            $title->setLabel('Title')
                ->setRequired()
                ->setDecorators(array(new FieldDecorator()));
            $pane->addElement($title);
            
            $content = new Textarea('content_' . $language->getAbbrev());
            $content->setLabel('Content')
                ->setRequired()
                ->setAttrib('rows', 20)
                ->setDecorators(array(new FieldDecorator()));
            $pane->addElement($content);
            
            $tabContent->addSubForm($pane, 'tab_' . $language->getAbbrev());
        }
        
        $this->addSubForm($tabContent, 'tab_content');
        
        $field = new Submit('submit');
        $field->setLabel('Add')
            ->setAttrib('class', 'news_add')
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
        $date = new DateTime();
        
        if ($date) {
            foreach($this->getLanguages() as $language) {
                $title = $form->getSubForm('tab_' . $language->getAbbrev())->getElement('title_' . $language->getAbbrev());
                $name = $date->format('Ymd') . '_' . str_replace(' ', '_', strtolower($data['title_' . $language->getAbbrev()]));

                $news = $this->_entityManager
                    ->getRepository('NewsBundle\Entity\Nodes\Translation')
                    ->findOneByName($name);

                if (!(null == $news || (null != $this->news && null != $news && $news->getNews() == $this->news))) {
                    $title->addError('This news item already exists');
                    $valid = false;
                }
            }
        }
        
        return $valid;
    }
}
