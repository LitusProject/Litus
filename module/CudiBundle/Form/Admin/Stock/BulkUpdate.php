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

namespace CudiBundle\Form\Admin\Stock;

/**
 * Bulk Update the Stock
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class BulkUpdate extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var array
     */
    private $articles = array();

    public function init()
    {
        parent::init();

        foreach ($this->articles as $article) {
            $this->add(array(
                'type'       => 'text',
                'name'       => 'article_' . $article->getId(),
                'value'      => $article->getStockValue(),
                'attributes' => array(
                    'style' => 'width: 70px;',
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
                        ),
                    ),
                ),
            ));
        }

        $this->addSubmit('Save', 'edit');
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
