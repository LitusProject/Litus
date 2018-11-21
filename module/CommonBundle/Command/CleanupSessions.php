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

namespace CommonBundle\Command;

/**
 * Cleans up expired sessions.
 */
class CleanupSessions extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        $this->setName('common:cleanup-sessions')
            ->setDescription('Cleanup expired sessions and Shibboleth codes');
    }

    protected function invoke()
    {
        $this->gcSessions();
        $this->gcShibboleth();
    }

    private function gcSessions()
    {
        $em = $this->getEntityManager();
        $sessions = $em->getRepository('CommonBundle\Entity\User\Session')
            ->findAllExpired();

        foreach ($sessions as $session) {
            $em->remove($session);
        }

        $this->writeln('Removed <comment>' . count($sessions) . '</comment> expired sessions');

        $em->flush();
    }

    private function gcShibboleth()
    {
        $em = $this->getEntityManager();
        $sessions = $em->getRepository('CommonBundle\Entity\User\Shibboleth\Code')
            ->findAllExpired();

        foreach ($sessions as $session) {
            $em->remove($session);
        }

        $this->writeln('Removed <comment>' . count($sessions) . '</comment> expired Shibboleth codes');

        $em->flush();
    }
}
