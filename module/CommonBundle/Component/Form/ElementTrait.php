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

namespace CommonBundle\Component\Form;

use Zend\Form\FormInterface;

/**
 * ElementTrait
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
trait ElementTrait
{
    use LabelAwareTrait;

    /**
     * @param string $class
     * @return self
     */
    public function addClass($class)
    {
        if ($this->hasClass($class)) {
            return $this;
        }

        $classes = array();
        if ($this->hasAttribute('class')) {
            $classes = explode(' ', $this->getAttribute('class'));
        }

        $this->setAttribute(
            'class',
            implode(
                ' ',
                array_merge(
                    $classes,
                    array(
                        $class
                    )
                )
            )
        );

        return $this;
    }

    /**
     * @param string $class
     * @return boolean
     */
    public function hasClass($class)
    {
        return in_array($class, explode(' ', $this->getAttribute('class')));
    }

    /**
     * @return array
     */
    public function getInputSpecification()
    {
        if (!array_key_exists('input', $this->getOptions())) {
            return array(
                'name'     => $this->getName(),
                'required' => $this->getAttribute('required') ?? false,
            );
        }

        $config = $this->getOption('input');
        $config['name'] = $this->getName();

        if (!array_key_exists('required', $config)) {
            $config['required'] = $this->getAttribute('required') ?? false;
        }

        return $config;
    }

    /**
     * @param  FormInterface $form
     * @return mixed
     */
    public function prepareElement(FormInterface $form)
    {
        if (!$this->hasAttribute('id')) {
            $this->setAttribute('id', hash('sha256', random_bytes(256)));
        }
    }

    /**
     * @param  string     $name
     * @param  mixed|null $value
     * @return self
     */
    abstract public function setAttribute($name, $value);

    /**
     * @param  string $name
     * @return mixed|null
     */
    abstract public function getAttribute($name);

    /**
     * @param  string $name
     * @return boolean
     */
    abstract public function hasAttribute($name);

    /**
     * @param array $labelAttributes
     * @return self
     */
    abstract public function setLabelAttributes(array $labelAttributes);

    /**
     * @return array
     */
    abstract public function getLabelAttributes();

    /**
     * @return array
     */
    abstract public function getOptions();
}
