<?php

namespace Softonic\Gigya;

/**
 * Class Accounts
 *
 * @package Softonic\Gigya
 */
class Accounts extends Gigya
{
    const GET_ACCOUNT_INFO = 'accounts.getAccountInfo';
    const HTTP_OK = '200';

    /**
     * The ACL of the resources and methods currently alloweds.
     * @var array $allowed_resources
     */
    protected $allowed_resources = [
        self::GET_ACCOUNT_INFO
    ];
    
    public function getAccountInfo($id){
        $this->createRequest(self::GET_ACCOUNT_INFO);
        $this->setGigyaUserId($id);

        return $this->send();
    }
}