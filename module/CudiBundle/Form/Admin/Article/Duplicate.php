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

namespace CudiBundle\Form\Admin\Article;

/**
 * Duplicate of Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Duplicate extends \CudiBundle\Form\Admin\Article\Add
{
    public function init()
    {
        parent::init();

        $this->remove('subject_form');

        $articleFieldset = $this->get('article');
        $articleFieldset->get('type')
            ->setAttribute('disabled', true);

        if ($this->article->getType() == 'common') {
            $articleFieldset->remove('type');
        }

        $this->remove('submit')
            ->addSubmit('Add', 'article_add');

        // Don't bind to the article, but extract its data
        $this->setData($this->getHydrator()->extract($this->article));
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        unset($specs['article']['type']);

        return $specs;
    }
}
