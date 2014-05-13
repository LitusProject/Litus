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
class TestConfig extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        $this
            ->setName('common:test-config')
            ->setDescription('Test configuration values.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command tests all serialized configuration values.
EOT
        );
    }

    protected function executeCommand()
    {
        $values = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->findAll();

        $number = 0;
        foreach ($values as $value) {
            if (strpos($value->getValue(), 'a:') !== 0)
                continue;
            try {
                $number++;
                unserialize($value->getValue());
            } catch (\Exception $e) {
                $this->writeln('Couldn\'t unserialize <comment>' . $value->getKey() . '</comment>');
            }
        }
        $this->writeln('Tested <comment>' . $number . '</comment> config values.');
    }

    protected function getLogName()
    {
        return 'TestConfig';
    }
}
