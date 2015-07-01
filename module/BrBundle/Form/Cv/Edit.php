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

namespace BrBundle\Form\Cv;

use BrBundle\Entity\Cv\Entry as CvEntry;

/**
 * Edit Cv
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Edit extends Add
{
    /**
     * @var CvEntry
     */
    private $entry;

    public function init()
    {
        parent::init();

        $this->remove('submit');

        $this->addSubmit('Save Changes');

        if (null !== $this->entry) {
            $this->bind($this->entry);
        }
    }

    /**
     * @param  CvEntry $entry
     * @return self
     */
    public function setEntry(CvEntry $entry)
    {
        $this->entry = $entry;

        return $this;
    }
}
