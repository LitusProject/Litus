<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace CudiBundle\Form\Admin\Sales\Article;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Entity\General\AcademicYear as AcademicYear,
    CudiBundle\Entity\Sales\Article,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit;

/**
 * Activate Sale Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Activate extends \CudiBundle\Form\Admin\Sales\Article\Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     * @param \CudiBundle\Entity\Sales\Article $article
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, AcademicYear $academicYear, Article $article, $name = null)
    {
        parent::__construct($entityManager, $academicYear, $name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Activate')
            ->setAttribute('class', 'article_edit');
        $this->add($field);

        $this->populateFromArticle($article);
    }
}
