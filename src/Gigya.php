<?php

namespace Softonic\Gigya;

use Softonic\Gigya\Accounts;

require_once "GSSDK.php";

/**
 * Class GigyaAPI
 *
 * @package App\Datasources\Gigya
 */
class Gigya
{

    /**
     * Contants with existing resources and methods to use the API.
     */
    const GET_ACCOUNT_INFO = 'accounts.getAccountInfo';

    /**
     * The ACL of the resources and methods currently alloweds.
     * @var array $allowed_resources
     */
    protected $allowed_resources = [];

    const NAMESPACE_ACCOUNTS = 'accounts';

    /**
     * @var GSRequest $gigya_request               The request object.
     * @var string    $gigya_user_id               The Gigya UID to send.
     * @var string    $gigya_api_key               The gigya_api_key.
     * @var string    $gigya_secret_key            The gigya_secret_key.
     * @var string    $gigya_url_api_domain        The gigya_api_domain.
     * @var string    $uid_required                A flag to set if UID is required or not.
     */
    protected $gigya_request;
    protected $gigya_user_id = null;
    protected $gigya_api_key;
    protected $gigya_secret_key;
    protected $gigya_url_api_domain;
    protected $uid_required = true;

    /**
     * GigyaAPI constructor.
     *
     * @param string $gigya_api_key         Gigya api sdk.
     * @param string $gigya_secret_key      Gigya secret key.
     * @param string $gigya_url_api_domain  Gigya url api domain.
     */
    public function __construct($gigya_api_key, $gigya_secret_key, $gigya_url_api_domain)
    {
        $this->gigya_api_key = $gigya_api_key;
        $this->gigya_secret_key = $gigya_secret_key;
        $this->gigya_url_api_domain = $gigya_url_api_domain;
    }

    /**
     * @return Accounts
     */
    public function accounts()
    {
        return $this->endpointFactory(Accounts::class);
    }

    /**
     * @param string $className
     *
     * @return Gigya
     */
    protected function endpointFactory($className = self::class)
    {
        return new $className(
            $this->gigya_api_key,
            $this->gigya_secret_key,
            $this->gigya_url_api_domain
        );
    }


    /**
     * Create request.
     *
     * @param $api_resource
     *
     * @return Gigya
     */
    protected function createRequest($api_resource)
    {
        $this->gigya_request = new GSRequest($this->gigya_api_key, $this->gigya_secret_key, $api_resource);
        $this->gigya_request->setAPIDomain($this->gigya_url_api_domain);
        $this->checkIfResourceIsAllowed($api_resource);
        return $this;
    }

    /**
     * Method to send the request using the GigyaSDK
     *
     * @return string
     */
    protected function send()
    {
        $this->checkIfUIDIsRequiredAndAddItToRequestIfNeeded();

        $gigya_response = $this->gigya_request->send();

        if (0 == $gigya_response->getErrorCode()) {
            return json_decode($gigya_response->getData()->toJsonString());
        } else {
            throw new \Exception($gigya_response->getErrorMessage(), $gigya_response->getErrorCode());
        }
    }

    /**
     * If the flag uid_required is setted to TRUE, this method will check it and add to the request.
     *
     * @throws \InvalidArgumentException
     */
    private function checkIfUIDIsRequiredAndAddItToRequestIfNeeded()
    {
        if ($this->uid_required) {
            if (!is_null($this->gigya_user_id)) {
                $this->gigya_request->setParam('UID', $this->gigya_user_id);
            } else {
                throw new \InvalidArgumentException('You should set the UID before call the API.');
            }
        }
    }

    /**
     * Method to validate if called resource and method are allowed.
     *
     * @param string $api_resource Api resource.
     */
    private function checkIfResourceIsAllowed($api_resource)
    {
        if (!in_array($api_resource, $this->allowed_resources)) {
            throw new \InvalidArgumentException('Invalid resource called: ' . $api_resource);
        }
    }

    /**
     * Gigya user identifier setter.
     *
     * @param mixed $gigya_user_id Gigya user identifier.
     *
     * @return Gigya
     */
    protected function setGigyaUserId($gigya_user_id)
    {
        $this->gigya_user_id = $gigya_user_id;
        return $this;
    }

    /**
     *
     * Unique identifier required setter.
     *
     * @param boolean $uid_required Unique identifier required.
     *
     * @return Gigya
     */
    protected function setUidRequired($uid_required)
    {
        $this->uid_required = $uid_required;
        return $this;
    }
}
