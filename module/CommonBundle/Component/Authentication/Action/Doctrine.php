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

use CommonBundle\Entity\User\Code,
    Doctrine\ORM\EntityManager,
    Zend\Mail\Transport\TransportInterface,
    Zend\Mail\Message;

/**
 * The action that should be taken after authentication.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Doctrine implements \CommonBundle\Component\Authentication\Action
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager;

    /**
     * @var \Zend\Mail\Transport\TransportInterface The mail transport interface
     */
    private $_mailTransport;

    /**
     * @param \Doctrine\ORM\EntityManager             $entityManager The EntityManager instance
     * @param \Zend\Mail\Transport\TransportInterface $mailTransport The mail transport interface
     */
    public function __construct(EntityManager $entityManager, TransportInterface $mailTransport)
    {
        $this->_entityManager = $entityManager;
        $this->_mailTransport = $mailTransport;
    }

    /**
     * The authorization has failed.
     *
     * @param $result
     * @return void
     */
    public function failedAction($result)
    {
        if (null === $result->getPersonObject() || !$result->getPersonObject()->hasCredential())
            return;

        $result->getPersonObject()
            ->setFailedLogins($result->getPersonObject()->getFailedLogins() + 1);

        if ($result->getPersonObject()->getFailedLogins() >= 5 && null === $result->getPersonObject()->getCode()) {
            do {
                $code = md5(uniqid(rand(), true));
                $found = $this->_entityManager
                    ->getRepository('CommonBundle\Entity\User\Code')
                    ->findOneByCode($code);
            } while (isset($found));

            $code = new Code($code);
            $this->_entityManager->persist($code);

            $result->getPersonObject()
                ->setCode($code);

            if (!($language = $result->getPersonObject()->getLanguage())) {
                $language = $this->_entityManager->getRepository('CommonBundle\Entity\General\Language')
                    ->findOneByAbbrev('en');
            }

            $mailData = unserialize(
                $this->_entityManager
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('common.account_deactivated_mail')
            );

            $message = $mailData[$language->getAbbrev()]['content'];
            $subject = $mailData[$language->getAbbrev()]['subject'];

            $mailaddress = $this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('system_mail_address');

            $mailname = $this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('system_mail_name');

            $mail = new Message();
            $mail->setBody(str_replace('{{ code }}', $code->getCode() , $message))
                ->setFrom($mailaddress, $mailname)
                ->addTo($result->getPersonObject()->getEmail(), $result->getPersonObject()->getFullName())
                ->setSubject($subject);

            if ('development' != getenv('APPLICATION_ENV'))
                $this->_mailTransport->send($mail);
        }
        $this->_entityManager->flush();
    }

    /**
     * The authorization was successful.
     *
     * @param $result
     * @return void
     */
    public function succeededAction($result)
    {
        $result->getPersonObject()
            ->setFailedLogins(0);
        $this->_entityManager->flush();
    }
}
