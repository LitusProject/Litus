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
 *
 * @license http://litus.cc/LICENSE
 */

namespace MailBundle\Component\Validator;

/**
 * Checks whether an alias is unique or not.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Alias extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'alias' => null,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'An alias with this name already exists',
    );

    /**
     * Sets validator options
     *
     * @param int|array|\Traversable $options
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $options = func_get_args();
            $temp['alias'] = array_shift($options);
            $options = $temp;
        }

        parent::__construct($options);
    }

    /**
     * Returns true if no matching record is found in the database.
     *
     * @param  string  $value   The value of the field that will be validated
     * @param  array   $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $alias = $this->_entityManager
            ->getRepository('MailBundle\Entity\Alias')
            ->findOneByName($value);

        if (null === $alias || ($this->options['alias'] && $alias == $this->options['alias'])) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}
