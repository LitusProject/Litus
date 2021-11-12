<?php

namespace CudiBundle\Command;

use Symfony\Component\Console\Input\InputArgument;

class TestPrinter extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        parent::configure();

        $this->setName('cudi:test-printer')
            ->setDescription('Tests the printers')
            ->addArgument('printer', InputArgument::REQUIRED, 'The printer name')
            ->addArgument('ticket', InputArgument::REQUIRED, 'The ticket type');
    }

    protected function invoke()
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
     * @return integer
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
