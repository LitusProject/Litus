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

namespace CudiBundle\Form\Booking;

/**
 * Book textbooks
 *
 * @author Niels Avonds<niels.avonds@litus.cc>
 */
class Booking extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * The maximum number allowed to enter in the textbook booking form.
     */
    const MAX_BOOKING_NUMBER = 5;

    /**
     * @var array[]
     */
    private $articles = array();

    public function init()
    {
        parent::init();

        foreach ($this->articles as $article) {
            $saleArticle = $article['article'];

            $this->add(array(
                'type'       => 'hidden',
                'name'       => 'article-' . $saleArticle->getId(),
                'attributes' => array(
                    'class'       => 'input-very-mini',
                    'id'          => 'article-' . $saleArticle->getId(),
                    'placeholder' => '0',
                ),
                'options'    => array(
                    'input' => array(
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'digits',
                            ),
                            array(
                                'name' => 'between',
                                'options' => array(
                                    'min' => 0,
                                    'max' => self::MAX_BOOKING_NUMBER,
                                ),
                            ),
                        ),
                    ),
                ),
            ));
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
