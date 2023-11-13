<?php

namespace ShopBundle\Component\CanReserve;

/**
 * This class is used as a response type for the CanReserve function in ShopController.
 * It supports a boolean value indicating whether a user can reserve, and a reason why not.
 */
class CanReserveResponse
{
    /**
     * @var bool
     */
    private $canReserve;

    /**
     * @var string|null
     */
    private $reason;

    /**
     * CanReserveResponse constructor.
     *
     * @param bool $canReserve
     * @param string|null $reason
     */
    public function __construct(bool $canReserve, string $reason = null)
    {
        $this->canReserve = $canReserve;
        $this->reason = $reason;
    }

    /**
     * @return bool
     */
    public function canReserve(): bool
    {
        return $this->canReserve;
    }

    /**
     * @return string|null
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }
}
