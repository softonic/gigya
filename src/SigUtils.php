<?php

namespace Softonic\Gigya;

class SigUtils
{
    /**
     * Added for simplify the tests without refactor the full package.
     *
     * @var int
     */
    public static $signatureTTL = 180;

    public static function validateUserSignature($UID, $timestamp, $secret, $signature)
    {
        $now = time();
        if (abs($now - $timestamp) > self::$signatureTTL) {
            throw new GSException('Login Signature Expired');
        }
        $baseString = $timestamp . "_" . $UID;
        $expectedSig = self::calcSignature($baseString, $secret);
        return $expectedSig == $signature;
    }

    public static function validateFriendSignature($UID, $timestamp, $friendUID, $secret, $signature)
    {
        $now = time();
        if (abs($now - $timestamp) > self::$signatureTTL) {
            return false;
        }
        $baseString = $timestamp . "_" . $friendUID . "_" . $UID;
        $expectedSig = self::calcSignature($baseString, $secret);
        return $expectedSig == $signature;
    }

    public static function currentTimeMillis()
    {
        // get utc time in ms
        list($msecs, $uts) = explode(' ', microtime());
        return floor(($uts + $msecs) * 1000);
    }

    public static function getDynamicSessionSignature($glt_cookie, $timeoutInSeconds, $secret)
    {
        // cookie format:
        // <expiration time in unix time format>_BASE64(HMACSHA1(secret key, <login token>_<expiration time in unix time format>))
        $expirationTimeUnixMS = (SigUtils::currentTimeMillis() / 1000) + $timeoutInSeconds;
        $expirationTimeUnix = (string)floor($expirationTimeUnixMS);
        $unsignedExpString = $glt_cookie . "_" . $expirationTimeUnix;
        $signedExpString = SigUtils::calcSignature($unsignedExpString, $secret); // sign the base string using the secret key
        $ret = $expirationTimeUnix . '_' . $signedExpString;   // define the cookie value
        return $ret;
    }

    public static function getDynamicSessionSignatureUserSigned($glt_cookie, $timeoutInSeconds, $userKey, $secret)
    {
        // cookie format:
        // <expiration time in unix time format>_<User Key>_BASE64(HMACSHA1(secret key, <login token>_<expiration time in unix time format>_<User Key>))
        $expirationTimeUnixMS = (SigUtils::currentTimeMillis() / 1000) + $timeoutInSeconds;
        $expirationTimeUnix = (string)floor($expirationTimeUnixMS);
        $unsignedExpString = $glt_cookie . "_" . $expirationTimeUnix . "_" . $userKey;
        $signedExpString = SigUtils::calcSignature($unsignedExpString, $secret); // sign the base string using the secret key
        $ret = $expirationTimeUnix . "_" . $userKey . "_" . $signedExpString;   // define the cookie value
        return $ret;
    }

    public static function calcSignature($baseString, $key)
    {
        $baseString = utf8_encode($baseString);
        $rawHmac = hash_hmac("sha1", utf8_encode($baseString), base64_decode($key), true);
        $signature = base64_encode($rawHmac);
        return $signature;
    }
}
