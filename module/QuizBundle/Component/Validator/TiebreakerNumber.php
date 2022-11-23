<?php

namespace QuizBundle\Component\Validator;

/**
 * Validates the uniqueness of a tiebreaker number in a quiz
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class TiebreakerNumber extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'quiz'  => null,
        'tiebreaker' => null,
    );

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The tiebreaker number already exists',
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
            $options['quiz'] = array_shift($args);
            $options['tiebreaker'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if this tiebreaker is unique
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if (!is_numeric($value)) {
            $this->error(self::NOT_VALID);

            return false;
        }

        $tiebreakers = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Tiebreaker')
            ->findBy(
                array(
                    'quiz'  => $this->options['quiz']->getId(),
                    'order' => $value,
                )
            );

        if (count($tiebreakers) == 0 || $tiebreakers[0] == $this->options['tiebreaker']) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}
