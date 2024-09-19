<?php

namespace CudiBundle\Form\Booking;

/**
 * Book textbooks
 *
 * @author Niels Avonds<niels.avonds@litus.cc>
 */
class Booking extends \CommonBundle\Component\Form\Bootstrap\Form
{
//    /**
//     * The maximum number allowed to enter in the textbook booking form.
//     */
//    const MAX_BOOKING_NUMBER = 20;

    /**
     * @var array[]
     */
    private $articles = array();

    public function init()
    {
        parent::init();

        foreach ($this->articles as $article) {
            $saleArticle = $article['article'];

            $this->add(
                array(
                    'type'       => 'hidden',
                    'name'       => 'article-' . $saleArticle->getId(),
                    'attributes' => array(
                        'class'       => 'input-very-mini',
                        'id'          => 'article-' . $saleArticle->getId(),
                        'placeholder' => '0',
                    ),
                    'options'    => array(
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
                                        'max' => $this->getEntityManager()
                                            ->getRepository('CommonBundle\Entity\General\Config')
                                            ->getConfigValue('cudi.maximum_booking_number'),
                                    ),
                                ),
                            ),
                        ),
                    ),
                )
            );
        }

        $this->addSubmit('Book', 'btn btn-primary pull-right btn-booking');
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
