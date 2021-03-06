<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Document\Generator;

use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;

class Csv
{
    /**
     * @var array The array containing the headers
     */
    private $headers;

    /**
     * @var array The array containing the results
     */
    private $results;

    /**
     * @param string[] $headers The array containing the headers
     * @param array    $results The array containing the form results
     */
    public function __construct(array $headers, array $results)
    {
        $this->headers = $headers;
        $this->results = $results;
    }

    /**
     * Generate a file to download.
     *
     * @param CsvFile $file The file to write to
     */
    public function generateDocument(CsvFile $file)
    {
        $file->appendContent($this->headers);

        foreach ($this->results as $result) {
            $file->appendContent($result);
        }
    }
}
