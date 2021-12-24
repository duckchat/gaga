<?php
/**
 * Created by PhpStorm.
 * User: sssl
 * Date: 2018/8/28
 * Time: 11:42 AM
 */

// mock bcmath
if(!extension_loaded("bcmath")) {
    function bcadd($left_operand,  $right_operand, $scale = 0 )
    {
        return (string)(intval($left_operand) + intval($right_operand));
    }

    function bccomp($left_operand, $right_operand, $scale = 0)
    {
        $left_operand = intval($left_operand);
        $right_operand = intval($right_operand);

        if($left_operand > $right_operand) {
            return 1;
        } else if($left_operand == $right_operand) {
            return 0;
        } else {
            return -1;
        }
    }

    function bcsub($left_operand, $right_operand, $scale = 0)
    {
        return (string)(intval($left_operand) - intval($right_operand));
    }
}

if (!function_exists("mb_strlen")) {
    function mb_strlen($string) {
        preg_match_all("/./us", $string, $match);
        return count($match[0]);
    }
}

if(!function_exists("mb_substr")) {
    function mb_substr($string, $start, $length) {
        if (mb_strlen($string) > $length) {
            $str = null;
            $len = 0;
            $i = $start;
            while ( $len < $length) {
                if (ord(substr($string, $i, 1)) > 0xc0) {
                    ///utf8, 大于A,
                    $str .=substr($string, $i, 3);
                    $i+= 3;
                }elseif (ord(substr($string, $i, 1)) > 0xa0) {
                    //gbk ASCII oxao 表示汉字的开始
                    $str .= substr($string, $i, 2);
                    $i+= 2;
                }else {
                    $str.=substr($string, $i, 1);
                    $i++;
                }
                $len ++;
            }
            return $str;
        }
        return $string;
    }
}

if(extension_loaded("openssl")) {
    // fix OpenSSL
    //
    // 所有PHP手册中含有下述信息的，都需要wrapper一下再用:
    //  // Note: You need to have a valid openssl.cnf installed for this function to operate correctly. See the notes under the installation section for more information.
    //
    //
    define("MOCK_OPENSSL_CNF", __DIR__ . "/mock-openssl.cnf");
}

if(!function_exists('mime_content_type')) {

    function mime_content_type($path)
    {
        $mimeTypeHexs = [
            "89504e470d0a1a0a" => "image/png",
            "ffd8ffe0010"      => "image/jpg",
            "ffd8ffe0"         => "image/jpeg",
            "474946383961"     => 'image/gif',
            "667479704d534e56" => 'video/mp4',
            '6674797069736f6d' => 'audio/mp4',
            '667479704d344120' => 'audio/x-m4a',
        ];

        $fileContent    = file_get_contents($path);
        $fileContentHex = bin2hex($fileContent);
        $fileContentHex = substr($fileContentHex, 0, 20);

        foreach($mimeTypeHexs as $mimeHex => $mime) {
            $flag = strpos($fileContentHex, $mimeHex);
            if($flag == 0) {
                return $mime;
            }
        }
        return "application/octet-stream";
    }
}

