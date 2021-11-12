<?php

namespace QuizBundle\Component\Validator;

/**
 * Validates the uniqueness of a team number in a quiz
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class TeamNumber extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'quiz' => null,
        'team' => null,
    );

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The team number already exists',
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
            $options['team'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if this team is unique
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

        $teams = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Team')
            ->findBy(
                array(
                    'quiz'   => $this->options['quiz']->getId(),
                    'number' => $value,
                )
            );

        if (count($teams) == 0 || $teams[0] == $this->options['team']) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}
