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

namespace CudiBundle\Form\Prof\Article;

use CudiBundle\Entity\Article,
    Doctrine\ORM\EntityManager,
    CommonBundle\Component\Form\Bootstrap\Element\Submit;

/**
 * Edit Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CudiBundle\Entity\Article $article
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Article $article, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->remove('subject');
        $this->remove('submit');
        $this->remove('draft');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'btn btn-primary');
        $this->add($field);

        $field = new Submit('draft');
        $field->setValue('Save As Draft')
            ->setAttribute('class', 'btn btn-info');
        $this->add($field);

        $this->populateFromArticle($article);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();

        $inputFilter->remove('subject');
        $inputFilter->remove('subject_id');

        return $inputFilter;
    }
}
