<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace GalleryBundle\Form\Admin\Album;

use CommonBundle\Component\Form\Bootstrap\Element\Submit,
    Doctrine\ORM\EntityManager,
	Doctrine\ORM\QueryBuilder,
    GalleryBundle\Entity\Album\Album;

/**
 * Edit an album.
 */
class Edit extends Add
{
	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
	 * @param mixed $opts The validator's options
	 */
    public function __construct(EntityManager $entityManager, Album $album, $opts = null)
    {
        parent::__construct($entityManager, $opts);
        
        $this->album = $album;
        
        $this->removeElement('submit');
        
        $field = new Submit('submit');
        $field->setLabel('Save');
        $this->addElement($field);
        
        $this->setActionsGroup(array('submit'));
        
        $this->populateFromAlbum($album);
    }
}
