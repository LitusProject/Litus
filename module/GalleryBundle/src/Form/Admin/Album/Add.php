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
 
namespace GalleryBundle\Form\Admin\Album;

use CommonBundle\Component\Form\Bootstrap\SubForm\TabContent,
    CommonBundle\Component\Form\Bootstrap\SubForm\TabPane,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Component\Form\Bootstrap\Element\Tabs,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    Doctrine\ORM\EntityManager,
    GalleryBundle\Entity\Album\Album,
    Zend\Validator\Date as DateValidator;

/**
 * Add an album.
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form\Tabbable
{
	/**
	 * @var \Doctrine\ORM\EntityManager The EntityManager instance
	 */
	private $_entityManager = null;
	
	/**
	 * @var \GalleryBundle\Entity\Album\Album
	 */
	protected $album;
	
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
		    
		    $field = new Text('title_' . $language->getAbbrev());
		    $field->setLabel('Title')
		        ->setAttrib('class', $field->getAttrib('class') . ' input-xxlarge')
		        ->setRequired();
		    $pane->addElement($field);
		    
		    $tabContent->addSubForm($pane, 'tab_' . $language->getAbbrev());
		}
		
		$this->addSubForm($tabContent, 'tab-content');
		
		$field = new Text('date');
		$field->setLabel('Date')
		    ->setAttrib('class', $field->getAttrib('class') . ' input-large')
		    ->setRequired()
		    ->addValidator(new DateValidator('dd/MM/yyyy'));
		$this->addElement($field);
        
        $field = new Submit('submit');
        $field->setLabel('Add');
        $this->addElement($field);
        
        $this->setActionsGroup(array('submit'));
    }
    
    public function populateFromAlbum(Album $album)
    {
        $data = array(
            'date' => $album->getDate()->format('d/m/Y'),
        );
        foreach($this->_getLanguages() as $language) {
            $data['title_' . $language->getAbbrev()] = $album->getTitle($language);
        }
        $this->populate($data);
    }
    
    protected function _getLanguages()
    {
        return $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
    }
    
    /**
     * Validate the form
     *
     * @param  array $data
     * @return boolean
     */
    public function isValid($data)
    {
        $valid = parent::isValid($data);
        
        $form = $this->getSubForm('tab-content');
        $date = \DateTime::createFromFormat('d/m/Y', $data['date']);
        
        if ($date) {
            foreach($this->_getLanguages() as $language) {
                $title = $form->getSubForm('tab_' . $language->getAbbrev())->getElement('title_' . $language->getAbbrev());
                $name = $date->format('Ymd') . '_' . str_replace(' ', '_', strtolower($data['title_' . $language->getAbbrev()]));

                $album = $this->_entityManager
                	->getRepository('GalleryBundle\Entity\Album\Translation')
                	->findOneByName($name);

                if (!(null == $album || 
                    (null != $this->album && null != $album && $album->getAlbum() == $this->album))) {
                    $title->addError('This album title already exists');
                    $valid = false;
                }
            }
        }
        
        return $valid;
    }
}
