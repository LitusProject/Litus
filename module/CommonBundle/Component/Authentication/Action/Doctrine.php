<?php

namespace CommonBundle\Component\Authentication\Action;

use CommonBundle\Component\Authentication\Result;
use CommonBundle\Entity\User\Code;
use Doctrine\ORM\EntityManager;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\TransportInterface;

/**
 * The action that should be taken after authentication.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Doctrine implements \CommonBundle\Component\Authentication\Action
{
    /**
     * @var EntityManager The EntityManager instance
     */
    private $entityManager;

    /**
     * @var TransportInterface The mail transport interface
     */
    private $mailTransport;

    /**
     * @param EntityManager      $entityManager The EntityManager instance
     * @param TransportInterface $mailTransport The mail transport interface
     */
    public function __construct(EntityManager $entityManager, TransportInterface $mailTransport)
    {
        $this->entityManager = $entityManager;
        $this->mailTransport = $mailTransport;
    }

    /**
     * The authorization has failed.
     *
     * @param  Result $result
     * @return void
     */
    public function failedAction(Result $result)
    {
        if (!$result->hasPersonObject() || !$result->getPersonObject()->hasCredential()) {
            return;
        }

        $result->getPersonObject()->setFailedLogins(
            $result->getPersonObject()->getFailedLogins() + 1
        );

        if ($result->getPersonObject()->getFailedLogins() >= 5 && $result->getPersonObject()->getCode() === null) {
            do {
                $code = md5(uniqid(rand(), true));
                $found = $this->entityManager
                    ->getRepository('CommonBundle\Entity\User\Code')
                    ->findOneByCode($code);
            } while (isset($found));

            $code = new Code($code);
            $this->entityManager->persist($code);

            $result->getPersonObject()->setCode($code);

            $language = $result->getPersonObject()->getLanguage();
            if ($language === null) {
                $language = $this->entityManager
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findOneByAbbrev('en');
            }

            $mailData = unserialize(
                $this->entityManager
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.account_deactivated_mail')
            );

            $message = $mailData[$language->getAbbrev()]['content'];
            $subject = $mailData[$language->getAbbrev()]['subject'];

            $mailaddress = $this->entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('system_mail_address');

            $mailname = $this->entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('system_mail_name');

            $mail = new Message();
            $mail->setEncoding('UTF-8')
                ->setBody(str_replace(array('{{ name }}', '{{ code }}'), array($result->getPersonObject()->getFullName(), $code->getCode()), $message))
                ->setFrom($mailaddress, $mailname)
                ->addTo($result->getPersonObject()->getEmail(), $result->getPersonObject()->getFullName())
                ->setSubject($subject);

            if (getenv('APPLICATION_ENV') != 'development') {
                $this->mailTransport->send($mail);
            }
        }
        $this->entityManager->flush();
    }

    /**
     * The authorization was successful.
     *
     * @param  Result $result
     * @return void
     */
    public function succeededAction(Result $result)
    {
        $result->getPersonObject()->setFailedLogins(0);
        $this->entityManager->flush();
    }
}
