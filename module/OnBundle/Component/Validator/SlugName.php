<?php

namespace OnBundle\Component\Validator;

/**
 * Checks whether a slug name already exists.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class SlugName extends \CommonBundle\Component\Validator\AbstractValidator
{
    const TITLE_EXISTS = 'nameExists';

    protected $options = array(
        'slug' => null,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::TITLE_EXISTS => 'There already is a slug with this title',
    );

    /**
     * Sets validator options
     *
     * @param integer|array|\Traversable $options
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $args = func_get_args();
            $options = array();
            $options['slug'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if no publication with this title exists.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $slug = $this->getEntityManager()
            ->getRepository('OnBundle\Entity\Slug')
            ->findOneByName($value);

        if ($slug === null || ($this->options['slug'] && $slug == $this->options['slug'])) {
            return true;
        }

        $this->error(self::TITLE_EXISTS);

        return false;
    }
}
