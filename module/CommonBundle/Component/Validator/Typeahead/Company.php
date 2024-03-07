<?php

namespace CommonBundle\Component\Validator\Typeahead;

use CommonBundle\Component\Validator\Typeahead;

class Company extends Typeahead
{
    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This company does not exist',
    );

    /**
     * Create a new typeahead validator
     */
    public function __construct()
    {
        parent::__construct('BrBundle\Entity\Company');
    }
}
