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

namespace CudiBundle\Form\Admin\Sales\Article;

use CudiBundle\Entity\Sale\Article,
    Doctrine\ORM\EntityManager;
/**
 * View Sale Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class View extends \CudiBundle\Form\Admin\Sales\Article\Edit
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CudiBundle\Entity\Sale\Article $article
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Article $article, $name = null)
    {
        parent::__construct($entityManager, $article, $name);

        foreach($this->getElements() as $element) {
            $element->setAttribute('disabled', 'disabled');
        }

        $this->remove('submit');
    }
}