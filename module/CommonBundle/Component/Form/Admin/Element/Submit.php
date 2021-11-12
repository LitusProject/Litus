<?php

namespace CommonBundle\Component\Form\Admin\Element;

use CommonBundle\Component\Form\ElementTrait;

/**
 * Submit form element
 *
 * @author Matthias Swiggers <matthias.swiggers@vtk.be>
 */
class Submit extends \Laminas\Form\Element\Submit implements \CommonBundle\Component\Form\ElementInterface
{
    use ElementTrait;
}
