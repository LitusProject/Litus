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

namespace CommonBundle\Component\Console;

use Zend\ServiceManager\ServiceLocatorAwareTrait,
    Symfony\Component\Console\Input\InputInterface as Input,
    Symfony\Component\Console\Output\OutputInterface as Output;

abstract class Command extends \Symfony\Component\Console\Command\Command implements \Zend\ServiceManager\ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var string
     */
    private $_logName;

    /**
     * @var string
     */
    private $_logNameTag;

    /**
     * @return int|void
     */
    protected function execute(Input $input, Output $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->_logName = $this->getLogName();
        $this->_logNameTag = $this->getLogNameTag();

        return $this->executeCommand();
    }

    /**
     * @return int|void
     */
    abstract protected function executeCommand();

    /**
     * @return string
     */
    abstract protected function getLogName();

    /**
     * @return string
     */
    protected function getLogNameTag()
    {
        return 'fg=green;options=bold';
    }

    /**
     * @param string $string the string to write
     * @param boolean $raw whether to output the string raw
     */
    protected function write($string, $raw = false)
    {
        if ($raw) {
            $this->output->write($string);
        } else {
            $this->output->write(
                sprintf('[<%1$s>%2$20s</%1$s>] %3$s', $this->_logNameTag, $this->_logName, $string)
            );
        }
    }

    /**
     * @param string $string the string to write
     * @param boolean $raw whether to output the string raw
     */
    protected function writeln($string, $raw = false)
    {
        if ($raw) {
            $this->output->writeln($string);
        } else {
            $this->output->writeln(
                sprintf('[<%1$s>%2$20s</%1$s>] %3$s', $this->_logNameTag, $this->_logName, $string)
            );
        }
    }

    /**
     * @return mixed
     */
    protected function getOption($name)
    {
        return $this->input->getOption($name);
    }

    /**
     * @return mixed
     */
    protected function hasOption($name)
    {
        return $this->input->hasOption($name);
    }

    /**
     * @return mixed
     */
    protected function getArgument($name)
    {
        return $this->input->getArgument($name);
    }

    /**
     * @return mixed
     */
    protected function hasArgument($name)
    {
        return $this->input->hasArgument($name);
    }

    /**
     * We want an easy method to retrieve the DocumentManager from
     * the DI container.
     *
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function getDocumentManager()
    {
        return $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
    }

    /**
     * We want an easy method to retrieve the EntityManager from
     * the DI container.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }

    /**
     * We want an easy method to retrieve the Cache from
     * the DI container.
     *
     * @return \Zend\Cache\Storage\Adapter\Apc
     */
    protected function getCache()
    {
        if ($this->getServiceLocator()->has('cache'))
            return $this->getServiceLocator()->get('cache');

        return null;
    }

    /**
     * We want an easy method to retrieve the Mail Transport from
     * the DI container.
     *
     * @return \Zend\Mail\Transport\TransportInterface
     */
    protected function getMailTransport()
    {
        return $this->getServiceLocator()->get('mail_transport');
    }

    /**
     * Retrieve the common session storage from the DI container.
     *
     * @return \Zend\Session\Container
     */
    protected function getSessionStorage()
    {
        return $this->getServiceLocator()->get('common_sessionstorage');
    }

    /**
     * @return \Zend\Console\Console
     */
    protected function getConsole()
    {
        return $this->getServiceLocator()->get('Console');
    }
}
