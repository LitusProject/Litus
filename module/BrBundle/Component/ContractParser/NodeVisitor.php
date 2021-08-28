<?php

namespace BrBundle\Component\ContractParser;

/**
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
interface NodeVisitor
{
    /**
     * @param  Entry $entry
     * @return null
     */
    public function visitEntry(Entry $entry);

    /**
     * @param  Entries $entries
     * @return null
     */
    public function visitEntries(Entries $entries);

    /**
     * @param  Text $text
     * @return null
     */
    public function visitText(Text $text);
}
