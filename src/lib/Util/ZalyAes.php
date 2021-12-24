<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 17/07/2018
 * Time: 4:34 PM
 */

class ZalyAes
{
    /**
     * AES加密方式，Electronic Codebook Book
     */
    CONST METHOD = "AES-128-ECB";
    /**
     * 如果不设置，OPENSSL_ZERO_PADDING， 默认将会按照PKCS#7填充
     * OPENSSL_RAW_DATA 按照 raw data解析 ，不然默认是base64
     */
//    //////CONST OPTION = OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING;
    CONST OPTION = OPENSSL_RAW_DATA;

    CONST KEY_PATH = "/akaxin/phpconfig";

    /**
     * aes128ecb pkcs5加密数据
     * mcrypt_encrypt 在php7.2以后弃用
     *
     * @author 尹少爷 2018.03.21
     *
     * @param string $key  加密key
     * @param string $data 加密数据
     *
     * @return string base64
     */
    public  function encrypt($data, $key)
    {
        return  openssl_encrypt($data, self::METHOD,  $key, self::OPTION);
    }
    /**
     * aes128ecb pkcs5解密数据
     * mcrypt_decrypt 在php7.2以后弃用
     *
     * @author 尹少爷 2018.03.21
     *
     * @param string $key  解密key
     * @param string $data 解密数据
     *
     * @return string
     */
    public function decrypt($data, $key)
    {
        $data = openssl_decrypt($data, self::METHOD, $key, self::OPTION);
        return $data;
    }
}
