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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Console;

use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\ConfigTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\ConsoleTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\DoctrineTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\MailTransportTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\SentryTrait;
use Exception;
use Raven_ErrorHandler;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;

abstract class Command extends \Symfony\Component\Console\Command\Command implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    use ConfigTrait;
    use ConsoleTrait;
    use DoctrineTrait;
    use MailTransportTrait;
    use SentryTrait;

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
     * @param  string  $string the string to write
     * @param  boolean $raw    whether to output the string raw
     * @return void
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
     * @param  string  $string the string to write
     * @param  boolean $raw    whether to output the string raw
     * @return void
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
}
