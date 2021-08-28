<?php

namespace MailBundle\Command;

use MailBundle\Entity\Alias\Academic as AcademicAlias;
use MailBundle\Entity\Alias\External as ExternalAlias;
use Symfony\Component\Console\Input\InputArgument;

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
        parent::configure();

        $this->setName('mail:import-aliases')
            ->setDescription('Import alias files')
            ->addOption('flush', 'f', null, 'Flush the created aliases to the database')
            ->addArgument('file', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'The files to import');
    }

    protected function invoke()
    {
        $files = $this->getArgument('file');
        if (!is_array($files)) {
            $files = array($files);
        }

        $pwd = getenv('PWD');
        foreach ($files as $file) {
            $this->loadFile($pwd . '/' . $file);
        }

        if ($this->getOption('flush')) {
            $this->write('Flushing to database...');
            $this->getEntityManager()->flush();
            $this->writeln(" <fg=green>\u{2713}</fg=green>", true);
        }
    }

    private function loadFile($file)
    {
        foreach (file($file) as $line) {
            $parts = explode(':', trim($line));

            $alias = strtolower(trim($parts[0]));
            $mail = strtolower(trim($parts[1]));

            $academic = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneByEmail($mail);

            if ($academic !== null) {
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
