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

namespace QuizBundle\Component\Validator\Round;

/**
 * Validates the uniqueness of a round number in a quiz
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class Unique extends \CommonBundle\Component\Validator\AbstractValidator
{
    const NOT_VALID = 'notValid';

    protected $options = array(
        'quiz' => null,
        'round' => null,
    );

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'The round number already exists',
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
            $options['quiz'] = array_shift($args);
            $options['round'] = array_shift($args);
        }

        parent::__construct($options);
    }

    public function isValid($value)
    {
        $this->setValue($value);

        if (!is_numeric($value)) {
            $this->error(self::NOT_VALID);

            return false;
        }

        $rounds = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Round')
            ->findBy(
                array(
                    'quiz' => $this->options['quiz']->getId(),
                    'order' => $value,
                )
            );

        if (count($rounds) == 0 || $rounds[0] == $this->options['round']) {
            return true;
        }

        $this->error(self::NOT_VALID);

        return false;
    }
}
