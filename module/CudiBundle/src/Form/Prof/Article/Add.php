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

namespace CudiBundle\Form\Prof\Article;

use CommonBundle\Component\Validator\Uri as UriValidator,
    CommonBundle\Component\Validator\Year as YearValidator,
    CudiBundle\Entity\Article,
    Doctrine\ORM\EntityManager,
    CommonBundle\Component\Form\Bootstrap\Element\Checkbox,
    CommonBundle\Component\Form\Bootstrap\Element\Collection,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    Zend\Form\Element\Hidden,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $article = new Collection('article');
        $article->setLabel('Article')
            ->setAttribute('id', 'article_form');
        $this->add($article);

        $field = new Text('title');
        $field->setLabel('Title')
            ->setAttribute('style', 'width:400px')
            ->setRequired();
        $article->add($field);

        $field = new Text('author');
        $field->setLabel('Authors')
            ->setAttribute('style', 'width:350px')
            ->setRequired();
        $article->add($field);

        $field = new Text('publisher');
        $field->setLabel('Publisher')
            ->setAttribute('style', 'width:300px')
            ->setRequired();
        $article->add($field);

        $field = new Text('year_published');
        $field->setLabel('Publish Year');
        $article->add($field);

        $field = new Text('isbn');
        $field->setLabel('ISBN');
        $article->add($field);

        $field = new Text('url');
        $field->setLabel('URL');
        $article->add($field);

        $field = new Checkbox('downloadable');
        $field->setLabel('Downloadable');
        $article->add($field);

        $types = Article::$POSSIBLE_TYPES;
        unset($types['common']);
        $field = new Select('type');
        $field->setLabel('Type')
            ->setAttribute('options', $types);
        $article->add($field);

        $field = new Checkbox('internal');
        $field->setLabel('Internal Article');
        $article->add($field);

        $internal = new Collection('internal');
        $internal->setLabel('Internal Article')
            ->setAttribute('id', 'internal_form');
        $this->add($internal);

        $field = new Select('binding');
        $field->setLabel('Binding')
            ->setAttribute('options', $this->_getBindings());
        $internal->add($field);

        $field = new Checkbox('rectoverso');
        $field->setLabel('Recto Verso');
        $internal->add($field);

        $field = new Checkbox('perforated');
        $field->setLabel('Perforated');
        $internal->add($field);

        $subject = new Collection('subject');
        $subject->setLabel('Subject Mapping')
            ->setAttribute('id', 'subject_form');
        $this->add($subject);

        $field = new Hidden('subject_id');
        $field->setAttribute('id', 'subjectId');
        $subject->add($field);

        $field = new Text('subject');
        $field->setLabel('Subject')
            ->setAttribute('style', 'width:400px')
            ->setAttribute('id', 'subjectSearch')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('data-provide', 'typeahead')
            ->setRequired();
        $subject->add($field);

        $field = new Checkbox('mandatory');
        $field->setLabel('Mandatory');
        $subject->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'btn btn-primary');
        $this->add($field);
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
            $data['binding'] = $article->getBinding()->getId();
            $data['rectoverso'] = $article->isRectoVerso();
            $data['perforated'] = $article->isPerforated();
        }

        $this->setData($data);
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'title',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'author',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'publisher',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'year',
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'int',
                            ),
                            new YearValidator(),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'isbn',
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'isbn',
                                'options' => array(
                                    'type' => 'auto'
                                ),
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'url',
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            new UriValidator(),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'type',
                        'required' => true,
                    )
                )
            );

            if (isset($this->data['internal']) && $this->data['internal']) {
                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name'     => 'binding',
                            'required' => true,
                        )
                    )
                );
            }

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'subject_id',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'int',
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'subject',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}