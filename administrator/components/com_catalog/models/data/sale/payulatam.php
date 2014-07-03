<?php

/**
 * Base class for payulatam_functions
 */
class payulatam_functions
{
    /**
     * 
     * @return array convert itself into an associative array
     */
    public function toArray()
    {
        return json_decode(json_encode($this), true);
    }

}

class payulatam extends payulatam_functions
{

    
    public static $public_key='PK6490kR8aGK0VLJ7lE8010m3d';

    /**
     *
     * @var api Login 
     */
    public static $apiLogin='49fe3741284dad8';

    /**
     *
     * @var api key 
     */
    public $ApiKey='5l5rk9sg7s3lf6qto6eej63vn';//6u39nqhq8ftd0hlvnjfs66eh8c

    
    public $merchantId='509192';//500238
    /**
     * true if the payment is in test mode.
     * @var string 
     */
    public $test=false;   

    public $accountId='510329';//500537

    public $amount=0;

    public $referenceCode='';

    public $tax=0;

    public $taxReturnBase=0;

    public $signature="";

    public $currency="USD";

    public $buyerEmail="";

    public $lng="ES";
    
    public $responseUrl="";

    public $confirmationUrl="";

    

    public $payerFullName="";

    

    public $payerDocument="";

    

    public $mobilePhone="";

    

    public $billingAddress="";

    

    public $shippingAddress="";

    

    public $telephone="";

    

    public $officeTelephone="";

    

    public $discount=0;

    

    public $algorithmSignature="md5";

    

    public $extra3="";

    

    public $billingCity="";

    

    public $shippingCity="";

    

    public $zipCode="";

    

    public $billingCountry="";

    

    public $shippingCountry="";

    

    public $buyerFullName="";

    

    public $payerEmail="";

    

    public $payerPhone="";

    

    public $payerOfficePhone="";

    

    public $payerMobilePhone="";

    

    public $extra1 = "";

    

    public $extra2= "";

    

    public $description="";

    

    public function __construct() {

       

        

    }

    

    /**
     * Generates de response for payment request json.
     * 
     * @return string json string
     */
    public function generateJson()
    {

        return json_encode($this);

    }

    public function generateForm()
    {
        header('Content-Type: text/html; charset=utf-8');
        if($this->test == true)
            $url='https://stg.gateway.payulatam.com/ppp-web-gateway/';
        else
            $url='https://gateway.payulatam.com/ppp-web-gateway/';

        

        $form ="<form name=\"payulatam\" action=\"$url\" method=\"POST\" accept-charset=\"UTF-8\">";

        $vars=get_object_vars($this);
        foreach($vars as $key=>$var)
        {
            $clean = AuxTools::Purify_HTML($var);
            $form.="<input value=\"$clean\"  name=\"$key\" type=\"hidden\" />
                    ";
        }

        $form.="<p>Processing.....<img style=\"width:20px; height:20px;\" src=\"/loading.gif\"/> </p>";
        $form.="</form>

            <script type=\"text/javascript\">

            window.onload = function(){

                document.payulatam.submit();

            };

            </script>";

        return $form;

    }

    

    /**

     * Generates the hash string for the validation

     * 

     * @param string $referenceCode

     * @param string $amount

     * @param string $currency

     * @param string $algorithm default md5, md5 or sha depending on the algorithm.

     * @return string generated hash

     */

    public function generateSignature($referenceCode,

            $amount,$currency, $algorithm='md5')

    {

        $toHash=$this->ApiKey.'~'.$this->merchantId.'~'.$referenceCode.'~'.$amount.'~'.$currency;

        $algorithm=  strtolower($algorithm);

        switch ($algorithm)

        {

            default:

            case 'md5':

                return strtoupper(md5($toHash));

            break;

        

            case 'sha':

                return strtoupper(sha1($toHash));

            break;

        }

    }

    

}

