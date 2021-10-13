<?php

namespace CommonBundle\Component\View\Helper;

use Parsedown;

/**
 * A view helper that parses Markdown.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Markdown extends \Laminas\View\Helper\AbstractHelper
{
    /**
     * @param  string|null
     * @return string
     */
    public function __invoke($text)
    {
        if ($text === null) {
            return '';
        }

        return (new Parsedown())->text($text);
    }
}
