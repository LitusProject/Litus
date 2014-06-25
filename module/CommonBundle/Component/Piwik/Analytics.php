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
    private $_url = '';

    /**∏
     * @var string The authentication token that should be used
     */
    private $_tokenAuth = '';

    /**
     * @var integer The ID of the site that should be queried
     */
    private $_idSite = 0;

    /**
     * @param string  $url       The API's URL
     * @param string  $tokenAuth The authentication token that should be used
     * @param integer $idSite    The ID of the site that should be queried
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
     * @param  string        $date   The period over which we want to query
     * @param  string        $period The resolution of the date argument
     * @return array|integer
     */
    public function getUniqueVisitors($date = 'today', $period = 'day')
    {
        $parameters = array(
            'method' => 'VisitsSummary.getUniqueVisitors',
            'date'   => $date,
            'period' => $period
        );

        if (null === ($data = $this->_getData($parameters)))
            return null;

        if (count($data) == 1)
            return $data['value'];

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
            'lastMinutes' => $lastMinutes
        );

        if (null === ($data = $this->_getData($parameters))) {
            return array(
                'visits'  => 'N/A',
                'actions' => 'N/A'
            );
        }

        return array(
            'visits'  => $data[0]->visits,
            'actions' => $data[0]->actions
        );
    }

    /**
     * Retrieves the data at the given URI.
     *
     * @param  array $parameters The request's parameters
     * @return array
     */
    private function _getData(array $parameters)
    {
        try {
            $client = new Client(
                $this->_url,
                array(
                    'timeout' => 5
                )
            );

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

            return (array) json_decode($client->send()->getBody());
        } catch (\Exception $e) {
            return null;
        }
    }
}
