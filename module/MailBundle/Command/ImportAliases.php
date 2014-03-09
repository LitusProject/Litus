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

namespace MailBundle\Command;

use Symfony\Component\Console\Input\InputArgument,
    MailBundle\Entity\Alias\Academic as AcademicAlias,
    MailBundle\Entity\Alias\External as ExternalAlias;

/**
 * ImportAliases
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class ImportAliases extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        $this
            ->setName('mail:import-aliases')
            ->setAliases(array('mail:aliases:import'))
            ->setDescription('import alias files')
            ->addOption('flush', 'f', null, 'flush the created aliases to the database')
            ->addArgument('file', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'the files to import')
            ->setHelp(<<<EOT
The %command.name% command imports the given alias <fg=blue>files</fg=blue> and stores them
if the <fg=blue>--flush</fg=blue> flag is given.
EOT
        );
    }

    protected function executeCommand()
    {
        $files = $this->getArgument('file');
        if (!is_array($files))
            $files = array($files);

        // get PWD of shell that called public/index.php
        // the PWD of php itself is changed in said file
        $pwd = getenv('PWD');

        foreach ($files as $file) {
            $this->_loadFile($pwd . '/' . $file);
        }

        if ($this->getOption('flush')) {
            $this->write('Flushing to database...');
            $this->getEntityManager()->flush();
            $this->writeln(' done.', true);
        }
    }

    protected function getLogName()
    {
        return 'ImportAlias';
    }

    /**
     * @param string $file
     */
    private function _loadFile($file)
    {
        foreach (file($file) as $line) {
            $parts = explode(':', trim($line));

            $alias = strtolower(trim($parts[0]));
            $mail = strtolower(trim($parts[1]));

            $academic = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneByEmail($mail);

            if (null !== $academic) {
                $this->writeln('Academic: ' . $academic->getFullName());

                $newAlias = new AcademicAlias($alias, $academic);
            } else {
                $this->writeln('External: ' . $mail);

                $newAlias = new ExternalAlias($alias, $mail);
            }

            $this->getEntityManager()->persist($newAlias);
        }
    }
}
