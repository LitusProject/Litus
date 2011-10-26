<?php

namespace Litus\Entity\Cudi\Sales;

/**
 * @Entity(repositoryClass="Litus\Repository\Cudi\Sales\CashRegister")
 * @Table(name="cudi.sales_cash_register")
 */
class CashRegister
{
	
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @Column(name="number_500", type="integer", nullable=true)
     */
    private $number500;

    /**
     * @Column(name="number_200", type="integer", nullable=true)
     */
    private $number200;

    /**
     * @Column(name="number_100", type="integer", nullable=true)
     */
    private $number100;

    /**
     * @Column(name="number_50", type="integer", nullable=true)
     */
    private $number50;

    /**
     * @Column(name="number_20", type="integer", nullable=true)
     */
    private $number20;

    /**
     * @Column(name="number_10", type="integer", nullable=true)
     */
    private $number10;

    /**
     * @Column(name="number_5", type="integer", nullable=true)
     */
    private $number5;

    /**
     * @Column(name="number_2", type="integer", nullable=true)
     */
    private $number2;

    /**
     * @Column(name="number_1", type="integer", nullable=true)
     */
    private $number1;

    /**
     * @Column(name="number_0p5", type="integer", nullable=true)
     */
    private $number0p5;

    /**
     * @Column(name="number_0p2", type="integer", nullable=true)
     */
    private $number0p2;

    /**
     * @Column(name="number_0p1", type="integer", nullable=true)
     */
    private $number0p1;

    /**
     * @Column(name="number_0p05", type="integer", nullable=true)
     */
    private $number0p05;

    /**
     * @Column(name="number_0p02", type="integer", nullable=true)
     */
    private $number0p02;

    /**
     * @Column(name="number_0p01", type="integer", nullable=true)
     */
    private $number0p01;

    /**
     * @Column(name="amount_bank1", type="integer", nullable=true)
     */
    private $amountBank1;

    /**
     * @Column(name="amount_bank2", type="integer", nullable=true)
     */
    private $amountBank2;
	
	public function __construct( $bill_and_coin_number_array )
    {
        $a = $bill_and_coin_number_array;
        $this->number500 = $a['500p'];
        $this->number200 = $a['200p'];
        $this->number100 = $a['100p'];
        $this->number50 = $a['50p'];
        $this->number20 = $a['20p'];
        $this->number10 = $a['10p'];
        $this->number5 = $a['5p'];
        $this->number2 = $a['2p'];
        $this->number1 = $a['1p'];
        $this->number0p5 = $a['0p5'];
        $this->number0p2 = $a['0p2'];
        $this->number0p1 = $a['0p1'];
        $this->number0p05 = $a['0p05'];
        $this->number0p02 = $a['0p02'];
        $this->number0p01 = $a['0p01'];
        $this->amountBank1 = $a['Bank_Device_1'] * 100;
        $this->amountBank2 = $a['Bank_Device_2'] * 100;
    }

    public function getAmountsArray()
    {
        $a = array( '500p' => $this->number500,
                    '200p' => $this->number200,
                    '100p' => $this->number100,
                    '50p' => $this->number50,
                    '20p' => $this->number20,
                    '10p' => $this->number10,
                    '5p' => $this->number5,
                    '2p' => $this->number2,
                    '1p' => $this->number1,
                    '0p5' => $this->number0p5,
                    '0p2' => $this->number0p2,
                    '0p1' => $this->number0p1,
                    '0p05' => $this->number0p05,
                    '0p02' => $this->number0p02,
                    '0p01' => $this->number0p01,
                    'Bank_Device_1' => $this->amountBank1 / 100,
                    'Bank_Device_2' => $this->amountBank2 / 100
                    );
		return $a;
    }

    public function getTotalAmount()
    {
        $a = 0;
        $a += 50000 * $this->number500;
        $a += 20000 * $this->number200;
        $a += 10000 * $this->number100;
        $a += 5000 * $this->number50;
        $a += 2000 * $this->number20;
        $a += 1000 * $this->number10;
        $a += 500 * $this->number5;
        $a += 200 * $this->number2;
        $a += 100 * $this->number1;
        $a += 50 * $this->number0p5;
        $a += 20 * $this->number0p2;
        $a += 10 * $this->number0p1;
        $a += 5 * $this->number0p05;
        $a += 2 * $this->number0p02;
        $a += 1 * $this->number0p01;
        return $a;
    }
	
	/**
	 * Get the id of the cashregister
	 *
	 * @return integer
	 */
    public function getId()
	{
        return $this->id;
    }

    /**
     * Set number500
     *
     * @param integer $number500
     */
    public function setNumber500($number500)
    {
        $this->number500 = $number500;
    }

    /**
     * Get number500
     *
     * @return integer 
     */
    public function getNumber500()
    {
        return $this->number500;
    }

