<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 21/07/2018
 * Time: 4:51 PM
 */

class  ZalyBase64
{

    static function base64url_encode($data, $pad = null)
    {
        $data = str_replace(array('+', '/'), array('-', '_'), base64_encode($data));
        if (!$pad) {
            $data = rtrim($data, '=');
        }
        return $data;
    }

    static function base64url_decode($data)
    {
        return base64_decode(str_replace(array('-', '_'), array('+', '/'), $data));
    }

}