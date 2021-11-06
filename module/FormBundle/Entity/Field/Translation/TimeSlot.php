<?php

namespace FormBundle\Entity\Field\Translation;

use CommonBundle\Entity\General\Language;
use Doctrine\ORM\Mapping as ORM;
use FormBundle\Entity\Field\TimeSlot as TimeSlotField;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Field\Translation\TimeSlot")
 * @ORM\Table(name="form_fields_translations_time_slots")
 */
class TimeSlot
{
    /**
     * @var integer The ID of this tanslation
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var TimeSlotField The field of this translation
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Field\TimeSlot", inversedBy="timeslotTranslations")
     * @ORM\JoinColumn(name="timeslot", referencedColumnName="id")
     */
    private $timeslot;

    /**
     * @var Language The language of this tanslation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;

    /**
     * @var string The location of this tanslation
     *
     * @ORM\Column(type="string")
     */
    private $location;

    /**
     * @var string The location of this tanslation
     *
     * @ORM\Column(name="extra_information", type="text")
     */
    private $extraInformation;

    /**
     * @param TimeSlotField timeslot
     * @param Language               $language
     * @param string                 $location
     * @param string                 $extraInformation
     */
    public function __construct(TimeSlotField $timeslot, Language $language, $location, $extraInformation)
    {
        $this->timeslot = $timeslot;
        $this->language = $language;
        $this->location = $location;
        $this->extraInformation = $extraInformation;
    }

    /**
     * @var int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return TimeSlotField
     */
    public function getTimeSlot()
    {
        return $this->timeslot;
    }

    /**
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     *
     * @return self
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtraInformation()
    {
        return $this->extraInformation;
    }

    /**
     * @param string $extraInformation
     *
     * @return self
     */
    public function setExtraInformation($extraInformation)
    {
        $this->extraInformation = $extraInformation;

        return $this;
    }
}
