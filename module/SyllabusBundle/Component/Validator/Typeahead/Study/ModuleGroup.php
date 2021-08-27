<?php

namespace SyllabusBundle\Component\Validator\Typeahead\Study;

class ModuleGroup extends \CommonBundle\Component\Validator\Typeahead
{
    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This module group does not exits',
    );

    /**
     * Create a new typeahead validator
     */
    public function __construct()
    {
        parent::__construct('SyllabusBundle\Entity\Study\ModuleGroup');
    }
}
