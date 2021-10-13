<?php

namespace BrBundle\Component\ContractParser;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
interface Node
{
    /**
     * @param  NodeVisitor $nodeVisitor
     * @return null
     */
    public function visitNode(NodeVisitor $nodeVisitor);
}
