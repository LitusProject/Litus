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
    CommonBundle\Component\Validator\FormAwareInterface,
    Doctrine\ORM\EntityManager,
    SyllabusBundle\Entity\Study;

/**
 * Matches the given parent against recursion
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Recursion extends \Zend\Validator\AbstractValidator implements FormAwareInterface
{
    const NOT_VALID = 'notValid';

    /**
     * @var EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var Study The study exluded from this check
     */
    private $_study;

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
     * Create a new Article Barcode validator.
     *
     * @param EntityManager $entityManager The EntityManager instance
     * @param Study         $study
     * @param mixed         $opts          The validator's options
     */
    public function __construct(EntityManager $entityManager, Study $study, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;
        $this->_study = $study;
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

        $parent = $this->_entityManager
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findOneByKulId($this->_form->get('parent')->get('id')->getValue());

        if (null === $parent) {
            return true;
        }

        if ($parent->getId() == $this->_study->getId()) {
            $this->error(self::NOT_VALID);

            return false;
        }

        foreach ($this->_study->getAllChildren() as $child) {
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
