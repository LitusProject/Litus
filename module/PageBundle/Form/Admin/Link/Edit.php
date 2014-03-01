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

namespace PageBundle\Form\Admin\Link;

use Doctrine\ORM\EntityManager,
    PageBundle\Entity\Link,
    Zend\Form\Element\Submit;

/**
 * Edit Link
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \PageBundle\Entity\Link     $link          The link we're going to modify
     * @param null|string|int             $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Link $link, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'link_edit');
        $this->add($field);

        $this->_populateFromLink($link);
    }

    private function _populateFromLink(Link $link)
    {
        $data = array();
        foreach ($this->getLanguages() as $language) {
            $data['name_' . $language->getAbbrev()] = $link->getName($language, false);
            $data['url_' . $language->getAbbrev()] = $link->getUrl($language, false);
        }

        $data['category'] = $link->getCategory()->getId();
        $data['parent_' . $link->getCategory()->getId()] = null !== $link->getParent() ? $link->getParent()->getId() : '';

        $this->setData($data);
    }
}
