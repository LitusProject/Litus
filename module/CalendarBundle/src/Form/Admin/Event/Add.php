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
 
namespace CalendarBundle\Form\Admin\Event;

use CommonBundle\Component\Form\Bootstrap\SubForm\TabContent,
    CommonBundle\Component\Form\Bootstrap\SubForm\TabPane,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Component\Form\Bootstrap\Element\Tabs,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    CommonBundle\Component\Form\Bootstrap\Element\TextArea,
    Doctrine\ORM\EntityManager,
    CalendarBundle\Component\Validator\DateCompare as DateCompareValidator,
    CalendarBundle\Entity\Nodes\Event,
    Zend\Validator\Date as DateValidator;

/**
 * Add an event.
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
		
		$field = new Text('start_date');
		$field->setLabel('Start Date')
		    ->setAttrib('class', $field->getAttrib('class') . ' input-large')
		    ->setRequired()
		    ->addValidator(new DateValidator('dd/MM/yyyy H:m'));
		$this->addElement($field);
		
		$field = new Text('end_date');
		$field->setLabel('End Date')
		    ->setAttrib('class', $field->getAttrib('class') . ' input-large')
		    ->addValidator(new DateCompareValidator('start_date', 'd/m/Y H:i'))
		    ->addValidator(new DateValidator('dd/MM/yyyy H:m'));
		$this->addElement($field);
		
		$tabs = new Tabs('languages');
		$this->addElement($tabs);
		
		$tabContent = new TabContent();
		
		foreach($this->_getLanguages() as $language) {
		    $tabs->addTab(array($language->getName() => '#tab_' . $language->getAbbrev()));
		    
		    $pane = new TabPane('tab_' . $language->getAbbrev());
		    
		    $field = new Text('location_' . $language->getAbbrev());
		    $field->setLabel('Location')
		        ->setAttrib('class', $field->getAttrib('class') . ' input-xlarge')
		        ->setRequired();
		    $pane->addElement($field);
		    
		    $field = new Text('title_' . $language->getAbbrev());
		    $field->setLabel('Title')
		        ->setAttrib('class', $field->getAttrib('class') . ' input-xxlarge')
		        ->setRequired();
		    $pane->addElement($field);
		    
		    $field = new TextArea('content_' . $language->getAbbrev());
		    $field->setLabel('Content')
		        ->setAttrib('class', $field->getAttrib('class') . ' input-xxlarge')
		        ->setRequired();
		    $pane->addElement($field);
		    
		    $tabContent->addSubForm($pane, 'tab_' . $language->getAbbrev());
		}
		
		$this->addSubForm($tabContent, 'tab-content');
        
        $field = new Submit('submit');
        $field->setLabel('Add');
        $this->addElement($field);
        
        $this->setActionsGroup(array('submit'));
    }
    
    public function populateFromEvent(Event $event)
    {
        $data = array(
            'start_date' => $event->getStartDate()->format('d/m/Y H:i'),
        );
        if ($event->getEndDate())
            $data['end_date'] = $event->getEndDate()->format('d/m/Y H:i');
        
        foreach($this->_getLanguages() as $language) {
            $data['location_' . $language->getAbbrev()] = $event->getLocation($language);
            $data['title_' . $language->getAbbrev()] = $event->getTitle($language);
            $data['content_' . $language->getAbbrev()] = $event->getContent($language);
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