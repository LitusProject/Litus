<?php

namespace FormBundle\Entity\Field;

use CommonBundle\Entity\General\Language;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Field\File")
 * @ORM\Table(name="form_fields_files")
 */
class File extends \FormBundle\Entity\Field
{
    /**
     * @var integer The maximum size of the file.
     *
     * @ORM\Column(name="max_size", type="integer")
     */
    private $maxSize;

    /**
     * Returns the maximum size of the file
     *
     * @return integer The maximum size of the file
     */
    public function getMaxSize()
    {
        return $this->maxSize;
    }

    /**
     * @param  integer $maxSize The maximum size of the file
     * @return File
     */
    public function setMaxSize($maxSize)
    {
        if (!is_int($maxSize)) {
            $maxSize = 25;
        }
        $this->maxSize = $maxSize;

        return $this;
    }

    /**
     * @param  Language $language
     * @param  boolean  $value
     * @return boolean
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
        return 'file';
    }
}
