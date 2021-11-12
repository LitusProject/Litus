<?php

namespace CommonBundle\Component\View\Helper;

/**
 * A view helper that replaces all email addresses.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class HideEmail extends \Laminas\View\Helper\AbstractHelper
{
    /**
     * @param  string $text
     * @return string
     */
    public function __invoke($text)
    {
        $regexEmail = '([^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*)\@([a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4})';
        $regexText = '[a-z0-9.\s\-]*';
        $text = preg_replace(
            '/<a href\="(?:mailto\:)?' . $regexEmail . '">' . $regexEmail . '<\/a>/i',
            '<script type="text/javascript">document.write(\'<a href="mail\'+\'to:$1\');document.write(\'@\'+\'$3">\'+\'$1\');document.write(\'@\');document.write(\'$3\'+\'</a>\')</script>',
            $text
        );
        $text = preg_replace(
            '/<a href\="(?:mailto\:)?' . $regexEmail . '">(' . $regexText . ')<\/a>/i',
            '<script type="text/javascript">document.write(\'<a href="mail\'+\'to:$1\');document.write(\'@\'+\'$3">\');document.write(\'$5\'+\'</a>\')</script>',
            $text
        );
        $text = preg_replace(
            '/<a href\="(?:mailto\:)?' . $regexEmail . '">(' . $regexText . ')\(' . $regexEmail . '\)(' . $regexText . ')<\/a>/i',
            '<script type="text/javascript">document.write(\'<a href="mail\'+\'to:$1\');document.write(\'@\'+\'$3">\');document.write(\'$5\'+\'(\');document.write(\'$6\');document.write(\'@\'+\'$8)\'+\'</a>\')</script>',
            $text
        );

        return preg_replace('/' . $regexEmail . '/', '<script type="text/javascript">document.write(\'$1\');document.write(\'@\'+\'$3\')</script>', $text);
    }
}
