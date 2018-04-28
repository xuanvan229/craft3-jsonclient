<?php

namespace loca\jsonclient\twigextensions;

use loca\jsonclient\jsonclient;

use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

use Craft;
use ReflectionProperty;

class JsonClientTwigExtension extends Twig_Extension
{

    static $manifestObject = null;
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'JsonClient';
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('fetchJson', [$this, 'fetchJson']),
        ];
    }

    /**
     * Returns versioned file or the entire tag.
     *
     * @param  string  $file
     * @return string
     */
    public function getCookie(){
        return Craft::$app->config->general->cookie;
    }

    public function getAuthorization() {
        return Craft::$app->config->general->authorization;
    }
    public function fetchJson($options = [])
    {
        //return \view::render('settings', []);
        // return 'twitter feed...';

        if (!isset($options['url'])) {
          die('Required url parameter not set!');
        }
        $authorization = self::getAuthorization();
        $cookie = self::getCookie();
        if ($options['check'] == "1"){
            $data = self::getUrl($options['url'], $authorization, $options['check']);
        }
        if ($options['check'] == "0"){
            $data = self::getUrl($options['url'], $authorization, $options['check']);
        }
        if ($options['check'] == "3"){
            $data = self::getUrlWithId($options['url'], $authorization, $options['id'], $options['flag']);
        }if ($options['check'] == "4"){
            $data = self::SearchText($options['url'], $authorization, $options['text']);
        }
        if ($options['check'] == "2"){
            $data = self::getUrlWithParams($options['url'], $authorization, $options['doctorName'],$options['specialty'],$options['hospital'],$options['gender'],$options['postalCode'],$options['city'],$options['state']);
        }
        

        return json_decode($data, true);

    }

        // Function for cURL
        private static function getUrl($url, $authorization, $check) {
            error_reporting(0);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if ($check == "1"){
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: {$authorization}"));
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $store = curl_exec($ch);
            curl_close($ch);

            return $store;
        }
        public function getUrlWithId($url, $authorization, $id, $flag){
            $ch = curl_init();
            error_reporting(0);
            $query = (object) array();
            $specialty_field = "specialty.code";
            $query->$specialty_field = $id;
            $flag_field = "flag";
            $query->$flag_field = $flag;
            $urlquery = $url.json_encode($query);
            curl_setopt($ch, CURLOPT_URL, $urlquery);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: {$authorization}"));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $store = curl_exec($ch);     
            curl_close($ch);
            return $store;
        } 
         public function SearchText($url, $authorization, $text){
            $ch = curl_init();
            error_reporting(0);
            $text_search = "";
            $array = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
            for($x = 0; $x<count($array); $x++){
                $text_search = $text_search.'"'.$array[$x].'"';
            }
            if($text !== ''){
            $search = (object) array(
                    '$search' => $text_search
                 );
            $query = (object) array();
            $query = (object) array (
                '$text' => $search
                );
            } 
            $urlquery = $url.json_encode($query);
            curl_setopt($ch, CURLOPT_URL, $urlquery);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: {$authorization}"));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $store = curl_exec($ch);     
            curl_close($ch);
            return $store;
        } 
        public function getUrlWithParams($url, $authorization, $doctorName, $specialty, $hospital, $gender, $postalCode, $city, $state){
            $ch = curl_init();
            error_reporting(0);
            $text_search = "";

            $array = preg_split('/\s+/', $doctorName, -1, PREG_SPLIT_NO_EMPTY);
            for($x = 0; $x<count($array); $x++){
                $text_search = $text_search.'"'.$array[$x].'"';
            }
            if($doctorName !== ''){
            $search = (object) array(
                    '$search' => $text_search
                 );
            $query = (object) array();
            $query = (object) array (
                '$text' => $search
            );
            } else {
                $query = (object) array();
            }
             if($specialty !== ''){
                $specialty_field = "specialty.code";
                $query->$specialty_field = $specialty;
            }
           if($hospital !== ''){
                $hospital_field = "hospital.name";
                $query->$hospital_field = $hospital;
            }
            if($gender !== ''){
                $gender_field = "gender";
                $query->$gender_field = $gender;
            }
            if($postalCode !== ''){
                $postalCode_field = "address.postalCode";
                $query->$postalCode_field = $postalCode;
               
            }
            if($city !== ''){
                $city_field = "address.city";
                $query->$city_field = $city;
            }
            if($state !== ''){
                 $state_field = "address.state";
                $query->$state_field = $state;
            }
        
            $urlquery = $url.json_encode($query);
            curl_setopt($ch, CURLOPT_URL, $urlquery);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: {$authorization}"));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $store = curl_exec($ch);     
            curl_close($ch);
            return $store;
        }


}
