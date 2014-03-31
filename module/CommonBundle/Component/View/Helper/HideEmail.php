<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\View\Helper;

/**
 * A view helper that replaces all email addresses.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class HideEmail extends \Zend\View\Helper\AbstractHelper
{
    /**
     *
     * @return Zend\Date\Date
     */
    public function __invoke($text)
    {
        $regexEmail = '([^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*)\@([a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4})';
        $regexText = '[a-z0-9.\s\-]*';
        $text = preg_replace('/<a href\="(?:mailto\:)?' . $regexEmail . '">' . $regexEmail . '<\/a>/i',
            '<script type="text/javascript">document.write(\'<a href="mail\'+\'to:$1\');document.write(\'@\'+\'$3">\'+\'$1\');document.write(\'@\');document.write(\'$3\'+\'</a>\')</script>', $text);
        $text = preg_replace('/<a href\="(?:mailto\:)?' . $regexEmail . '">(' . $regexText . ')<\/a>/i',
            '<script type="text/javascript">document.write(\'<a href="mail\'+\'to:$1\');document.write(\'@\'+\'$3">\');document.write(\'$5\'+\'</a>\')</script>', $text);
        $text = preg_replace('/<a href\="(?:mailto\:)?' . $regexEmail . '">(' . $regexText . ')\(' . $regexEmail . '\)(' . $regexText . ')<\/a>/i',
            '<script type="text/javascript">document.write(\'<a href="mail\'+\'to:$1\');document.write(\'@\'+\'$3">\');document.write(\'$5\'+\'(\');document.write(\'$6\');document.write(\'@\'+\'$8)\'+\'</a>\')</script>', $text);

        return preg_replace('/' . $regexEmail . '/', '<script type="text/javascript">document.write(\'$1\');document.write(\'@\'+\'$3\')</script>', $text);
    }
}
