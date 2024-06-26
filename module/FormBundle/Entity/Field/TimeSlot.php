<?php

namespace FormBundle\Entity\Field;

use CommonBundle\Entity\General\Language;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FormBundle\Entity\Node\Form;
use IntlDateFormatter;
use Locale;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Field\TimeSlot")
 * @ORM\Table(name="form_fields_time_slots")
 */
class TimeSlot extends \FormBundle\Entity\Field
{
    /**
     * @var DateTime The start date of the timeslot
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime The end date of the timeslot
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var ArrayCollection The translations of this field
     *
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\Field\Translation\TimeSlot", mappedBy="timeslot", cascade={"remove"})
     */
    private $timeslotTranslations;

    /**
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        parent::__construct($form);

        $this->timeslotTranslations = new ArrayCollection();
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param  DateTime $startDate
     * @return self
     */
    public function setStartDate(DateTime $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param  DateTime $endDate
     * @return self
     */
    public function setEndDate(DateTime $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @param Language|null $language
     * @param boolean       $allowFallback
     *
     * @return string
     */
    public function getLabel(Language $language = null, $allowFallback = true)
    {
        $locale = isset($language) ? $language->getAbbrev() : Locale::getDefault();

        $formatterDate = new IntlDateFormatter(
            $locale,
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'd MMM Y'
        );

        $formatterHour = new IntlDateFormatter(
            $locale,
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'H:mm'
        );

        if ($this->getStartDate()->format('d/M/Y') == $this->getEndDate()->format('d/M/Y')) {
            return $formatterDate->format($this->getStartDate()) . ': ' . $formatterHour->format($this->getStartDate()) . ' - ' . $formatterHour->format($this->getEndDate());
        } else {
            return $formatterDate->format($this->getStartDate()) . ' ' . $formatterHour->format($this->getStartDate()) . ' - ' . $formatterDate->format($this->getEndDate()) . ' ' . $formatterHour->format($this->getEndDate());
        }
    }

    /**
     * @param Language|null $language
     * @param boolean       $allowFallback
     *
     * @return string
     */
    public function getLocation(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTimeSlotTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getLocation();
        }

        return '';
    }

    /**
     * @param Language|null $language
     * @param boolean       $allowFallback
     *
     * @return string
     */
    public function getExtraInformation(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTimeSlotTranslation($language, $allowFallback);

        if ($translation !== null) {
            return $translation->getExtraInformation();
        }

        return '';
    }

    /**
     * @param Language|null $language
     * @param boolean       $allowFallback
     *
     * @return \FormBundle\Entity\Field\Translation\TimeSlot
     */
    public function getTimeSlotTranslation(Language $language = null, $allowFallback = true)
    {
        foreach ($this->timeslotTranslations as $translation) {
            if ($language !== null && $translation->getLanguage() == $language) {
                return $translation;
            }

            if ($translation->getLanguage()->getAbbrev() == Locale::getDefault()) {
                $fallbackTranslation = $translation;
            }
        }

        if ($allowFallback && isset($fallbackTranslation)) {
            return $fallbackTranslation;
        }

        return null;
    }

    /**
     * @param  Language $language
     * @param  string   $value
     * @return string
     */
    public function getValueString(Language $language, $value)
    {
        return $value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'timeslot';
    }
}
