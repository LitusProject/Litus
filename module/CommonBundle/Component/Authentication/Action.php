<?php

namespace CommonBundle\Component\Authentication;

/**
 * Interface specifying an action that should be taken after authentication.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
interface Action
{
    /**
     * The authorization has failed.
     *
     * @param  Result $result
     * @return void
     */
    public function failedAction(Result $result);

    /**
     * The authorization was successful.
     *
     * @param  Result $result
     * @return void
     */
    public function succeededAction(Result $result);
}
