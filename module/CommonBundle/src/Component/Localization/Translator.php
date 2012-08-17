<?php

namespace CommonBundle\Component\Localization;

use Zend\Translator\Exception\InvalidArgumentException;

class Translator extends \Zend\Translator\Translator
{
    /**
     * Creates a new translator instance.
     *
     * Override from \Zend\Translator\Translator::__construct($options).
     *
     * @param string $adapter
     * @param array $translations
     */
    public function __construct($adapter, $translations)
    {
        if (empty($translations))
            throw new InvalidArgumentException("At least one translator file must be given.");

        $first = current($translations);
        parent::__construct($adapter, $first['content'], $first['locale']);

          foreach ($translations as $alias => $translation) {
              $this->addTranslation($translation['content'], $translation['locale']);
          }
    }
}