    /**
     * Set number200
     *
     * @param integer $number200
     */
    public function setNumber200($number200)
    {
        $this->number200 = $number200;
    }

    /**
     * Get number200
     *
     * @return integer 
     */
    public function getNumber200()
    {
        return $this->number200;
    }

    /**
     * Set number100
     *
     * @param integer $number100
     */
    public function setNumber100($number100)
    {
        $this->number100 = $number100;
    }

    /**
     * Get number100
     *
     * @return integer 
     */
    public function getNumber100()
    {
        return $this->number100;
    }

    /**
     * Set number50
     *
     * @param integer $number50
     */
    public function setNumber50($number50)
    {
        $this->number50 = $number50;
    }

    /**
     * Get number50
     *
     * @return integer 
     */
    public function getNumber50()
    {
        return $this->number50;
    }

    /**
     * Set number20
     *
     * @param integer $number20
     */
    public function setNumber20($number20)
    {
        $this->number20 = $number20;
    }

    /**
     * Get number20
     *
     * @return integer 
     */
    public function getNumber20()
    {
        return $this->number20;
    }

    /**
     * Set number10
     *
     * @param integer $number10
     */
    public function setNumber10($number10)
    {
        $this->number10 = $number10;
    }

    /**
     * Get number10
     *
     * @return integer 
     */
    public function getNumber10()
    {
        return $this->number10;
    }

    /**
     * Set number5
     *
     * @param integer $number5
     */
    public function setNumber5($number5)
    {
        $this->number5 = $number5;
    }

    /**
     * Get number5
     *
     * @return integer 
     */
    public function getNumber5()
    {
        return $this->number5;
    }

    /**
     * Set number2
     *
     * @param integer $number2
     */
    public function setNumber2($number2)
    {
        $this->number2 = $number2;
    }

    /**
     * Get number2
     *
     * @return integer 
     */
    public function getNumber2()
    {
        return $this->number2;
    }

    /**
     * Set number1
     *
     * @param integer $number1
     */
    public function setNumber1($number1)
    {
        $this->number1 = $number1;
    }

    /**
     * Get number1
     *
     * @return integer 
     */
    public function getNumber1()
    {
        return $this->number1;
    }

    /**
     * Set number0p5
     *
     * @param integer $number0p5
     */
    public function setNumber0p5($number0p5)
    {
        $this->number0p5 = $number0p5;
    }

    /**
     * Get number0p5
     *
     * @return integer 
     */
    public function getNumber0p5()
    {
        return $this->number0p5;
    }

    /**
     * Set number0p2
     *
     * @param integer $number0p2
     */
    public function setNumber0p2($number0p2)
    {
        $this->number0p2 = $number0p2;
    }

    /**
     * Get number0p2
     *
     * @return integer 
     */
    public function getNumber0p2()
    {
        return $this->number0p2;
    }

    /**
     * Set number0p1
     *
     * @param integer $number0p1
     */
    public function setNumber0p1($number0p1)
    {
        $this->number0p1 = $number0p1;
    }

    /**
     * Get number0p1
     *
     * @return integer 
     */
    public function getNumber0p1()
    {
        return $this->number0p1;
    }

    /**
     * Set number0p05
     *
     * @param integer $number0p05
     */
    public function setNumber0p05($number0p05)
    {
        $this->number0p05 = $number0p05;
    }

    /**
     * Get number0p05
     *
     * @return integer 
     */
    public function getNumber0p05()
    {
        return $this->number0p05;
    }

    /**
     * Set number0p02
     *
     * @param integer $number0p02
     */
    public function setNumber0p02($number0p02)
    {
        $this->number0p02 = $number0p02;
    }

    /**
     * Get number0p02
     *
     * @return integer 
     */
    public function getNumber0p02()
    {
        return $this->number0p02;
    }

    /**
     * Set number0p01
     *
     * @param integer $number0p01
     */
    public function setNumber0p01($number0p01)
    {
        $this->number0p01 = $number0p01;
    }

    /**
     * Get number0p01
     *
     * @return integer 
     */
    public function getNumber0p01()
    {
        return $this->number0p01;
    }

    /**
     * Set amountBank1
     *
     * @param integer $amountBank1
     */
    public function setAmountBank1($amountBank1)
    {
        $this->amountBank1 = $amountBank1;
    }

    /**
     * Get amountBank1
     *
     * @return integer 
     */
    public function getAmountBank1()
    {
        return $this->amountBank1;
    }

    /**
     * Set amountBank2
     *
     * @param integer $amountBank2
     */
    public function setAmountBank2($amountBank2)
    {
        $this->amountBank2 = $amountBank2;
    }

    /**
     * Get amountBank2
     *
     * @return integer 
     */
    public function getAmountBank2()
    {
        return $this->amountBank2;
    }
}
