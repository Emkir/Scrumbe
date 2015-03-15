<?php

namespace Scrumbe\Exception;

/**
 * Class BaseException
 *
 * @package Scrumbe\Exceptions
 */
class BaseException extends \Exception
{
    protected $availableHttpCodes = array(
        '100','101','102','118',
        '200','201','202','203','204','205','206','207','210','226',
        '300','301','302','303','304','305','306','307','308','310',
        '400','401','402','403','404','405','406','407','408','409','410','411','412','413','414','415','416','417','418','422','423','424','425','426','428','429','431','449','450','456','499',
        '500','501','502','503','504','505','506','507','508','509','510','520'
    );

    /**
     * @param string $message
     * @param int $httpCode
     */
    public function __construct($message, $httpCode = 400)
    {
        $this->message = $message;
        $this->setCode($httpCode);
    }

    /**
     * @name setCode
     * @param int $code
     * @return $this
     */
    public function setCode($code)
    {
        if(in_array($code, $this->availableHttpCodes))
        {
            $this->code = $code;
        }
        else
        {
            $this->code = 400;
        }

        return $this;
    }

    /**
     * @name getResponse
     * @return JSON
     */
    public function getResponse()
    {
        return array("code" => $this->code, "message" => $this->message);
    }

}