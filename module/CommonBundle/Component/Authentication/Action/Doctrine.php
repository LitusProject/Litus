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

namespace CommonBundle\Component\Authentication\Action;

use CommonBundle\Component\Authentication\Result,
    CommonBundle\Entity\User\Code,
    Doctrine\ORM\EntityManager,
    Zend\Mail\Message,
    Zend\Mail\Transport\TransportInterface;

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

        $result->getPersonObject()
            ->setFailedLogins($result->getPersonObject()->getFailedLogins() + 1);

        if ($result->getPersonObject()->getFailedLogins() >= 5 && null === $result->getPersonObject()->getCode()) {
            do {
                $code = md5(uniqid(rand(), true));
                $found = $this->entityManager
                    ->getRepository('CommonBundle\Entity\User\Code')
                    ->findOneByCode($code);
            } while (isset($found));

            $code = new Code($code);
            $this->entityManager->persist($code);

            $result->getPersonObject()
                ->setCode($code);

            if (!($language = $result->getPersonObject()->getLanguage())) {
                $language = $this->entityManager->getRepository('CommonBundle\Entity\General\Language')
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

            if ('development' != getenv('APPLICATION_ENV')) {
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
        $result->getPersonObject()
            ->setFailedLogins(0);
        $this->entityManager->flush();
    }
}
