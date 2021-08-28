<?php

namespace CudiBundle\Component\Validator\Sale\Session\Restriction;

use CudiBundle\Entity\Sale\Session;
/**
 * Check Restriction already exists.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Exists extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'session' => null,
    );

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The restriction already exists',
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
            $options['session'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if and only if a field name has been set, the field name is available in the
     * context, and the value of that field is valid.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $restriction = null;
        if ($value == 'name') {
            $restriction = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session\Restriction\Name')
                ->findOneBySession($this->options['session']);
        } elseif ($value == 'year') {
            $restriction = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session\Restriction\Year')
                ->findOneBySession($this->options['session']);
        } elseif ($value == 'study') {
            $restriction = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session\Restriction\Study')
                ->findOneBySession($this->options['session']);
        }

        if ($restriction == null) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}
