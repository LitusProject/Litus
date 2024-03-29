<?php

namespace TicketBundle\Form\Admin\Ticket;

class Csv extends \CommonBundle\Component\Form\Admin\Form
{
    const FILE_SIZE = '10MB';

    protected $hydrator = 'TicketBundle\Hydrator\Ticket';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'file',
                'name'       => 'file',
                'label'      => 'Ticket csv',
                //                'required'   => true,
                'attributes' => array(
                    'data-help' => 'The maximum file size is ' . self::FILE_SIZE . '.',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'FileSize',
                                'options' => array(
                                    'max' => self::FILE_SIZE,
                                ),
                            ),
                            array(
                                'name'    => 'FileExtension',
                                'options' => array(
                                    'extension' => 'csv',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'ticket_csv');
    }
}
