<?php

return array(
    'submenus' => array(
        'Corporate Relations' => array(
            'subtitle'    => array('Companies','CVs', 'Products'),
            'items'       => array(
                'br_admin_collaborator' => array(
                    'title' => 'Collaborators',
                    'help'  => 'Here you can find a list of all the people that are collaborating with corporate relations.',
                ),
                'br_admin_company' => array(
                    'title' => 'Companies',
                    'help'  => 'Here you can find all the companies that are registered in the database.',
                ),
                'br_admin_contract' => array(
                    'title' => 'Contracts',
                    'help'  => 'Here you can find a list of contracts that were generated by the creation of orders.  You can sign, edit, download and view these contracts.',
                ),
                'br_admin_cv_entry' => array(
                    'title' => 'CVs',
                    'help'  => 'Here you can find a list of all the CVs that are uploaded on the site.',
                ),
                'br_admin_invoice' => array(
                    'title' => 'Invoices',
                    'help'  => 'Here you can find a list of invoices that are automaticly generated when a contract is signed.',
                ),
                'br_admin_order' => array(
                    'title' => 'Orders',
                    'help'  => 'Manage the orders of each company.  Orders can be added or deleted.  Creating an order instantly creates a contract that can be signed.  A the order of a signed contract cannot be removed.',
                ),
                'br_admin_overview' => array(
                    'title'  => 'Overview',
                    'action' => 'person',
                    'help'   => 'A general overview for each member or company individually.',
                ),
                'br_admin_product' => array(
                    'title' => 'Products',
                    'help'  => 'Here you can manage all the products VTK has to offer to every company.',
                ),
                'br_admin_request' => array(
                    'title' => 'Requests',
                    'help'  => 'Here you can manage all requests that are made by the companies.',
                ),
                'br_admin_event' => array(
                    'title' => 'Events',
                    'help'  => 'Here you can manage all events by VTK Corporate Relations.',
                ),
                'br_admin_communication' => array(
                    'title' => 'Communications',
                    'help'  => 'Here you can view all outgoing communications.',
                ),
                'br_admin_job' => array(
                    'title' => 'Jobs',
                    'help'  => 'Here you can view all jobs.',
                ),
                'br_admin_studentcompanymatch' => array(
                    'title' => 'Matches',
                    'help'  => 'Here you can view all matches.',
                ),
            ),
            'controllers' => array(
                'br_admin_company_event',
                'br_admin_company_job',
                'br_admin_company_user',
                'br_admin_company_logo',
                'br_admin_job',
                'br_admin_studentcompanymatch',
            ),
        ),
    ),
);
