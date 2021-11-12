<?php

namespace ApiBundle\Controller;

use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;
use RuntimeException;

/**
 * MailController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class MailController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function aliasesAction()
    {
        $aliases = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\Alias')
            ->findAll();

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Type' => 'text/plain',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'aliases' => $aliases,
            )
        );
    }

    public function getAliasesAction()
    {
        return $this->aliasesAction();
    }

    public function listsAction()
    {
        $lists = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\MailingList')
            ->findAll();

        $data = array();

        foreach ($lists as $list) {
            $entries = $this->getEntityManager()
                ->getRepository('MailBundle\Entity\MailingList\Entry')
                ->findByList($list);

            $addresses = array_map(
                function ($entry) {
                    return $entry->getEmailAddress();
                },
                $entries
            );
            $addressesString = implode(', ', $addresses);

            if (count($addresses) > 0) {
                $data[] = array('name' => $list->getName(), 'addresses' => $addressesString);
            }
        }

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Type' => 'text/plain',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $data,
            )
        );
    }

    public function getListsAction()
    {
        return $this->listsAction();
    }

    public function listsArchiveAction()
    {
        throw new RuntimeException('The listsArchive endpoint has been deprecated');
    }

    public function getListsArchiveAction()
    {
        return $this->listsArchiveAction();
    }
}
