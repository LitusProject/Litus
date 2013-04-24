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

namespace CommonBundle\Component\Piwik\Api;

use Zend\Http\Client;

/**
 * This class represents an image retrieved from the Piwik Analytics API.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Image
{
    /**∏
     * @var string The image's URL
     */
    private $_url = '';

    /**
     * @var array The request's parameters
     */
    private $_parameters = array();

    /**
     * @param string $url The image's URL
     * @param array $parameters The request's parameters
     */
    public function __construct($url, array $parameters)
    {
        $this->_url = $url;
        $this->_parameters = $parameters;
    }

    /**
     * Returns an HTML data URI for this image
     *
     * @param integer $width The image's width
     * @param integer $height The image's height
     * @return string
     */
    public function getDataUri($width, $height)
    {
        $this->_parameters = array_merge(
            array(
                'width'     => $width,
                'height'    => $height
            ),
            $this->_parameters
        );

        return 'data:image/png' . ';base64,' . base64_encode($this->_getImage());
    }

    /**
     * Retrieves the image data from the URL.
     *
     * @return string
     */
    private function _getImage()
    {
        return file_get_contents(
            $this->_url . (substr($this->_url, -1) == '/' ? '?' : '/?') . http_build_query($this->_parameters)
        );
    }
}
