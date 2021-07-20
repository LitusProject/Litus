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

namespace OnBundle\Form\Admin\Slug;

/**
 * Edit Slug
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \OnBundle\Form\Admin\Slug\Add
{
    public function init()
    {
        parent::init();

        $nameField = $this->get('name');
        $nameField->setRequired();

        $this->remove('submit')
            ->addSubmit('Save', 'slug_edit');

        $dateTimeField = $this->get('expiration_date');

        $dateTimeField->setValue($this->slug->getExpirationDate()->format('d/m/Y'));
    }
}
