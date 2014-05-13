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

use CommonBundle\Entity\User\Person\Academic,
    DateTime,
    Symfony\Component\Console\Input\InputArgument;

/**
 * Destroy the personal data of an account.
 */
class DestroyAccount extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        $this
            ->setName('common:destroy-account')
            ->setDescription('Destroy the personal data of an account.')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the account')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command destroys the personal data (address, name, ...) from an account.
EOT
        );
    }

    protected function executeCommand()
    {
        $this->_destroyAccount($this->getArgument('id'));
    }

    protected function getLogName()
    {
        return 'DestroyAccount';
    }

    private function _destroyAccount($id)
    {
        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneById($id);

        if ($person->canLogin()) {
            $this->writeln('<error>The account must be disabled first.</error>');

            return;
        }

        $fullName = $person->getFullName();

        $confirmed = $this->getDialog()->askConfirmation(
            $this->output,
            'Do you want to destroy the account of ' . $person->getFullName() . '? [y/n]: ',
            false
        );

        if ($confirmed) {
            $person->setUsername(substr(md5(time()), 0, 50));
            $person->setFirstName(substr(md5(time()), 0, 50));
            $person->setLastName(substr(md5(time()), 0, 50));
            $person->setEmail(substr(md5(time()), 0, 50));
            $person->setPhoneNumber(null);

            if ($person->getAddress() !== null) {
                $person->getAddress()->setStreet(md5(time()))
                    ->setNumber(md5(time()))
                    ->setMailbox(null)
                    ->setPostal(md5(time()))
                    ->setCity(md5(time()));
            }

            if ($person instanceof Academic) {
                $person->setPersonalEmail(substr(md5(time()), 0, 100));
                $person->setUniversityEmail(substr(md5(time()), 0, 100));
                $person->setUniversityIdentification(substr(md5(time()), 0, 8));
                $person->setBirthday(new DateTime());

                $filePath = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.profile_path');

                if (file_exists($filePath . '/' . $person->getPhotoPath()))
                    unlink($filePath . '/' . $person->getPhotoPath());

                $person->setPhotoPath('');

                if ($person->getPrimaryAddress() !== null) {
                    $person->getPrimaryAddress()->setStreet(md5(time()))
                        ->setNumber(md5(time()))
                        ->setMailbox(null)
                        ->setPostal(md5(time()))
                        ->setCity(md5(time()));
                }

                if ($person->getSecondaryAddress() !== null) {
                    $person->getSecondaryAddress()->setStreet(md5(time()))
                        ->setNumber(md5(time()))
                        ->setMailbox(null)
                        ->setPostal(md5(time()))
                        ->setCity(md5(time()));
                }
            }

            $this->getEntityManager()->flush();
            $this->writeln('Account of <comment>' . $fullName . '</comment> was destroyed.');
        }
    }
}
