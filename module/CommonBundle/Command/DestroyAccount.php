<?php

namespace CommonBundle\Command;

use CommonBundle\Entity\User\Person\Academic;
use DateTime;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Destroy the personal data of an account.
 */
class DestroyAccount extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        parent::configure();

        $this->setName('common:destroy-account')
            ->setDescription('Destroy the personal data of an account')
            ->addArgument('id', InputArgument::REQUIRED, 'The ID of the account');
    }

    protected function invoke()
    {
        $this->destroy($this->getArgument('id'));
    }

    private function destroy($id)
    {
        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneById($id);

        if ($person->canLogin()) {
            $this->writeln('<error>The account must be disabled first.</error>');

            return;
        }

        $fullName = $person->getFullName();

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Do you want to destroy the account of ' . $person->getFullName() . '?', false);
        $confirmed = $helper->ask($this->input, $this->output, $question);

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

                if (file_exists($filePath . '/' . $person->getPhotoPath())) {
                    unlink($filePath . '/' . $person->getPhotoPath());
                }

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
            $this->writeln('Account of <comment>' . $fullName . '</comment> was destroyed!');
        }
    }
}
