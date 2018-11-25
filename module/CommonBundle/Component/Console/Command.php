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

use CommonBundle\Component\ServiceManager\ServiceLocatorAware\ConfigTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\DoctrineTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\MailTransportTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\SentryClientTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use CommonBundle\Component\Util\AcademicYear;
use DateTime;
use Ko\Mixin\ProcessTitle;
use ReflectionClass;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;

abstract class Command extends \Symfony\Component\Console\Command\Command implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    use ConfigTrait;
    use DoctrineTrait;
    use MailTransportTrait;
    use SentryClientTrait;

    use ProcessTitle;

    /**
     * @var Input
     */
    protected $input;

    /**
     * @var Output
     */
    protected $output;

    /**
     * @return integer|void
     */
    protected function execute(Input $input, Output $output)
    {
        $this->input = $input;
        $this->output = $output;

        $formatter = $this->output->getFormatter();
        $formatter->setStyle('error', new OutputFormatterStyle('red'));
        $formatter->setStyle('info', new OutputFormatterStyle('blue'));
        $formatter->setStyle('comment', new OutputFormatterStyle('yellow'));
        $formatter->setStyle('question', new OutputFormatterStyle('white'));

        $this->setProcessTitle('litus: ' . $this->getName());

        return $this->invoke();
    }

    /**
     * @return integer|void
     */
    abstract protected function invoke();

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
     * @return array
     */
    protected function getOptions()
    {
        return $this->input->getOptions();
    }

    /**
     * @return string
     */
    protected function getLogFormat()
    {
        $nameLength = $this->getLogNameLength();
        if ($nameLength > 0) {
            return '<%1$s>[%2$s]</%1$s> <%3$s>[%4$' . $nameLength . 's]</%3$s> %5$s';
        }

        return '<%1$s>[%2$s]</%1$s> <%3$s>[%4$s]</%3$s> %5$s';
    }

    /**
     * @return string
     */
    protected function getLogDateTag()
    {
        return 'options=bold';
    }

    /**
     * @return string
     */
    protected function getLogNameLength()
    {
        return 0;
    }

    /**
     * @return string
     */
    protected function getLogNameTag()
    {
        return 'fg=blue;options=bold';
    }

    /**
     * @return string
     */
    protected function getLogName()
    {
        return (new ReflectionClass($this))->getShortName();
    }

    /**
     * @param  string  $string The string to write
     * @param  boolean $raw    Whether to output the string raw
     * @return void
     */
    public function write($string, $raw = false)
    {
        if ($raw) {
            $this->output->write($string);
        } else {
            $this->output->write(
                sprintf(
                    $this->getLogFormat(),
                    $this->getLogDateTag(),
                    (new DateTime())->format('d/m/Y H:i:s'),
                    $this->getLogNameTag(),
                    $this->getLogName(),
                    $string
                )
            );
        }
    }

    /**
     * @param  string  $string The string to write
     * @param  boolean $raw    Whether to output the string raw
     * @return void
     */
    public function writeln($string, $raw = false)
    {
        if ($raw) {
            $this->output->writeln($string);
        } else {
            $this->output->writeln(
                sprintf(
                    $this->getLogFormat(),
                    $this->getLogDateTag(),
                    (new DateTime())->format('d/m/Y H:i:s'),
                    $this->getLogNameTag(),
                    $this->getLogName(),
                    $string
                )
            );
        }
    }

    /**
     * Get the current academic year
     *
     * @param  boolean $organization
     * @return AcademicYear
     */
    protected function getCurrentAcademicYear($organization = false)
    {
        if ($organization) {
            return AcademicYear::getOrganizationYear($this->getEntityManager());
        }

        return AcademicYear::getUniversityYear($this->getEntityManager());
    }
}
