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

namespace SyllabusBundle\Component\Validator\Subject;

use CommonBundle\Component\Form\Form;
use CommonBundle\Component\Validator\FormAwareInterface;

/**
 * Matches the given subject against the database to check duplicate mappings.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ModuleGroup extends \CommonBundle\Component\Validator\AbstractValidator implements FormAwareInterface
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'subject'       => null,
        'academic_year' => null,
    );

    /**
     * @var Form The form to validate
     */
    private $form;

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The mapping already exists',
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
            $options['subject'] = array_shift($args);
            $options['academic_year'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * @param Form $form
     * @return self
     */
    public function setForm(Form $form)
    {
        $this->form = $form;

        return $this;
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

        $moduleGroup = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study\ModuleGroup')
            ->findOneById(self::getFormValue($this->form, array('module_group', 'id')));

        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study\SubjectMap')
            ->findOneByModuleGroupSubjectAndAcademicYear($moduleGroup, $this->options['subject'], $this->options['academic_year']);

        if ($mapping === null) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}
