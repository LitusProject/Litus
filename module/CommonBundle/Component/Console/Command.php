<?php

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
    public function execute(Input $input, Output $output)
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
    protected abstract function executeCommand();

    /**
     * @return string
     */
    protected abstract function getLogName();

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
    public function write($string, $raw = false)
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
    public function writeln($string, $raw = false)
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
    public function getOption($name)
    {
        return $this->input->getOption($name);
    }

    /**
     * @return mixed
     */
    public function hasOption($name)
    {
        return $this->input->hasOption($name);
    }

    /**
     * @return mixed
     */
    public function getArgument($name)
    {
        return $this->input->getArgument($name);
    }

    /**
     * @return mixed
     */
    public function hasArgument($name)
    {
        return $this->input->hasArgument($name);
    }

    /**
     * We want an easy method to retrieve the DocumentManager from
     * the DI container.
     *
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    public function getDocumentManager()
    {
        return $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
    }

    /**
     * We want an easy method to retrieve the EntityManager from
     * the DI container.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }

    /**
     * We want an easy method to retrieve the Cache from
     * the DI container.
     *
     * @return \Zend\Cache\Storage\Adapter\Apc
     */
    public function getCache()
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
    public function getMailTransport()
    {
        return $this->getServiceLocator()->get('mail_transport');
    }

    /**
     * Retrieve the common session storage from the DI container.
     *
     * @return \Zend\Session\Container
     */
    public function getSessionStorage()
    {
        return $this->getServiceLocator()->get('common_sessionstorage');
    }

    /**
     * @return \Zend\Console\Console
     */
    public function getConsole()
    {
        return $this->getServiceLocator()->get('Console');
    }
}
