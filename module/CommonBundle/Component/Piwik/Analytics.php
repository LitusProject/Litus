<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Piwik;

use CommonBundle\Component\Piwik\Api\Image,
    Zend\Http\Client;

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
    private $_url = '';

    /**∏
     * @var string The authentication token that should be used
     */
    private $_tokenAuth = '';

    /**
     * @var string The ID of the site that should be queried
     */
    private $_idSite = 0;

    /**
     * @param string $url The API's URL
     * @param string $tokenAuth The authentication token that should be used
     * @param integer $idSite The ID of the site that should be queried
     */
    public function __construct($url, $tokenAuth, $idSite)
    {
        $this->_url = $url;
        $this->_tokenAuth = $tokenAuth;
        $this->_idSite = $idSite;
    }

    /**
     * Returns the number of unique visitors in the given period.
     *
     * @param string $period The resolution of the date argument
     * @param string $date The period over which we want to query
     * @return integer
     */
    public function getUniqueVisitors($period = 'week', $date = 'today')
    {
        $parameters = array(
            'method' => 'VisitsSummary.getUniqueVisitors',
            'period' => $period,
            'date'   => $date
        );

        $data = $this->_getData($parameters);

        return $data->value;
    }

    /**
     * Returns the live counter data.
     *
     * @param integer $lastMinutes The amount of time we should go back, in minutes
     * @return array
     */
    public function getLiveCounters($lastMinutes = 30)
    {
        $parameters = array(
            'method'      => 'Live.getCounters',
            'lastMinutes' => $lastMinutes
        );

        $data = $this->_getData($parameters);

        return array(
            'visits' => $data[0]->visits,
            'actions' => $data[0]->actions
        );
    }

    /**
     * Returns a graph with a summary of the user evolution over a specified period.
     *
     * @param string $period The resolution of the date argument
     * @param string $date The period over which we want to query
     * @return \CommonBundle\Component\Piwik\Api\Image
     */
    public function getVisitsSummary($period = 'day', $date = 'previous7')
    {
        $parameters = array(
            'method'    => 'ImageGraph.get',
            'apiModule' => 'VisitsSummary',
            'apiAction' => 'get',
            'graphType' => 'evolution',

            'period'    => $period,
            'date'      => $date,
        );

        return $this->_getImage($parameters);
    }

    /**
     * Retrieves the data at the given URI.
     *
     * @param array $parameters The request's parameters
     * @return \Object
     */
    private function _getData(array $parameters)
    {
        $client = new Client($this->_url);

        $client->setParameterGet(
            array_merge(
                array(
                    'module'     => 'API',
                    'format'     => 'json',
                    'token_auth' => $this->_tokenAuth,
                    'idSite'     => $this->_idSite
                ),
                $parameters
            )
        );

        return json_decode($client->send()->getBody());
    }

    /**
     * Retrieve an image at the given URI.
     *
     * @param array $parameters The request's parameters
     * @return \CommonBundle\Component\Piwik\Api\Image
     */
    private function _getImage(array $parameters)
    {
        $parameters = array_merge(
            array(
                'module'     => 'API',
                'token_auth' => $this->_tokenAuth,
                'idSite'     => $this->_idSite
            ),
            $parameters
        );

        return new Image($this->_url, $parameters);
    }
}
