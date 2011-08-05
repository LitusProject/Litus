<?php

namespace Litus\Util;

use \Litus\Util\TmpFile;

class TmpFileClosedException extends \Exception {

    public function __construct(TmpFile $tmpFile)
    {
        parent::__construct($tmpFile->getFilename() . ' has already been closed.');
    }
}
