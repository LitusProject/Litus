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

namespace CudiBundle\Form\Admin\Prof\Article;

use LogicException;

/**
 * Confirm Article add action
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Confirm extends \CudiBundle\Form\Admin\Article\Add
{
    public function init()
    {
        if (null === $this->article) {
            throw new LogicException('Cannot confirm a null article');
        }

        parent::init();

        $this->remove('subject_form');

        $this->remove('submit')
            ->addSubmit('Confirm', 'article_add');

        $this->bind($this->article);
    }
}
