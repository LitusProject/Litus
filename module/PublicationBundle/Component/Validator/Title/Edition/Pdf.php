<?php

namespace PublicationBundle\Component\Validator\Title\Edition;

/**
 * Checks whether a publication title already exists.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Pdf extends \CommonBundle\Component\Validator\AbstractValidator
{
    const TITLE_EXISTS = 'titleExists';

    protected $options = array(
        'publication'   => null,
        'academic_year' => null,
        'exclude'       => null,
    );

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::TITLE_EXISTS => 'There already is a PDF edition with this title for this publication',
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
            $options['publication'] = array_shift($args);
            $options['academic_year'] = array_shift($args);
            $options['exclude'] = array_shift($args);
        }

        parent::__construct($options);
    }

    /**
     * Returns true if no edition with this title exists.
     *
     * @param  string     $value   The value of the field that will be validated
     * @param  array|null $context The context of the field that will be validated
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $edition = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Edition\Pdf')
            ->findOneByPublicationTitleAndAcademicYear($this->options['publication'], $value, $this->options['academic_year']);

        if ($edition !== null) {
            if ($this->options['exclude'] === null || $edition->getId() !== $this->options['exclude']) {
                $this->error(self::TITLE_EXISTS);

                return false;
            }
        }

        return true;
    }
}
