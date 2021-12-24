<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 17/07/2018
 * Time: 4:34 PM
 */

class ZalyRsa
{

    static $KeyPublicKey = "PublicKeyPem";
    static $KeyPrivateKey = "PrivateKeyPem";

    private $option = OPENSSL_PKCS1_PADDING;

    public function encrypt($data, $key)
    {
        openssl_public_encrypt($data, $crypted, $key, $this->option);
        return $crypted;
    }


    public function decrypt($data, $key)
    {
        openssl_private_decrypt($data, $decrypted, $key, $this->option);
        return $decrypted;
    }

    public function sign($data, $privateKey)
    {
        openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        return $signature;
    }


    /**
     * @param int $len
     * @return array on suucess, false on failed.
     */
    public static function newRsaKeyPair($len = 2048) {
        $conf = array(
            'private_key_bits' => $len
        );

        // make private key
        $privateKey = openssl_pkey_new($conf);
        if (false === $privateKey) {
            $conf["config"] = MOCK_OPENSSL_CNF;
            $privateKey = openssl_pkey_new($conf);
        }

        if (false == $privateKey) {
            return false;
        }

        // export
        $keyPem = "";
        $exportResult = openssl_pkey_export($privateKey, $keyPem);
        if(false === $exportResult) {
            openssl_pkey_export($privateKey, $keyPem, null, array("config"=>MOCK_OPENSSL_CNF));
        }
        if (empty($keyPem)) {
            return false;
        }

        $keyData = openssl_pkey_get_details($privateKey);
        $publicKeyPem = isset($keyData["key"]) ? $keyData["key"] : "";
        if (empty($publicKeyPem)) {
            return false;
        }

        return array(
            ZalyRsa::$KeyPrivateKey=> trim($keyPem),
            ZalyRsa::$KeyPublicKey=>trim($publicKeyPem)
        );
    }
}
