<?php

namespace FormBundle\Entity\Field;

use CommonBundle\Entity\General\Language;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Field\Checkbox")
 * @ORM\Table(name="form_fields_checkboxes")
 */
class Checkbox extends \FormBundle\Entity\Field
{
    /**
     * @param  Language $language
     * @param  boolean  $value
     * @return string
     */
    public function getValueString(Language $language, $value)
    {
        return $value ? 'TRUE' : 'FALSE';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'checkbox';
    }
}
