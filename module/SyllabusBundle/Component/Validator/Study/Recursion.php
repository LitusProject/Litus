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

namespace SyllabusBundle\Component\Validator\Study;

use CommonBundle\Component\Form\Form,
    CommonBundle\Component\Validator\FormAwareInterface;

/**
 * Matches the given parent against recursion
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Recursion extends \CommonBundle\Component\Validator\AbstractValidator implements FormAwareInterface
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'study' => null,
    );

    /**
    * @var Form The form to validate
    */
    private $_form;

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The study cannot be chosen',
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
            $temp['study'] = array_shift($options);
            $options = $temp;
        }

        parent::__construct($options);
    }

    /**
     * Returns true if and only if a field name has been set, the field name is available in the
     * context, and the value of that field is valid.
     *
     * @param  string  $value   The value of the field that will be validated
     * @param  array   $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        $parent = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findOneByKulId($this->_form->get('parent')->get('id')->getValue());

        if (null === $parent) {
            return true;
        }

        if ($parent->getId() == $this->options['study']->getId()) {
            $this->error(self::NOT_VALID);

            return false;
        }

        foreach ($this->options['study']->getAllChildren() as $child) {
            if ($child->getId() == $parent->getId()) {
                $this->error(self::NOT_VALID);

                return false;
            }
        }

        return true;
    }

    /**
     * @param Form $form
     */
    public function setForm(Form $form)
    {
        $this->_form = $form;
    }
}
