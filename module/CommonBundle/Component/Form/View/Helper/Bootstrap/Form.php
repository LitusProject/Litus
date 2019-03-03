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

namespace CommonBundle\Component\Form\View\Helper\Bootstrap;

use Zend\Form\FormInterface;

/**
 * View helper to render a form.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Form extends \Zend\Form\View\Helper\Form
{
    const LAYOUT_HORIZONTAL = 'horizontal';
    const LAYOUT_VERTICAL = 'vertical';

    /**
     * @var array
     */
    protected $layouts = array(
        self::LAYOUT_HORIZONTAL,
        self::LAYOUT_VERTICAL,
    );

    /**
     * @param  FormInterface $form
     * @param  string        $layout
     * @return string
     */
    public function __invoke(FormInterface $form = null, $layout = null)
    {
        if ($form === null) {
            return '';
        }

        if (in_array($layout, $this->layouts)) {
            foreach ($form->getElements() as $element) {
                $element->setOption('formLayout', $layout);
            }

            foreach ($form->getFieldsets() as $fieldset) {
                $fieldset->setOption('formLayout', $layout);
            }
        }

        return parent::__invoke($form);
    }
}
