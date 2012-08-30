<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Authentication\Action;

use CommonBundle\Entity\Users\Code,
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
     * @var \Doctrine\ORM\EntityManager
     */
    private $_entityManager;

    /**
     * @var \Zend\Mail\Transport\TransportInterface
     */
    private $_mailTransport;

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
        if (null === $result->getPersonObject())
            return;

        $result->getPersonObject()
            ->setFailedLogins($result->getPersonObject()->getFailedLogins() + 1);

        if ($result->getPersonObject()->getFailedLogins() >= 5 && null === $result->getPersonObject()->getCode()) {
            do {
                $code = md5(uniqid(rand(), true));
                $found = $this->_entityManager
                    ->getRepository('CommonBundle\Entity\Users\Code')
                    ->findOneByCode($code);
            } while(isset($found));

            $code = new Code($code);
            $this->_entityManager->persist($code);

            $result->getPersonObject()
                ->setCode($code);

            $email = $this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('account_deactivated_mail');

            $subject = $this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('account_deactivated_subject');

            $mailaddress = $this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('system_mail_address');

            $mailname = $this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('system_mail_name');

            $mail = new Message();
            $mail->setBody(str_replace('{{ code }}', $code->getCode() , $email))
                ->setFrom($mailaddress, $mailname)
                ->addTo($result->getPersonObject()->getEmail(), $result->getPersonObject()->getFullName())
                ->setSubject($subject);

            if ('production' == getenv('APPLICATION_ENV'))
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
