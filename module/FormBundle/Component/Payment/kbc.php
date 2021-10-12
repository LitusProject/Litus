<?php #General docs: https://support-paypage.ecom-psp.com/en/integration-solutions/integrations/hosted-payment-page

class PaymentParam     #Class to create parameters to send in a payment, hash these according to the rules in documentation and get a url
{
    protected $name;
    protected $value;

    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->name . "=" . $this->value;
    }

    public function isEmpty()
    {
        return $this->value == "";
    }

    public static function nonEmptyPaymentParam($value)
    {
        return !$value->isEmpty();
    }

    public static function implodeHash($arr, $shapass) #Just stitching all params behind each other
    {
        $hashstring = "";
        foreach ($arr as &$value) {
            $hashstring .= $value->name . "=" . $value->value . $shapass;
        }
        unset($value);
        return $hashstring;
    }

    public static function getUrl($arr, $shapass, $urlPrefix) #Just stitching all params differently behind each other, appending the hash
    {
        $hashstring = PaymentParam::implodeHash($arr, $shapass);
        $hash = hash('sha512',$hashstring);

        $url = $urlPrefix;
        foreach ($arr as &$value) {
            $url .= $value->name . "=" .$value->value . "&";
        }
        $url .= "SHASIGN=" . $hash;
        return $url;
    }
}

$shapass = "ToZ4]ARc1=/AeqBr8UG"; #Hash for params to the paypage
$shaOut = "D7TZq\Xdm2/zYM9z&jJ"; #Hash for params from the paypage to accepturl
$urlPrefix = "https://secure.paypage.be/ncol/prod/orderstandard_utf8.asp?";   #Change prod to test for testenvironment

$data = [   #These are in alphabetical order as that is required for the hash
    new PaymentParam("ACCEPTURL", "https://www.vtk.be"), #URL where user is redirected to when payment is accepted, the same parameters that were sent to paypage will be returned, and hashed (sha-512) to check for validity. (https://support-paypage.ecom-psp.com/en/integration-solutions/integrations/hosted-payment-page#e_commerce_integration_guides_transaction_feedback)
    new PaymentParam("AMOUNT", "600" ), #Required, in cents
    new PaymentParam("CN", "RobinWroblowski" ),
    new PaymentParam("COM", "700100 2022-001-0001" ),  #Required for beheer: char 0-15 given by beheer, last 4 should increment with each payment
    new PaymentParam("CURRENCY", "EUR" ),  #Required
    new PaymentParam("EMAIL", "robin.wroblowski@telenet.be" ),
    new PaymentParam("LANGUAGE", "en_UK" ),
    new PaymentParam("LOGO", "logo.png" ), #Required
    new PaymentParam("ORDERID", "20220010001" ), #Required, char 0-6 given by beheer, last 4 should increment with each payment
    new PaymentParam("PMLISTTYPE", "2" ), #Required
    new PaymentParam("PSPID", "vtkprod" ), #Required
    new PaymentParam("TP", "ingenicoResponsivePaymentPageTemplate_index.html" ), #Required
];

$data_filtered = array_filter($data, "PaymentParam::nonEmptyPaymentParam"); #No empty params in url/hash

$url = PaymentParam::getUrl($data_filtered, $shapass, $urlPrefix);
echo $url;