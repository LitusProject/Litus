<?php

namespace Litus\Authentication;

interface Action
{
    /**
     * The authorization has failed.
     * 
     * @param $result
     * @return void
     */
	public function failedAction($result);
	
    /**
     * The authorization was successful.
     *
     * @param $result
     * @return void
     */
	public function succeededAction($result);
}