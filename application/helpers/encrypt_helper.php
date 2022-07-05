<?php 
    defined('BASEPATH') OR exit('No direct script access allowed');

    // Function that convert an hexadecimal string to ascii
    function hex2asc($temp) 
    {
        $data = "";
        $len = strlen($temp);
        for ($i = 0; $i < $len; $i += 2) {
            $data .= chr(hexdec(substr($temp, $i, 2)));
        }

        return $data;
    }
    //validacion usuario

    function asc2hex($temp) 
    {
        $data = "";
        $len = strlen($temp);
        for ($i = 0; $i < $len; $i++) {
            $data .= sprintf("%02x", ord(substr($temp, $i, 1)));
        }

        return $data;
    }

    // String encryption function
    function encrypt($string) 
    {
        $key = "Ex6wCoVjh80Iu7ZAraanEEUyJmPHjCIt";
        $result = '';

        for ($i = 1; $i <= strlen($string); $i++) {
            $char = substr($string, $i - 1, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result .= $char;
        }

        return asc2hex($result);
    }

    // String decryption function
    function decrypt($string) 
    {
        $key = "Ex6wCoVjh80Iu7ZAraanEEUyJmPHjCIt";
        $result = '';
        $string = hex2asc($string);

        for ($i = 1; $i <= strlen($string); $i++) {
            $char = substr($string, $i - 1, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result .= $char;
        }
        
        return $result;
    }