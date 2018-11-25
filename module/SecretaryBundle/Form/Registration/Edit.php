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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SecretaryBundle\Form\Registration;

/**
 * Edit Registration
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \SecretaryBundle\Form\Registration\Add
{
    public function init()
    {
        parent::init();

        $academic = $this->metaData !== null ? $this->metaData->getAcademic() : $this->academic;
        $academicYear = $this->getCurrentAcademicYear(false);

        if ($academic->getOrganizationStatus($academicYear) !== null
            && $academic->getOrganizationStatus($academicYear)->getStatus() == 'praesidium'
        ) {
            $organizationInfoFieldset = $this->get('organization_info');
            $organizationInfoFieldset->get('become_member')
                ->setValue(false)
                ->setAttribute('disabled', true);
        }

        $this->get('register')
            ->setValue('Save');
    }
}
