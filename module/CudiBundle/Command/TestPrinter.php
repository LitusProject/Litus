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

namespace CudiBundle\Command;

use Symfony\Component\Console\Input\InputArgument;

class TestPrinter extends \CommonBundle\Component\Console\Command
{
    /**
     * @return null
     */
    protected function configure()
    {
        $this
            ->setName('cudi:test-printer')
            ->setDescription('Tests the printers')
            ->addArgument('printer', InputArgument::REQUIRED, 'The printer name')
            ->addArgument('ticket', InputArgument::REQUIRED, 'The ticket type')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command sends a test ticket to a printer.

The printer to test is set by the <comment>printer</comment> argument. Possible values
are defined by the '<fg=blue>cudi.printers</fg=blue>' configuration entry.

The type of the ticket to send is set by the <comment>ticket</comment> argument. Possible values
are '<fg=blue>signin</fg=blue>', '<fg=blue>collect</fg=blue>' and '<fg=blue>sale</fg=blue>'.
EOT
            );
    }

    /**
     * @return integer|null
     */
    protected function executeCommand()
    {
        $printer = $this->getPrinter();
        if ($printer === null) {
            return 1;
        }

        $ticket = $this->getTicket();
        if ($ticket === null) {
            return 2;
        }

        $this->send($printer, $ticket);
    }

    /**
     * @return string
     */
    protected function getLogName()
    {
        return 'TestPrinter';
    }

    /**
     * @return string|null
     */
    private function getPrinter()
    {
        $printers = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.printers')
        );

        $printer = $this->getArgument('printer');

        if (isset($printers[$printer])) {
            return $printers[$printer];
        }

        $this->writeln('<error>Error:</error> Invalid printer name: ' . $printer);
        $this->writeln('Possible printers:');
        foreach ($printers as $key => $printer) {
            $this->writeln('    -> ' . $key . ':' . $printer);
        }

        return null;
    }

    /**
     * @return integer|null
     */
    private function getTicket()
    {
        switch ($this->getArgument('ticket')) {
            case 'signin':
                return 1;
            case 'collect':
                return 2;
            case 'sale':
                return 3;
            default:
                $this->writeln('<error>Error:</error> Invalid ticket type: ' . $this->getArgument('ticket'));
                $this->writeln('Possible ticket types:');
                $this->writeln('    -> signin');
                $this->writeln('    -> collect');
                $this->writeln('    -> sale');

                return null;
        }
    }

    /**
     * @param  string  $printer
     * @param  integer $ticket
     * @return null
     */
    private function send($printer, $ticket)
    {
        $ticket = (object) array(
            'type'        => $ticket,
            'id'          => 's0210425',
            'barcode'     => '1234567890',
            'name'        => 'Kristof Mariën',
            'queuenumber' => '3',
            'totalAmount' => '63,00',
            'title'       => 'Litus Cursusdienst',
            'items'       => array(
                array(
                    'title'   => 'Fundamentals of Computer Graphics',
                    'price'   => '45,00',
                    'barcode' => '12345',
                    'number'  => '1',
                ),
                array(
                    'title'   => 'De Bijbel',
                    'price'   => '8,00',
                    'barcode' => '54321',
                    'number'  => '2',
                ),
            ),
        );

        $data = json_encode(
            (object) array(
                'command' => 'PRINT',
                'id'      => $printer,
                'ticket'  => $ticket,
                'key'     => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.printer_socket_key'),
            )
        );

        $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
        socket_connect(
            $socket,
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.print_socket_address'),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.print_socket_port')
        );
        socket_write($socket, $data);
        socket_close($socket);
    }
}
