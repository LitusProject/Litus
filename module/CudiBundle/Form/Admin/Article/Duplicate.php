<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Form\Admin\Article;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CudiBundle\Entity\Article,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Duplicate of Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Duplicate extends \CudiBundle\Form\Admin\Article\Add
{
    /**
     * @var \CudiBundle\Entity\Article
     */
    private $_article;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CudiBundle\Entity\Article $article
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Article $article, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->_article = $article;

        $this->remove('submit');

        $this->remove('subject');

        $this->get('article')->get('type')->setAttribute('disabled', 'disabled');

        if ($article->getType() == 'common') {
            $this->get('article')
                ->remove('type');
        }

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'article_add');
        $this->add($field);

        $this->populateFromArticle($article);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();
        $factory = new InputFactory();

        $inputFilter->remove('type');

        $inputFilter->remove('subject');
        $inputFilter->remove('subject_id');

        return $inputFilter;
    }
}
