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

namespace FormBundle\Entity\Node\Form;

use CommonBundle\Entity\General\Language,
    Doctrine\ORM\Mapping as ORM,
    FormBundle\Entity\Node\Entry,
    FormBundle\Entity\Node\Form as BaseForm;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Node\Form\Form")
 * @ORM\Table(name="nodes.forms_forms")
 */
class Form extends BaseForm
{
    /**
     * @return string
     */
    public function getType()
    {
        return 'form';
    }

    /**
     * @param  \FormBundle\Entity\Node\Entry         $entry
     * @param  \CommonBundle\Entity\General\Language $language
     * @return string
     */
    protected function _getSummary(Entry $entry, Language $language)
    {
        $fieldEntries = $this->_entityManager
            ->getRepository('FormBundle\Entity\Entry')
            ->findAllByFormEntry($entry);

        $result = '';
        foreach ($fieldEntries as $fieldEntry) {
            $result .= $fieldEntry->getField()->getLabel($language) . ': ' . $fieldEntry->getValueString($language) . PHP_EOL;
        }

        return $result;
    }
}
