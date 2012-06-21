<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CudiBundle\Controller\Supplier;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear;

/**
 * ArticleController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ArticleController extends \CudiBundle\Component\Controller\SupplierController
{
    public function manageAction()
    {
        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Article')
            ->findAllBySupplierAndAcademicYear($this->getSupplier(), $this->getCurrentAcademicYear());
        
    	return array(
    	    'articles' => $articles
    	);
    }
}