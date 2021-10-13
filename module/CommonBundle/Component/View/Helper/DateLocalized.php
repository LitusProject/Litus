<?php

namespace CommonBundle\Component\View\Helper;

use CommonBundle\Component\ServiceManager\ServiceLocatorAware\TranslatorTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use DateTime;
use IntlDateFormatter;

/**
 * A view helper that allows us to easily translate the date.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class DateLocalized extends \Laminas\View\Helper\AbstractHelper implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    use TranslatorTrait;

    /**
     * @param DateTime|null $date
     * @param string        $format
     *
     * @return string
     */
    public function __invoke(DateTime $date = null, $format = '')
    {
        if ($date === null) {
            return '';
        }

        $formatter = new IntlDateFormatter(
            $this->getTranslator()->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            $format
        );

        return $formatter->format($date);
    }
}
