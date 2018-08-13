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

use LogicException;

/**
 * Edit Article
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    public function init()
    {
        if (null === $this->article) {
            throw new LogicException('Cannot edit a null article');
        }

        parent::init();

        $this->remove('subject_form');

    //    [COMMENT]: No idea why this is in here. Removed it on request by organisations
    //    if ($this->article->getType() == 'common') {
    //        /** @var \CommonBundle\Component\Form\Fieldset $articleFieldset */
    //        $articleFieldset = $this->get('article');
    //        $articleFieldset->remove('type');
    //    }

        $this->remove('submit')
            ->addSubmit('Save', 'article_edit');

        $this->bind($this->article);
    }
}
