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

namespace CudiBundle\Component\Validator\Sale\Session\Restriction;

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
     * @param int|array|\Traversable $options
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
        if ('name' == $value) {
            $restriction = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session\Restriction\Name')
                ->findOneBySession($this->options['session']);
        } elseif ('year' == $value) {
            $restriction = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session\Restriction\Year')
                ->findOneBySession($this->options['session']);
        } elseif ('study' == $value) {
            $restriction = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session\Restriction\Study')
                ->findOneBySession($this->options['session']);
        }

        if (null == $restriction) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}
