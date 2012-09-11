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

namespace CudiBundle\Form\Reservation;

use    CommonBundle\Component\Form\Admin\Element\Text,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Book textbooks
 *
 * @author Niels Avonds<niels.avonds@litus.cc>
 */
class Reservation extends \CommonBundle\Component\Form\Bootstrap\Form
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

        $field = new Submit('submit');
        $field->setValue('Book Textbooks')
            ->setAttribute('class', 'btn btn-primary pull-right');
        $this->add($field);
    }

    public function addInputsForArticles($articles)
    {
        foreach ($articles as $article) {
            $saleArticle = $article['article'];
            
            $name = 'article-' . $saleArticle->getId();
            echo "name = " . $name;
            $field = new Text($name);
            $field->setAttribute('class', 'input-very-mini')
                ->setAttribute('placeholder', '0');
            $this->add($field);
        }
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        
        
        
        return $inputFilter;
    }
}
