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

namespace CommonBundle\Component\Piwik;

use Zend\Http\Client;

/**
 * This class represents part of the Piwik Analytics API.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Analytics
{
    /**
     * @var string The API's URL
     */
    private $url = '';

    /**
     * @var string The authentication token that should be used
     */
    private $tokenAuth = '';

    /**
     * @var integer The ID of the site that should be queried
     */
    private $idSite = 0;

    /**
     * @param string  $url       The API's URL
     * @param string  $tokenAuth The authentication token that should be used
     * @param integer $idSite    The ID of the site that should be queried
     */
    public function __construct($url, $tokenAuth, $idSite)
    {
        $this->url = $url;
        $this->tokenAuth = $tokenAuth;
        $this->idSite = $idSite;
    }

    /**
     * Returns the number of unique visitors in the given period.
     *
     * @param  string $date   The period over which we want to query
     * @param  string $period The resolution of the date argument
     * @return array|integer
     */
    public function getUniqueVisitors($date = 'today', $period = 'day')
    {
        $parameters = array(
            'method' => 'VisitsSummary.getUniqueVisitors',
            'date'   => $date,
            'period' => $period,
        );

        $data = $this->getData($parameters);
        if ($data === null) {
            return null;
        }

        if (count($data) == 1) {
            return $data['value'];
        }

        return $data;
    }

    /**
     * Returns the live counter data.
     *
     * @param  integer $lastMinutes The amount of time we should go back, in minutes
     * @return array
     */
    public function getLiveCounters($lastMinutes = 30)
    {
        $parameters = array(
            'method'      => 'Live.getCounters',
            'lastMinutes' => $lastMinutes,
        );

        $data = $this->getData($parameters);
        if ($data === null) {
            return array(
                'visits'  => 'N/A',
                'actions' => 'N/A',
            );
        }

        return array(
            'visits'  => $data[0]->visits,
            'actions' => $data[0]->actions,
        );
    }

    /**
     * Retrieves the data at the given URI.
     *
     * @param  array $parameters The request's parameters
     * @return array|null
     */
    private function getData($parameters)
    {
        try {
            $client = new Client(
                $this->url,
                array(
                    'timeout' => 5,
                )
            );

            $client->setParameterGet(
                array_merge(
                    array(
                        'module'     => 'API',
                        'format'     => 'json',
                        'token_auth' => $this->tokenAuth,
                        'idSite'     => $this->idSite,
                    ),
                    $parameters
                )
            );

            return (array) json_decode($client->send()->getBody());
        } catch (\Throwable $e) {
            return null;
        }
    }
}
