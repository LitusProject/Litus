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

namespace CudiBundle\Form\Admin\Article;

use LogicException;

/**
 * Duplicate of Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Duplicate extends Add
{
    public function init()
    {
        if (null === $this->article) {
            throw new LogicException('Cannot duplicate a null article');
        }

        parent::init();

        $this->remove('subject_form');

        /** @var \CommonBundle\Component\Form\Fieldset $articleFieldset */
        $articleFieldset = $this->get('article');
        $articleFieldset->get('type')
            ->setAttribute('disabled', true);

        if ($this->article->getType() == 'common') {
            $articleFieldset->remove('type');
        }

        $this->remove('submit')
            ->addSubmit('Add', 'article_add');

        // don't bind to the article, but extract its data
        $this->setData($this->getHydrator()->extract($this->article));
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        unset($specs['article']['type']);

        return $specs;
    }
}
