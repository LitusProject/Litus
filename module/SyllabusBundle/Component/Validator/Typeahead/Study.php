<?php

namespace SyllabusBundle\Component\Validator\Typeahead;

class Study extends \CommonBundle\Component\Validator\Typeahead
{
    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This study does not exits',
    );

    /**
     * Create a new typeahead validator
     */
    public function __construct()
    {
        parent::__construct('SyllabusBundle\Entity\Study');
    }
}
