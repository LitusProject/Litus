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

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait,
    Exception,
    Symfony\Component\Console\Input\InputInterface as Input,
    Symfony\Component\Console\Output\OutputInterface as Output,
    Zend\ServiceManager\ServiceLocatorAwareTrait as ZendServiceLocatorAwareTrait;

abstract class Command extends \Symfony\Component\Console\Command\Command implements \CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface
{
    use ZendServiceLocatorAwareTrait;
    use ServiceLocatorAwareTrait;

    /**
     * @var Input
     */
    protected $input;

    /**
     * @var Output
     */
    protected $output;

    /**
     * @return int|void
     */
    protected function execute(Input $input, Output $output)
    {
        $this->input = $input;
        $this->output = $output;

        try {
            return $this->executeCommand();
        } catch (Exception $e) {
            if ('production' == getenv('APPLICATION_ENV')) {
                $this->getService('lilo')
                    ->sendException($e);
            }

            throw $e;
        }
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
     * @param string  $string the string to write
     * @param boolean $raw    whether to output the string raw
     */
    public function write($string, $raw = false)
    {
        if ($raw) {
            $this->output->write($string);
        } else {
            $this->output->write(
                sprintf('[<%1$s>%2$20s</%1$s>] %3$s', $this->getLogNameTag(), $this->getLogName(), $string)
            );
        }
    }

    /**
     * @param string  $string the string to write
     * @param boolean $raw    whether to output the string raw
     */
    public function writeln($string, $raw = false)
    {
        if ($raw || false === $this->getLogName()) {
            $this->output->writeln($string);
        } else {
            $this->output->writeln(
                sprintf('[<%1$s>%2$20s</%1$s>] %3$s', $this->getLogNameTag(), $this->getLogName(), $string)
            );
        }
    }

    /**
     * @param  string $name
     * @return mixed
     */
    protected function getOption($name)
    {
        return $this->input->getOption($name);
    }

    /**
     * @return boolean
     */
    protected function hasOption($name)
    {
        return $this->input->hasOption($name);
    }

    /**
     * @param  string $name
     * @return mixed
     */
    protected function getArgument($name)
    {
        return $this->input->getArgument($name);
    }

    /**
     * @return boolean
     */
    protected function hasArgument($name)
    {
        return $this->input->hasArgument($name);
    }

    /**
     * @return \Zend\Console\Console
     */
    protected function getConsole()
    {
        return $this->getServiceLocator()->get('Console');
    }

    /**
     * @return \Symfony\Component\Console\Helper\QuestionHelper
     */
    protected function getQuestion()
    {
        return $this->getHelperSet()->get('question');
    }
}
