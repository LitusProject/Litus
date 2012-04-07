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
 
namespace ProfBundle\Form\Prof\Article;

use CommonBundle\Component\Validator\Price as PriceValidator,
	CommonBundle\Component\Validator\Year as YearValidator,
	CudiBundle\Entity\Article,
	Doctrine\ORM\EntityManager,
	CommonBundle\Component\Form\Bootstrap\Element\Checkbox,
	CommonBundle\Component\Form\Bootstrap\Element\Text,
	CommonBundle\Component\Form\Bootstrap\Element\Select,
	CommonBundle\Component\Form\Bootstrap\Element\Submit;

class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{

	/**
	 * @var \Doctrine\ORM\EntityManager The EntityManager instance
	 */
	protected $_entityManager = null;

    public function __construct(EntityManager $entityManager, $opts = null)
    {
        parent::__construct($opts);
        
       	$this->_entityManager = $entityManager;
         
        $field = new Text('title');
        $field->setLabel('Title')
			->setAttrib('size', 70)
            ->setRequired();
        $this->addElement($field);
         
        $field = new Text('author');
        $field->setLabel('Author')
			->setAttrib('size', 60)
        	->setRequired();
        $this->addElement($field);
         
        $field = new Text('publisher');
        $field->setLabel('Publisher')
			->setAttrib('size', 40)
        	->setRequired();
        $this->addElement($field);
         
        $field = new Text('year_published');
        $field->setLabel('Year Published')
        	->setRequired()
			->addValidator('int')
        	->addValidator(new YearValidator());
        $this->addElement($field);

		$field = new Checkbox('stock');
        $field->setLabel('Stock Article');
        $this->addElement($field);

		$this->addDisplayGroup(
			array(
				'title',
		        'author',
		        'publisher',
				'year_published',
				'stock'
		    ),
		    'article_form'
		);
		$this->getDisplayGroup('article_form')
		   	->setLegend('Article')
		    ->setAttrib('id', 'article_form')
		    ->removeDecorator('DtDdWrapper');
         
        $field = new Checkbox('internal');
        $field->setLabel('Internal Article');
        $this->addElement($field);
		
		$this->addDisplayGroup(
			array(
				'internal'
			),
			'stock_form'
		);
		$this->getDisplayGroup('stock_form')
		   	->setLegend('Stock Article')
		    ->setAttrib('id', 'stock_form')
		    ->removeDecorator('DtDdWrapper');

		$field = new Select('binding');
	    $field->setLabel('Binding')
	       	->setRequired()
			->setMultiOptions($this->_getBindings());
	    $this->addElement($field);

	    $field = new Checkbox('rectoverso');
	    $field->setLabel('Recto Verso');
	    $this->addElement($field);
		
		$this->addDisplayGroup(
		            array(
		                'binding',
						'rectoverso',
		            ),
		            'internal_form'
		        );
		$this->getDisplayGroup('internal_form')
	    	->setLegend('Internal Article')
	        ->setAttrib('id', 'internal_form')
	        ->removeDecorator('DtDdWrapper');

        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'btn btn-primary');
        $this->addElement($field);

        $this->setActionsGroup(array('submit'));
    }
	
	private function _getBindings()
	{
		$bindings = $this->_entityManager
	    	->getRepository('CudiBundle\Entity\Articles\StockArticles\Binding')
			->findAll();
		$bindingOptions = array();
		foreach($bindings as $item)
			$bindingOptions[$item->getId()] = $item->getName();
		
		return $bindingOptions;
	}
	
	private function _getColors()
	{
		$colors = $this->_entityManager
			->getRepository('CudiBundle\Entity\Articles\StockArticles\Color')
			->findAll();
		$colorOptions = array();
		foreach($colors as $item)
			$colorOptions[$item->getId()] = $item->getName();
		
		return $colorOptions;
	}
	
	public function populateFromArticle(Article $article)
	{
		$data = array(
			'title' => $article->getTitle(),
			'author' => $article->getMetaInfo()->getAuthors(),
			'publisher' => $article->getMetaInfo()->getPublishers(),
			'year_published' => $article->getMetaInfo()->getYearPublished(),
			'stock' => $article->isStock()
		);
		
		if ($article->isStock()) {
			$data['internal'] = $article->isInternal();
		}
		
		if ($article->isInternal()) {
			$data['binding'] = $article->getBinding()->getId();
			$data['rectoverso'] = $article->isRectoVerso();
		}
						
		$this->populate($data);
	}
	
	public function isValid($data)
	{
		if (!$data['stock']) {
			$validatorsStock = array();
			$requiredStock = array();
		    
			foreach ($this->getDisplayGroup('stock_form')->getElements() as $formElement) {
				$validatorsStock[$formElement->getName()] = $formElement->getValidators();
				$requiredStock[$formElement->getName()] = $formElement->isRequired();
				$formElement->clearValidators()
					->setRequired(false);
			}
		}
		
		if (!$data['internal']) {
			$validatorsInternal = array();
			$requiredInternal = array();
		    
			foreach ($this->getDisplayGroup('internal_form')->getElements() as $formElement) {
				$validatorsInternal[$formElement->getName()] = $formElement->getValidators();
				$requiredInternal[$formElement->getName()] = $formElement->isRequired();
				$formElement->clearValidators()
					->setRequired(false);
			}
		}
		
		$isValid = parent::isValid($data);
		
		if (!$data['stock']) {
			foreach ($this->getDisplayGroup('stock_form')->getElements() as $formElement) {
				if (array_key_exists ($formElement->getName(), $validatorsStock))
		 			$formElement->setValidators($validatorsStock[$formElement->getName()]);
				if (array_key_exists ($formElement->getName(), $requiredStock))
					$formElement->setRequired($requiredStock[$formElement->getName()]);
			}
		}
		
		if (!$data['internal']) {
			foreach ($this->getDisplayGroup('internal_form')->getElements() as $formElement) {
				if (array_key_exists ($formElement->getName(), $validatorsInternal))
		 			$formElement->setValidators($validatorsInternal[$formElement->getName()]);
				if (array_key_exists ($formElement->getName(), $requiredInternal))
					$formElement->setRequired($requiredInternal[$formElement->getName()]);
			}
		}
		
		return $isValid;
	}
}