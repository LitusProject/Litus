<?php

namespace ApiBundle\Entity\Token;

use Doctrine\ORM\Mapping as ORM;

/**
 * This entity represents an access token used in OAuth 2.0.
 *
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\Token\Access")
 * @ORM\Table(name="api_tokens_access")
 */
class Access extends \ApiBundle\Entity\Token
{
}
