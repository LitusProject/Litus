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

use CommonBundle\Component\Form\Element\Csrf;
use CommonBundle\Component\Form\Element\Hidden;
use Zend\Form\ElementInterface;

/**
 * View helper to render form element errors.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class FormElementErrors extends \Zend\Form\View\Helper\FormElementErrors
{
    /**@+
     * @var string
     */
    protected $messageCloseString = '.</div>';
    protected $messageOpenFormat = '<div%s>';
    protected $messageSeparatorString = '.<br />';
    /**@-*/

    /**
     * @var array
     */
    protected $attributes = array(
        'class' => 'invalid-feedback'
    );

    /**
     * @param  ElementInterface $element
     * @param  array            $attributes
     * @return string
     */
    public function render(ElementInterface $element, array $attributes = array())
    {
        if ($element instanceof Csrf || $element instanceof Hidden) {
            $this->attributes['class'] .= ' mb-3';
        }

        return parent::render($element, $attributes);
    }
}
