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

namespace CudiBundle\Form\Admin\Prof\Article;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Collection,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\Uri as UriValidator,
    CommonBundle\Component\Validator\Year as YearValidator,
    CudiBundle\Entity\Article,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Confirm Article add action
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
 class Confirm extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param Article         $article
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Article $article, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $articleCollection = new Collection('article');
        $articleCollection->setLabel('Article')
            ->setAttribute('id', 'article_form');
        $this->add($articleCollection);

        $field = new Text('title');
        $field->setLabel('Title')
            ->setAttribute('size', 70)
            ->setRequired();
        $articleCollection->add($field);

        $field = new Text('author');
        $field->setLabel('Author')
            ->setAttribute('size', 60)
            ->setRequired();
        $articleCollection->add($field);

        $field = new Text('publisher');
        $field->setLabel('Publisher')
            ->setAttribute('size', 40)
            ->setRequired();
        $articleCollection->add($field);

        $field = new Text('year_published');
        $field->setLabel('Year Published')
            ->setRequired();
        $articleCollection->add($field);

        $field = new Text('isbn');
        $field->setLabel('ISBN');
        $articleCollection->add($field);

        $field = new Text('url');
        $field->setLabel('URL');
        $articleCollection->add($field);

        $field = new Checkbox('downloadable');
        $field->setLabel('Downloadable')
            ->setAttribute('data-help', 'Enabling this flag will warn the students this article is also downloadable on the website of the subject.');
        $articleCollection->add($field);

        $field = new Checkbox('same_as_previous_year');
        $field->setLabel('Same As Previous Year')
            ->setAttribute('data-help', 'This flag can be enabled by a docent in \'Prof App\', by this it is possible to show the owners of the store the article is the same as previous year.');
        $articleCollection->add($field);

        $field = new Select('type');
        $field->setLabel('Type')
            ->setRequired()
            ->setAttribute('options', Article::$POSSIBLE_TYPES)
            ->setAttribute('data-help', 'The type of the article can be:
            <ul>
                <li><b>Common:</b> an article which is not mapped to a subject</li>
                <li><b>Exercises:</b> an article related to exercises</li>
                <li><b>Notes:</b> notes of the course</li>
                <li><b>Slides:</b> slides of the course</li>
                <li><b>Student:</b> an unofficial article of the course (made by students)</li>
                <li><b>Textbook:</b> a textbook of the course</li>
                <li><b>Other:</b> any other type</li>
            </ul>');
        $articleCollection->add($field);

        $field = new Checkbox('internal');
        $field->setLabel('Internal Article')
            ->setAttribute('data-help', 'Enabling this flag will show extra options for articles that will be printed by the owners of the store. Articles that are printed by and bought from another supplier doesn\'t need these options.');
        $articleCollection->add($field);

        $internal = new Collection('internal');
        $internal->setLabel('Internal Article')
            ->setAttribute('id', 'internal_form');
        $this->add($internal);

        $field = new Text('nb_black_and_white');
        $field->setLabel('Number of B/W Pages')
            ->setRequired();
        $internal->add($field);

        $field = new Text('nb_colored');
        $field->setLabel('Number of Colored Pages')
            ->setRequired();
        $internal->add($field);

        $field = new Select('binding');
        $field->setLabel('Binding')
            ->setRequired()
            ->setAttribute('options', $this->_getBindings());
        $internal->add($field);

        $field = new Checkbox('official');
        $field->setLabel('Official');
        $internal->add($field);

        $field = new Checkbox('rectoverso');
        $field->setLabel('Recto Verso');
        $internal->add($field);

        $field = new Select('front_color');
        $field->setLabel('Front Page Color')
            ->setRequired()
            ->setAttribute('options', $this->_getColors());
        $internal->add($field);

        $field = new Checkbox('perforated');
        $field->setLabel('Perforated');
        $internal->add($field);

        $field = new Checkbox('colored');
        $field->setLabel('Colored');
        $internal->add($field);

        $field = new Submit('submit');
        $field->setValue('Confirm')
            ->setAttribute('class', 'article_add');
        $this->add($field);

        $this->populateFromArticle($article);
    }

    private function _getBindings()
    {
        $bindings = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Article\Option\Binding')
            ->findAll();
        $bindingOptions = array();
        foreach($bindings as $item)
            $bindingOptions[$item->getId()] = $item->getName();

        return $bindingOptions;
    }

    private function _getColors()
    {
        $colors = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Article\Option\Color')
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
            'same_as_previous_year' => $article->isSameAsPreviousYear(),
            'type' => $article->getType(),
            'internal' => $article->isInternal()
        );

        if ($article->isInternal()) {
            $data['nb_black_and_white'] = '';
            $data['nb_colored'] = '';
            $data['front_color'] = 0;
            $data['binding'] = $article->getBinding()->getId();
            $data['official'] = $article->isOfficial();
            $data['rectoverso'] = $article->isRectoVerso();
            $data['perforated'] = $article->isPerforated();
            $data['colored'] = $article->isColored();
        }

        $this->setData($data);
    }

    public function getInputFilter()
    {
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
                        'name'     => 'nb_black_and_white',
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
                        'name'     => 'nb_colored',
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
                        'name'     => 'binding',
                        'required' => true,
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'front_color',
                        'required' => true,
                    )
                )
            );
        }

        return $inputFilter;
    }
}
