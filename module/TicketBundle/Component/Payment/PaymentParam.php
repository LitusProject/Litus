<?php

namespace TicketBundle\Component\Payment;

class PaymentParam #Class to create parameters to send in a payment, hash these according to the rules in documentation and get a url
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
        return $this->name . '=' . $this->value;
    }

    public function isEmpty()
    {
        return $this->value == '';
    }

    public static function nonEmptyPaymentParam($value)
    {
        return $value->isEmpty();
    }

    public static function implodeHash($arr, $shapass) #Just stitching all params behind each other
    {
        $hashstring = '';
        foreach ($arr as $value) {
            $hashstring .= $value->name . '=' . $value->value . $shapass;
        }
        unset($value);
        return $hashstring;
    }

    public static function getUrl($arr, $shapass, $urlPrefix) #Just stitching all params differently behind each other, appending the hash
    {
        $hashstring = PaymentParam::implodeHash($arr, $shapass);
        $hash = hash('sha512', $hashstring);

        error_log(gettype($arr));
        $url = $urlPrefix;
        foreach ($arr as $value) {
            $url .= $value->name . '=' .$value->value . '&';
        }
        $url .= 'SHASIGN=' . $hash;
        return $url;
    }
}
