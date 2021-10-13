<?php

namespace CudiBundle\Component\Validator\Typeahead\Sale;

class Article extends \CommonBundle\Component\Validator\Typeahead
{
    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This article does not exits',
    );

    /**
     * Create a new typeahead validator
     */
    public function __construct()
    {
        parent::__construct('CudiBundle\Entity\Sale\Article');
    }
}
