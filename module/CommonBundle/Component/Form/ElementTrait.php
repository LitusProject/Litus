<?php

namespace CommonBundle\Component\Form;

use Laminas\Form\FormInterface;

/**
 * ElementTrait
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
trait ElementTrait
{
    /**
     * @var boolean
     */
    private $required = false;

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * Specifies whether this element is a required field. Also sets the HTML5
     * 'required' attribute.
     *
     * @param  boolean $flag
     * @return self
     */
    public function setRequired($flag = true)
    {
        $this->required = $flag;
        $this->setAttribute('required', $flag);

        $labelAttributes = $this->getLabelAttributes() ?: array();
        if (isset($labelAttributes['class'])) {
            if (strpos($labelAttributes['class'], 'required') === false) {
                $labelAttributes['class'] .= ' ' . ($flag ? 'required' : 'optional');
            }
        } else {
            $labelAttributes['class'] = ($flag ? 'required' : 'optional');
        }
        $this->setLabelAttributes($labelAttributes);

        return $this;
    }

    /**
     * @param  string $class The class(es) to add
     * @return self
     */
    public function addClass($class)
    {
        if ($this->hasAttribute('class')) {
            $this->setAttribute('class', $this->getAttribute('class') . ' ' . $class);
        } else {
            $this->setAttribute('class', $class);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getInputSpecification()
    {
        if (!$this->hasOption('input')) {
            return array(
                'name'     => $this->getName(),
                'required' => $this->isRequired(),
            );
        }

        $config = $this->getOption('input');

        if (!array_key_exists('required', $config)) {
            $config['required'] = $this->isRequired();
        }

        $config['name'] = $this->getName();

        return $config;
    }

    /**
     * @param  string $option
     * @return boolean
     */
    public function hasOption($option)
    {
        return array_key_exists($option, $this->options);
    }

    /**
     * @param array $attributes
     * @return void
     */
    public function setAttributes($attributes)
    {
        if (array_key_exists('class', $attributes)) {
            $this->addClass($attributes['class']);
            unset($attributes['class']);
        }

        parent::setAttributes($attributes);
    }

    /**
     * @param  FormInterface $form
     * @return mixed
     */
    public function prepareElement(FormInterface $form)
    {
        if (!$this->hasAttribute('id')) {
            $this->setAttribute('id', md5(uniqid(rand(), true)));
        }
    }

    // The following methods are required by the trait

    /**
     * @param  string $name
     * @return mixed|null
     */
    abstract public function getAttribute($name);

    /**
     * @param  string     $name
     * @param  mixed|null $value
     * @return self
     */
    abstract public function setAttribute($name, $value);

    /**
     * @param  string $name
     * @return boolean
     */
    abstract public function hasAttribute($name);

    /**
     * @return array|null
     */
    abstract public function getLabelAttributes();

    /**
     * @param  array $attributes
     * @return self
     */
    abstract public function setLabelAttributes(array $attributes);
}
