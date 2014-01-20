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
 *
 * @license http://litus.cc/LICENSE
 */

namespace ApiBundle\Controller;

use Markdown_Parser,
    Zend\View\Model\ViewModel;

/**
 * NewsController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class NewsController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function getLastAction()
    {
        $items = $this->getEntityManager()
            ->getRepository('NewsBundle\Entity\Node\News')
            ->findAllSite();

        $result = array();
        foreach ($items as $item) {
            $parser = new Markdown_Parser();
            $summary = preg_replace('/\s+/', ' ', strip_tags($parser->transform($item->getContent($this->getLanguage()))));
            $summary = substr($summary, 0, isset($_POST['summary_length']) && is_numeric($_POST['summary_length']) ? $_POST['summary_length'] : 200);

            $result[] = array(
                'id' => $item->getId(),
                'creationTime' => $item->getCreationTime()->format('c'),
                'endDate' => $item->getEndDate() ? $item->getEndDate()->format('c') : null,
                'message' => trim(strip_tags(str_replace(array('<br />', '<br>'), "\r\n", $item->getContent($this->getLanguage())))),
                'summary' => $summary,
                'title' => $item->getTitle($this->getLanguage())
            );
        }

        return new ViewModel(
            array(
                'result' => (object) $result
            )
        );
    }
}
