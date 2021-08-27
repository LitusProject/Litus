<?php

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
