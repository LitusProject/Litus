<?php

/**
 * Copyright (c) 2013 Lars Vierbergen
 * MIT licensed
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace CommonBundle\Component\Util;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Doctrine\ORM\Query;

/**
 * Improved EntityRepository that handles conversion from methods returning a {@link Query}
 * to methods that return an array of results.
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
abstract class EntityRepository extends DoctrineEntityRepository
{
    /**
     * Adds support for methods that return a result instead of a {@link Query}.
     *
     * All methods that return a query _must_ have 'Query' at the end of their method name.
     * That way, calling the corresponding $method without 'Query' at the end will return all
     * results from the query returned by $method.'Query'.
     * If no the method $method.'Query' does not exist, control is passed on to
     * {@link Doctrine\ORM\EntityRepository::__call}, to handle the findBy* and findAllBy* methods.
     *
     * Note: {@link self::findAll()} and {@link self::findBy()} do not make use of this code,
     * because they are implemented in {@link Doctrine\ORM\EntityRepository}, so {@link self::__call()}
     * will never be called for these functions. {@link self::findAll()} is special-cased below.
     *
     * @param string $method
     * @param array $arguments
     * @return array|object
     */
    public function __call($method, $arguments)
    {
        if(method_exists($this, $method.'Query')) {
            return $this->_fetchResults($method.'Query', $arguments);
        }
        return parent::__call($method, $arguments);
    }

    /**
     * Fetches the results from $this->$method, returning a {@link Query} in an array.
     *
     * @param string $method
     * @param array $arguments
     * @return array
     * @throws \LogicException When the method does not return a {@link Query}
     */
    private function _fetchResults($method, $arguments)
    {
        switch(count($arguments)) {
            // Some fast paths to call methods with zero, one or two arguments.
            case 0:
                $query = $this->$method();
                break;
            case 1:
                $query = $this->$method($arguments[0]);
                break;
            case 2:
                $query = $this->$method($arguments[0], $arguments[1]);
                break;
            default:
                $query = call_user_func_array(array($this, $method), $arguments);
        }
        if(!$query instanceof Query) { // WHY U NO FOLLOW CONVENTION?
            throw new \LogicException(get_class($this).'::'.$method.' must return an instance of Doctrine\ORM\Query.');
        }
        /* @var $query Query */
        return $query->getResult();
    }

    public function findAll()
    {
        if(method_exists($this, 'findAllQuery')) {
            return $this->_fetchResults('findAllQuery', array());
        }
        return parent::findAll();
    }
}