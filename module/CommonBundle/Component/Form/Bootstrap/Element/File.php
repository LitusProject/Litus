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

namespace CommonBundle\Component\Form\Bootstrap\Element;

use CommonBundle\Component\Form\ElementTrait;
use Laminas\Form\FormInterface;

/**
 * File form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class File extends \Laminas\Form\Element\File implements \CommonBundle\Component\Form\ElementInterface
{
    use ElementTrait {
        ElementTrait::getInputSpecification as getTraitInputSpecification;
        ElementTrait::prepareElement as traitPrepareElement;
    }

    public function init()
    {
        $this->setLabelAttributes(
            array(
                'class' => 'col-sm-2 control-label',
            )
        );
    }

    public function getInputSpecification()
    {
        $specification = $this->getTraitInputSpecification();
        $specification['type'] = 'Laminas\InputFilter\FileInput';

        return $specification;
    }

    /**
     * Prepare the form element (mostly used for rendering purposes)
     *
     * @param  FormInterface $form
     * @return mixed
     */
    public function prepareElement(FormInterface $form)
    {
        $this->traitPrepareElement($form);

        return parent::prepareElement($form);
    }
}
