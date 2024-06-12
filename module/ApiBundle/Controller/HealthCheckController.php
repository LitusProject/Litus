<?php

namespace ApiBundle\Controller;

use ApiBundle\Component\Controller\ActionController\ApiController;
use Laminas\Http\Response;

/**
 * HealthCheckController
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class HealthCheckController extends ApiController
{
    public function pingAction(): Response
    {
        return new Response(); // Return a response with status code 200
    }
}
