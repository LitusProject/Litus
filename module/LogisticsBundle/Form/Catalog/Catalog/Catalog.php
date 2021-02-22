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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace LogisticsBundle\Form\Catalog\Catalog;

/**
 * Book articles
 *
 */
class Catalog extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var array[]
     */
    private $articles = array();

    public function init()
    {
        parent::init();

        foreach ($this->articles as $article) {
            $mapping = $article['article'];

            $this->add(
                array(
                    'type'       => 'hidden',
                    'name'       => 'article-' . $mapping->getId(),
                    'attributes' => array(
                        'class'       => 'input-very-mini',
                        'id'          => 'article-' . $mapping->getId(),
                        'placeholder' => '0',
                    ),
                    'options' => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array(
                                    'name' => 'Digits',
                                ),
                                array(
                                    'name'    => 'Between',
                                    'options' => array(
                                        'min' => 0,
                                        'max' => $mapping->getAmountAvailable(),
                                    ),
                                ),
                            ),
                        ),
                    ),
                )
            );
        }

        $this->addSubmit('Book', 'btn btn-primary pull-right');
    }

    /**
     * @param  array $articles
     * @return self
     */
    public function setArticles(array $articles)
    {
        $this->articles = $articles;

        return $this;
    }
}
