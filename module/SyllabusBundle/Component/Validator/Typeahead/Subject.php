<?php

namespace SyllabusBundle\Component\Validator\Typeahead;

class Subject extends \CommonBundle\Component\Validator\Typeahead
{
    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This subject does not exits',
    );

    /**
     * Create a new typeahead validator
     */
    public function __construct()
    {
        parent::__construct('SyllabusBundle\Entity\Subject');
    }
}
