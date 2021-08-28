<?php

namespace CudiBundle\Component\Validator\Typeahead;

class Retail extends \CommonBundle\Component\Validator\Typeahead
{
    const NOT_ALLOWED = 'notAllowed';
    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => 'This article does not exits',
        self::NOT_ALLOWED => 'This article is not allowed for retail',
    );

    /**
     * Create a new typeahead validator
     */
    public function __construct()
    {
        parent::__construct('CudiBundle\Entity\Article');
    }

    public function isValid($value, $context = null)
    {
        $allowedRetailTypes = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.retail_allowed_types')
        );

        if (!parent::isValid($value, $context)) {
            return false;
        }

        $retailValid = in_array($this->entityObject->getType(), $allowedRetailTypes);

        if (!$retailValid) {
            $this->error(self::NOT_ALLOWED);
            return false;
        }

        return true;
    }
}
