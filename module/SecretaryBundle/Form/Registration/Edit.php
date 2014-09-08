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

namespace SecretaryBundle\Form\Registration;

use LogicException;

/**
 * Edit Registration
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    public function init()
    {
        if (null === $this->metaData && null === $this->academic) {
            throw new LogicException('Cannot edit null registration');
        }

        parent::init();

        $academic = null !== $this->metaData
            ? $this->metaData->getAcademic()
            : $this->academic;
        $academicYear = $this->getCurrentAcademicYear(false);

        if (
            null !== $academic->getOrganizationStatus($academicYear)
            && 'praesidium' == $academic->getOrganizationStatus($academicYear)->getStatus()
        ) {
            $this->get('organization_info')
                ->get('become_member')
                ->setValue(false)
                ->setAttribute('disabled', true);
        }

        $this->get('register')
            ->setLabel('Save');
    }
}
