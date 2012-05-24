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
 
namespace CudiBundle\Form\Admin\Article;

/*use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	CommonBundle\Component\Validator\Price as PriceValidator,
	CommonBundle\Component\Validator\Year as YearValidator,
	CudiBundle\Component\Validator\UniqueArticleBarcode as UniqueArticleBarcodeValidator,
	CudiBundle\Component\Validator\Barcode as BarcodeValidator,
	CudiBundle\Entity\Article,
	Doctrine\ORM\EntityManager,
	Zend\Form\Element\Submit,
	Zend\Form\Element\Text,
	Zend\Form\Element\Select,
	Zend\Form\Element\Checkbox;*/
	
use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
	CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
	CommonBundle\Component\Validator\Uri as UriValidator,
	CommonBundle\Component\Validator\Year as YearValidator,
	CudiBundle\Component\Validator\ISBN as ISBNValidator,
	CudiBundle\Entity\Article,
	Doctrine\ORM\EntityManager,
	Zend\Form\Element\Checkbox,
    Zend\Form\Element\Select,
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text;

/**
 * Add Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
 class Add extends \CommonBundle\Component\Form\Admin\Form
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
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
         
        $field = new Text('author');
        $field->setLabel('Author')
			->setAttrib('size', 60)
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
         
        $field = new Text('publisher');
        $field->setLabel('Publisher')
			->setAttrib('size', 40)
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
         
        $field = new Text('year_published');
        $field->setLabel('Year Published')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()))
			->addValidator('int')
        	->addValidator(new YearValidator());
        $this->addElement($field);
        
        $field = new Text('isbn');
        $field->setLabel('ISBN')
        	->setRequired()
        	->setDecorators(array(new FieldDecorator()))
        	->addValidator('isbn');
        $this->addElement($field);
        
        $field = new Text('url');
        $field->setLabel('URL')
        	->setDecorators(array(new FieldDecorator()))
        	->addValidator(new UriValidator());
        $this->addElement($field);

		$field = new Checkbox('stock');
        $field->setLabel('Stock Article')
        	->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

		$this->addDisplayGroup(
			array(
				'title',
		        'author',
		        'publisher',
				'year_published',
				'isbn',
				'url',
				'stock'
		    ),
		    'article_form'
		);
		$this->getDisplayGroup('article_form')
		   	->setLegend('Article')
		    ->setAttrib('id', 'article_form')
		    ->removeDecorator('DtDdWrapper');
         
        $field = new Checkbox('internal');
        $field->setLabel('Internal Article')
        	->setDecorators(array(new FieldDecorator()));
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

		$field = new Text('nb_black_and_white');
	    $field->setLabel('Number of B/W Pages')
	        ->setRequired()
	        ->addValidator('int')
	        ->setDecorators(array(new FieldDecorator()));
	    $this->addElement($field);

	    $field = new Text('nb_colored');
	    $field->setLabel('Number of Colored Pages')
	        ->setRequired()
	        ->addValidator('int')
	        ->setDecorators(array(new FieldDecorator()));
	    $this->addElement($field);

		$field = new Select('binding');
	    $field->setLabel('Binding')
	       	->setRequired()
			->setMultiOptions($this->_getBindings())
	        ->setDecorators(array(new FieldDecorator()));
	    $this->addElement($field);

	    $field = new Checkbox('official');
	    $field->setLabel('Official')
	        ->setDecorators(array(new FieldDecorator()));
	    $this->addElement($field);

	    $field = new Checkbox('rectoverso');
	    $field->setLabel('Recto Verso')
	        ->setDecorators(array(new FieldDecorator()));
	    $this->addElement($field);

		$field = new Select('front_color');
	    $field->setLabel('Front Page Color')
	      	->setRequired()
			->setMultiOptions($this->_getColors())
	       	->setDecorators(array(new FieldDecorator()));
	    $this->addElement($field);
	    
	    $field = new Checkbox('front_text_colored');
	    $field->setLabel('Front Page Text Colored')
	        ->setDecorators(array(new FieldDecorator()));
	    $this->addElement($field);
	    
	    $field = new Checkbox('perforated');
	    $field->setLabel('Perforated')
	        ->setDecorators(array(new FieldDecorator()));
	    $this->addElement($field);
		
		$this->addDisplayGroup(
		            array(
		                'nb_black_and_white',
		                'nb_colored',
		                'binding',
						'official',
						'rectoverso',
						'front_color',
						'front_text_colored',
						'perforated',
		            ),
		            'internal_form'
		        );
		$this->getDisplayGroup('internal_form')
	    	->setLegend('Internal Article')
	        ->setAttrib('id', 'internal_form')
	        ->removeDecorator('DtDdWrapper');

        $field = new Submit('submit');
        $field->setLabel('Add')
                ->setAttrib('class', 'article_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }
	
	private function _getBindings()
	{
		$bindings = $this->_entityManager
	    	->getRepository('CudiBundle\Entity\Articles\Options\Binding')
			->findAll();
		$bindingOptions = array();
		foreach($bindings as $item)
			$bindingOptions[$item->getId()] = $item->getName();
		
		return $bindingOptions;
	}
	
	private function _getColors()
	{
		$colors = $this->_entityManager
			->getRepository('CudiBundle\Entity\Articles\Options\Color')
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
			'author' => $article->getAuthors(),
			'publisher' => $article->getPublishers(),
			'year_published' => $article->getYearPublished(),
			'isbn' => $article->getISBN(),
			'url' => $article->getURL(),
			'stock' => $article->isStock()
		);
		
		if ($article->isStock()) {
			$data['internal'] = $article->isInternal();
			
			if ($article->isInternal()) {
				$data['nb_black_and_white'] = $article->getNbBlackAndWhite();
				$data['nb_colored'] = $article->getNbColored();
				$data['binding'] = $article->getBinding()->getId();
				$data['official'] = $article->isOfficial();
				$data['rectoverso'] = $article->isRectoVerso();
				$data['front_color'] = $article->getFrontColor()->getId();
			}
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