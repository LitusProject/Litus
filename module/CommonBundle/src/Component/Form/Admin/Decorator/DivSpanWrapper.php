<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CommonBundle\Component\Form\Admin\Decorator;

use Zend\Form\Decorator\AbstractDecorator,
	Zend\Form\Decorator\Label;

/**
 * This decorator will be used to decorate our form fields with div and span tags.
 * It uses the label decorator internally.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class DivSpanWrapper extends \Zend\Form\Decorator\AbstractDecorator
{
	/**
	 * Decorate content and/or element
	 *
	 * @param string $content The element's content
	 * @return string
	 */
    public function render($content)
    {
        $labelDecorator = new Label();
        $labelDecorator->setElement($this->getElement());
        $label = $labelDecorator->render('');

        return '<div class="row"><span class="label">' . $label . '</span><span class="field">' . $content . '</span></div>';
    }
}