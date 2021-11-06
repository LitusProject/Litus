<?php

namespace CommonBundle\Component\Validator\Typeahead;

class Person extends \CommonBundle\Component\Validator\Typeahead
{
    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This person does not exist',
    );

    /**
     * Create a new typeahead validator
     */
    public function __construct()
    {
        parent::__construct('CommonBundle\Entity\User\Person');
    }
}
