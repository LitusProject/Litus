<?php
namespace Litus\Authentication;

interface Adapter extends \Zend\Authentication\Adapter
{
    /**
     * Set the provided identity.
     *
     * @param string $identity The identity that was provided
     * @return \Litus\Authentication\Adapter
     */
    public function setIdentity($identity);

    /**
     * Set the provided credential.
     *
     * @param string $credential The credential that was provided
     * @return \Litus\Authentication\Adapter
     */
    public function setCredential($credential);
}
