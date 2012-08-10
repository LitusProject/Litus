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
    
use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    CommonBundle\Component\Validator\Uri as UriValidator,
    CommonBundle\Component\Validator\Year as YearValidator,
    CudiBundle\Component\Validator\SubjectCode as SubjectCodeValidator,
    CudiBundle\Entity\Article,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Checkbox,
    Zend\Form\Element\Hidden,
    Zend\Form\Element\Select,
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text,
    Zend\Validator\Int as IntValidator,
    Zend\Validator\Isbn as IsbnValidator;

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
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator('int')
            ->addValidator(new YearValidator());
        $this->addElement($field);
        
        $field = new Text('isbn');
        $field->setLabel('ISBN')
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new IsbnValidator(array('type' => IsbnValidator::AUTO)));
        $this->addElement($field);
        
        $field = new Text('url');
        $field->setLabel('URL')
            ->setDecorators(array(new FieldDecorator()))
            ->addValidator(new UriValidator());
        $this->addElement($field);
        
        $field = new Checkbox('downloadable');
        $field->setLabel('Downloadable')
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
        
        $field = new Select('type');
        $field->setLabel('Type')
               ->setRequired()
            ->setMultiOptions(Article::$POSSIBLE_TYPES)
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Checkbox('internal');
        $field->setLabel('Internal Article')
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
                'downloadable',
                'type',
                'internal',
            ),
            'article_form'
        );
        $this->getDisplayGroup('article_form')
               ->setLegend('Article')
            ->setAttrib('id', 'article_form')
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
                        'perforated',
                    ),
                    'internal_form'
                );
        $this->getDisplayGroup('internal_form')
            ->setLegend('Internal Article')
            ->setAttrib('id', 'internal_form')
            ->removeDecorator('DtDdWrapper');
            
        $field = new Hidden('subject_id');
        $field->addValidator(new IntValidator())
            ->setAttrib('id', 'subjectId')
            ->clearDecorators()
            ->setDecorators(array('ViewHelper'));
        $this->addElement($field);
         
        $field = new Text('subject');
        $field->setLabel('Subject')
            ->setAttrib('size', 70)
            ->setAttrib('id', 'subjectSearch')
            ->setAttrib('autocomplete', 'off')
            ->setAttrib('data-provide', 'typeahead')
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
         
        $field = new Checkbox('mandatory');
        $field->setLabel('Mandatory')
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);
        
        $this->addDisplayGroup(
                    array(
                        'subject_id',
                        'subject',
                        'mandatory',
                    ),
                    'subject_form'
                );
        $this->getDisplayGroup('subject_form')
            ->setLegend('Subject Mapping')
            ->setAttrib('id', 'subject_form')
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
            'downloadable' => $article->isDownloadable(),
            'type' => $article->getType(),
            'internal' => $article->isInternal()
        );
        
        if ($article->isInternal()) {
            $data['nb_black_and_white'] = $article->getNbBlackAndWhite();
            $data['nb_colored'] = $article->getNbColored();
            $data['binding'] = $article->getBinding()->getId();
            $data['official'] = $article->isOfficial();
            $data['rectoverso'] = $article->isRectoVerso();
            $data['front_color'] = $article->getFrontColor()->getId();
            $data['perforated'] = $article->isPerforated();
        }
                        
        $this->populate($data);
    }
    
    public function isValid($data)
    {
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
        
        if (isset($data['type']) && $data['type'] !== 'common') {
            if ($data['subject_id'] == '' && $this->getElement('subject')) {
                $this->getElement('subject')
                    ->setRequired()
                    ->addValidator(new SubjectCodeValidator($this->_entityManager));
            }
        }
        
        $isValid = parent::isValid($data);
        
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
