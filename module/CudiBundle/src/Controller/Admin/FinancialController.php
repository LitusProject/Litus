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
 
namespace CudiBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
	CommonBundle\Entity\General\Bank\BankDevice\Amount as BankDeviceAmount,
	CommonBundle\Entity\General\Bank\CashRegister,
	CommonBundle\Entity\General\Bank\MoneyUnit\Amount as MoneyUnitAmount,
	CudiBundle\Entity\Sales\Session,
	CudiBundle\Form\Admin\Sale\CashRegisterEdit as CashRegisterEditForm,
	Doctrine\ORM\EntityManager,
	Doctrine\ORM\QueryBuilder;

/**
 * SaleController
 *
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 */
class FinancialController extends \CommonBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
    
    	$qb = $this->getEntityManager()->createQueryBuilder();
    	$qb->select( 's.openDate, s.closeDate, m.username, s.id' )
           ->from( 'CudiBundle\Entity\Sales\Session', 's' )
           ->from( 'CommonBundle\Entity\Users\Person', 'm' )
           ->where( 's.manager = m.id' )
           ->orderBy( 's.openDate', 'DESC' );
    		
        $records = $qb->getQuery()->getArrayResult();
        
        foreach( $records as &$record )
        {
        	$opentime = "0".strtotime( $record['openDate'] );
        	$closetime = "0".strtotime( $record['closeDate'] ); // prefix 0 to allow arithmetic
        	
            if( $opentime < $closetime )
                $record['open'] = false;
            else
                $record['open'] = true;
                
            $record['theoreticalrevenue'] = $this->getTheoreticalRevenue( $record['id'] );
            $record['actualrevenue'] = $this->getActualRevenue( $record['id'] );
        }
        
        // hats off to whoever gets all the above into one query
        // for now, the combination of several queries will do
        
        $currentPage = 0; // todo
        $paginator = $this->paginator()->createFromArray(
            $records,
            $currentPage
        );
        
        // todo: display sale session comment on mouse over
        
        return array(
        	'paginator' => $paginator,
        	'paginationControl' => $this->paginator()->createControl(true)
        );
    }
    
    /**
     * getTheoreticalRevenue
     * calculates the theoretical revenue of a given session --
     * that is, the revenue expected on the basis of sold stock items
     * @param $sessionId -- the session id which uniquely identifies
     * the session
     */
    private function getTheoreticalRevenue( $sessionId )
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
    	$qb->select( 'sum(s.price)' )
           ->from( 'CudiBundle\Entity\Sales\SaleItem', 's' )
           ->where( 's.session = ' . $sessionId );
        $revenue = $qb->getQuery()->getSingleScalarResult();
        if( $revenue === NULL )
            return 0;
        else
            return $revenue;
    }
    
    /**
     * getActualRevenue
     * calculates the actual revenue of a given session --
     * that is, the register difference between opening and closure of
     * a session
     * @param $sessionId -- the session id which uniquely identifies
     * the session
     */
    private function getActualRevenue( $sessionId )
    {
        $session = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sales\Session')
                    ->findOneById($sessionId);
        $openamount = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Bank\CashRegister')
            ->findOneById(
                $session->getOpenAmount()
            );
        
        if( $session->isOpen() )
            return 0;
        
        $closeamount = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Bank\CashRegister')
            ->findOneById(
                $session->getCloseAmount()
            );
        
        return $closeamount->getTotalAmount() - $openamount->getTotalAmount();
    }
    
}
