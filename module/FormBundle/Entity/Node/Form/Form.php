<?php

namespace FormBundle\Entity\Node\Form;

use CommonBundle\Entity\General\Language;
use Doctrine\ORM\Mapping as ORM;
use FormBundle\Entity\Field\File as FileField;
use FormBundle\Entity\Node\Entry;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Node\Form\Form")
 * @ORM\Table(name="nodes_forms_forms")
 */
class Form extends \FormBundle\Entity\Node\Form
{
    /**
     * @return string
     */
    public function getType()
    {
        return 'form';
    }

    /**
     * @param  Entry    $entry
     * @param  Language $language
     * @return string
     */
    protected function getSummary(Entry $entry, Language $language)
    {
        $fieldEntries = $this->entityManager
            ->getRepository('FormBundle\Entity\Entry')
            ->findAllByFormEntry($entry);

        $result = '';
        foreach ($fieldEntries as $fieldEntry) {
            $result .= $fieldEntry->getField()->getLabel($language) . ': ';
            $result .= $fieldEntry->getField() instanceof FileField ? $fieldEntry->getReadableValue() : $fieldEntry->getValueString($language);
            $result .= PHP_EOL;
        }

        return $result;
    }
}
