<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shortener_library
{
    protected static $chars = "abcdfghjkmnpqrstvwxyz|ABCDFGHJKLMNPQRSTVWXYZ|0123456789";
    protected static $table = "short_urls";
    protected static $checkUrlExists = TRUE;
    protected static $codeLength = 7;

    protected $pdo;
    protected $timestamp;

    public function __construct(){
         $this->CI =& get_instance();
       // $this->pdo = $pdo;
       // $this->timestamp = date("Y-m-d H:i:s");
    }

    public function urlToShortCode($url){
        if(empty($url)){
            throw new Exception("No URL was supplied.");
        }

       /* if($this->validateUrlFormat($url) == false){
            throw new Exception("URL does not have a valid format.");
        }*/

        if(self::$checkUrlExists){
            if (!$this->verifyUrlExists($url)){
                throw new Exception("URL does not appear to exist.");
            }
        }

       $shortCode = $this->createShortCode($url);
    

        return $shortCode;
    }

    /*protected function validateUrlFormat($url){
      return filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
    }*/

    public function solicitarUrl($url){
        
        $shortener = new Shortener_library();
        // Long URL
        $longURL = $url;
        //$longURL = 'https://www.codexworld.com/tutorials/php/';
        // Prefix of the short URL 
        $shortURL_Prefix = 'http://solven.me/'; // with URL rewrite
        //$shortURL_Prefix = 'https://solventa.com.ar/?c='; // without URL rewrite
        try{
            // Get short code of the URL
            $shortCode = $shortener->urlToShortCode($longURL);
            
            // Create short URL
            $shortURL = $shortURL_Prefix.$shortCode;
            
            // Display short URL
            return $shortURL;
        }catch(Exception $e){
            // Display error
            return $e->getMessage();
        }
    }

    protected function verifyUrlExists($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return (!empty($response) && $response != 404);
    }

    protected function urlExistsInDB($url){
        $query = "SELECT short_code FROM ".self::$table." WHERE long_url = :long_url LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $params = array(
            "long_url" => $url
        );
        $stmt->execute($params);

        $result = $stmt->fetch();
        return (empty($result)) ? false : $result["short_code"];
    }

    protected function createShortCode($url){
        $shortCode = $this->generateRandomString(self::$codeLength);
      //  $id = $this->insertUrlInDB($url, $shortCode);
        return $shortCode;
    }
    
    protected function generateRandomString($length = 6){
        $sets = explode('|', self::$chars);
        $all = '';
        $randString = '';
        foreach($sets as $set){
            $randString .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++){
            $randString .= $all[array_rand($all)];
        }
        $randString = str_shuffle($randString);
        return $randString;
    }

  
    
    public function shortCodeToUrl($code, $increment = true){
        if(empty($code)) {
            throw new Exception("No short code was supplied.");
        }

        if($this->validateShortCode($code) == false){
            throw new Exception("Short code does not have a valid format.");
        }

        $urlRow = $this->getUrlFromDB($code);
        if(empty($urlRow)){
            throw new Exception("Short code does not appear to exist.");
        }

        if($increment == true){
            $this->incrementCounter($urlRow["id"]);
        }

        return $urlRow["long_url"];
    }

    protected function validateShortCode($code){
        $rawChars = str_replace('|', '', self::$chars);
        return preg_match("|[".$rawChars."]+|", $code);
    }

    protected function getUrlFromDB($code){
        $query = "SELECT id, long_url FROM ".self::$table." WHERE short_code = :short_code LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $params=array(
            "short_code" => $code
        );
        $stmt->execute($params);

        $result = $stmt->fetch();
        return (empty($result)) ? false : $result;
    }

    protected function incrementCounter($id){
        $query = "UPDATE ".self::$table." SET hits = hits + 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $params = array(
            "id" => $id
        );
        $stmt->execute($params);
    }
}