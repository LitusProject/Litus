<?php

namespace CommonBundle\Command;

/**
 * Cleans up expired sessions.
 */
class CleanupSessions extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        parent::configure();

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
