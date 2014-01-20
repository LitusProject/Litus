<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace FormBundle\Entity\Field\Translation;

use CommonBundle\Entity\General\Language,
    FormBundle\Entity\Field\TimeSlot as TimeSlotField,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Field\Translation\TimeSlot")
 * @ORM\Table(name="forms.fields_timeslots_translations")
 */
class TimeSlot
{
    /**
     * @var int The ID of this tanslation
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \FormBundle\Entity\Field\TimeSlot The field of this translation
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Field\TimeSlot", inversedBy="timeslotTranslations")
     * @ORM\JoinColumn(name="timeslot", referencedColumnName="id")
     */
    private $timeslot;

    /**
     * @var \CommonBundle\Entity\General\Language The language of this tanslation
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
     * @param \FormBundle\Entity\Field\TimeSlot timeslot
     * @param \CommonBundle\Entity\General\Language $language
     * @param string $location
     * @param string $extraInformation
     */
    public function __construct(TimeSlotField $timeslot, Language $language, $location, $extraInformation)
    {
        $this->timeslot = $timeslot;
        $this->language = $language;
        $this->location = $location;
        $this->extraInformation = $extraInformation;
    }

    /**
     * @return \FormBundle\Entity\Field\TimeSlot
     */
    public function getTimeSlot()
    {
        return $this->timeslot;
    }

    /**
     * @return \CommonBundle\Entity\General\Language
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
     * @return \FormBundle\Entity\Field\Translation\TimeSlot
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
     * @return \FormBundle\Entity\Field\Translation\TimeSlot
     */
    public function setExtraInformation($extraInformation)
    {
        $this->extraInformation = $extraInformation;
        return $this;
    }
}