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
include 'init_autoloader.php';

use CommonBundle\Entity\User\Status\Organization as OrganizationStatus;

$app = Zend\Mvc\Application::init(include 'config/application.config.php');
$entityManager = $app->getServiceManager()->get('doctrineormentitymanager');

$year = $entityManager->getRepository('CommonBundle\Entity\General\AcademicYear')->findOneByDate(new DateTime());

$registrations = $entityManager->getRepository('SecretaryBundle\Entity\Registration')
    ->findByAcademicYear($year);

foreach ($registrations as $registration) {
    if ($registration->hasPayed() && !$registration->isCancelled()) {
        $status = $registration->getAcademic()
            ->getOrganizationStatus($year);
        if ($status === null) {
            $registration->getAcademic()
                ->addOrganizationStatus(
                    new OrganizationStatus(
                        $registration->getAcademic(),
                        'member',
                        $year
                    )
                );
        } elseif ($status->getStatus() === 'non_member') {
            $status->setStatus('member');
        }
    }
}

$entityManager->flush();
