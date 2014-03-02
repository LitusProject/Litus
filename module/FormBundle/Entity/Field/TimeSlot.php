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

namespace FormBundle\Entity\Field;

use CommonBundle\Entity\General\Language,
    DateTime,
    Doctrine\ORM\Mapping as ORM,
    IntlDateFormatter,
    FormBundle\Entity\Field,
    FormBundle\Entity\Node\Form;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Field\TimeSlot")
 * @ORM\Table(name="forms.fields_timeslot")
 */
class TimeSlot extends Field
{
    /**
     * @var \DateTime The start date of the timeslot
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var \DateTime The end date of the timeslot
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var array The translations of this field
     *
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\Field\Translation\TimeSlot", mappedBy="timeslot", cascade={"remove"})
     */
    private $timeslotTranslations;

    /**
     * @param FormBundle\Entity\Node\Form $form
     * @param integer                     $order
     * @param bool                        $required
     * @param \FormBundle\Entity\Field    $visibityDecisionField
     * @param string                      $visibilityValue
     * @param \DateTime                   $startDate
     * @param \DateTime                   $endDate
     */
    public function __construct(Form $form, $order, $required, Field $visibityDecisionField = null, $visibilityValue = null, DateTime $startDate, DateTime $endDate)
    {
        parent::__construct($form, $order, $required, $visibityDecisionField, $visibilityValue);

        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param  \DateTime                          $startDate
     * @return \FormBundle\Entity\Fields\TimeSlot
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param  \DateTime                          $endDate
     * @return \FormBundle\Entity\Fields\TimeSlot
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean                               $allowFallback
     *
     * @return string
     */
    public function getLabel(Language $language = null, $allowFallback = true)
    {
        $formatterDate = new IntlDateFormatter(
            $language->getAbbrev(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'd MMM Y'
        );

        $formatterHour = new IntlDateFormatter(
            $language->getAbbrev(),
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
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean                               $allowFallback
     *
     * @return string
     */
    public function getLocation(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTimeSlotTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getLocation();

        return '';
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean                               $allowFallback
     *
     * @return string
     */
    public function getExtraInformation(Language $language = null, $allowFallback = true)
    {
        $translation = $this->getTimeSlotTranslation($language, $allowFallback);

        if (null !== $translation)
            return $translation->getExtraInformation();

        return '';
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean                               $allowFallback
     *
     * @return \FormBundle\Entity\Translation
     */
    public function getTimeSlotTranslation(Language $language = null, $allowFallback = true)
    {
        foreach ($this->timeslotTranslations as $translation) {
            if (null !== $language && $translation->getLanguage() == $language)
                return $translation;

            if ($translation->getLanguage()->getAbbrev() == \Locale::getDefault())
                $fallbackTranslation = $translation;
        }

        if ($allowFallback && isset($fallbackTranslation))
            return $fallbackTranslation;

        return null;
    }

    /**
     * @param  \CommonBundle\Entity\General\Language $language
     * @param  boolean                               $value
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
