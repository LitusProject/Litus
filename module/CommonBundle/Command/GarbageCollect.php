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

namespace CommonBundle\Command;

/**
 * Performs garbage collection on the sessions.
 */
class GarbageCollect extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        $this
            ->setName('common:gc')
            ->setDescription('Perform Garbage Collection on Sessions.')
            ->addOption('all', 'a', null, 'Garbage Collect all sessions')
            ->addOption('sessions', 'se', null, 'Garbage Collect password sessions')
            ->addOption('shibboleth', 'sh', null, 'Garbage Collect shibboleth sessions')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command removes the expired password sessions and/or shibboleth sessions.
EOT
        );
    }

    public function executeCommand()
    {
        $doneSomething = false;

        if ($this->getOption('all') || $this->getOption('sessions')) {
            $this->_gcSessions();
            $doneSomething = true;
        }

        if ($this->getOption('all') || $this->getOption('shibboleth')) {
            $this->_gcShibboleth();
            $doneSomething = true;
        }

        if (!$doneSomething) {
            $this->writeln('<error>Error:</error> you must give at least one option.');
            return 1;
        }
    }

    protected function getLogName()
    {
        return 'GarbageCollect';
    }

    private function _gcSessions()
    {
        $em = $this->getEntityManager();
        $sessions = $em->getRepository('CommonBundle\Entity\User\Session')
            ->findAllExpired();

        foreach($sessions as $session)
            $em->remove($session);

        $this->writeln('Removed <comment>' . count($sessions) . '</comment> expired sessions');

        $em->flush();
    }

    private function _gcShibboleth()
    {
        $em = $this->getEntityManager();
        $sessions = $em->getRepository('CommonBundle\Entity\User\Shibboleth\Code')
            ->findAllExpired();

        foreach($sessions as $session)
            $em->remove($session);

        $this->writeln('Removed <comment>' . count($sessions) . '</comment> expired Shibboleth codes');

        $em->flush();
    }
}
