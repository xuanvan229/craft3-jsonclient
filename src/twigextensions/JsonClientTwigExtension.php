<?php
namespace tifl\jsonclient\twigextensions;

use tifl\jsonclient\jsonclient;

use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

use Craft;
use ReflectionProperty;

class JsonClientTwigExtension extends Twig_Extension
{
    static $manifestObject = null;

    public $MAX_RESULT = '24';
    // public $PRACTITIONER_URL = Craft::$app->config->general->tiflPractitionerUrl;
    // public $ORGANIZATION_URL = Craft::$app->config->general->tiflOrganizationUrl;
    // public $SPECIALTY_URL_PRACTITIONER = Craft::$app->config->general->tiflSpecialtyUrlPractitioner;
    // public $SPECIALTY_URL_ORGANIZATION = Craft::$app->config->general->tiflSpecialtyUrlPractitioner;
    // public $HOSPITAL_URL = Craft::$app->config->general->tiflHospitalUrl;
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'JsonClient';
    }

    public function getUrl($type)
    { 
        $url = "";
        switch ($type) {
            case "tiflPractitionerUrl": 
                $url = Craft::$app->config->general->tiflPractitionerUrl;
                break;
            case "tiflOrganizationUrl":
                $url = Craft::$app->config->general->tiflOrganizationUrl;
                break;
            case "tiflSpecialtyUrlPractitioner": 
                $url = Craft::$app->config->general->tiflSpecialtyUrlPractitioner;
                break;
            case "tiflSpecialtyUrlOrganization":
                $url = Craft::$app->config->general->tiflSpecialtyUrlOrganization;
                break;
            case "tiflHospitalUrl":
                $url = Craft::$app->config->general->tiflHospitalUrl;
                break;
        } 
        return $url;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            // Get 1 practitioner
            new \Twig_SimpleFunction('getPractitioner', [$this, 'getPractitioner']),
            // Get 1 Organzation
            new \Twig_SimpleFunction('getOrganization', [$this, 'getOrganization']),
            // Get List PCP
            new \Twig_SimpleFunction('getListPCP', [$this, 'getListPCP']),
            // Get List Specialist
            new \Twig_SimpleFunction('getListSpecialist', [$this, 'getListSpecialist']),
            // Get List Ancillaries
            new \Twig_SimpleFunction('getListAncillaries', [$this, 'getListAncillaries']),
            // Get List Facilities
            new \Twig_SimpleFunction('getListFacilities', [$this, 'getListFacilities']),
            // Search By Text
            new \Twig_SimpleFunction('SearchPractitioner', [$this, 'SearchPractitioner']),

            new \Twig_SimpleFunction('SearchOrganization', [$this, 'SearchOrganization']),

            new \Twig_SimpleFunction('getSpecialtyPractitioner', [$this, 'getSpecialtyPractitioner']),
            new \Twig_SimpleFunction('getSpecialtyOrganization', [$this, 'getSpecialtyOrganization']),
            new \Twig_SimpleFunction('getHospital', [$this, 'getHospital']),
            // Get Url encode
            new \Twig_SimpleFunction('getUrlImage', [$this, 'getUrlImage'])

        ];
    }

    /**
     * Returns versioned file or the entire tag.
     *
     * @param  string  $file
     * @return string
     */
    // Get cookie from config
    public function getCookie() {
        return Craft::$app->config->general->cookie;
    }

    // Get authorization from config
    public function getAuthorization() {
        return Craft::$app->config->general->tiflAuthorization;
    }

    public function getUrlImage($options= []) {
        $url = str_replace("https://", "", $options['url']);
        return urlencode($url);
    }

    public function getUrlPractitioners($options,$plag) {
        $query = (object) array();

        $baseUrl = self::getUrl("tiflPractitionerUrl");

        if(isset($options['id'])){
            $specialty_field = "primarySpec.code";
            $query->$specialty_field = $options['id'];
        }

        $flag_field = "flag";
        $query->$flag_field = $plag;

        $url = $baseUrl.'?max_results='.$this->MAX_RESULT.'&page='.$options['page'].'&where=';
        $urlquery = $url.json_encode($query);

        return $urlquery;
    }

    public function getUrlSearch($options,$baseurl){
            $text_search = "";
            if(isset($options['text']) && $options['text'] !== '') {
                $array = preg_split('/\s+/', $options['text'], -1, PREG_SPLIT_NO_EMPTY);

                for($x = 0; $x<count($array); $x++) {
                    $text_search = $text_search.'"'.$array[$x].'"';
                }

                $search = (object) array(
                    '$search' => $text_search
                );

                $query = (object) array();
                $query = (object) array(
                    '$text' => $search
                );
            }
            else {
                $query = (object) array();
            }

            if(isset($options['id']) && $options['id'] !== '') {
                $specialty_field = "primarySpec.code";
                $query->$specialty_field = $options['id'];
            }

            if(isset($options['hospital']) && $options['hospital'] !== '') {
                $hospital_field = "_props.organization";
                $query->$hospital_field = $options['hospital'];
            }

            if(isset($options['gender']) && $options['gender'] !== '') {
                $gender_field = "gender";
                $query->$gender_field = $options['gender'];
            }

            if(isset($options['postalCode']) && $options['postalCode'] !== '') {
                $postalCode_field = "address.postalCode";
                $query->$postalCode_field = $options['postalCode'];
            }

            if(isset($options['city']) && $options['city'] !== '') {
                $city_field = "address.city";
                $query->$city_field = $options['city'];
            }

            if(isset($options['state']) && $options['state'] !== '') {
                $state_field = "address.state";
                $query->$state_field = $options['state'];
            }

            if(isset($options['id']) && $options['id'] !== '') {
                $specialty_field = "primarySpec.code";
                $query->$specialty_field = $options['id'];
            }

            $url = $baseurl.'?max_results='.$this->MAX_RESULT.'&page='.$options['page'].'&where=';
            $urlquery = $url.json_encode($query);
            return $urlquery;
    }

    public function getUrlOrganzations($options,$plag){
        $query = (object) array();

        $baseUrl = self::getUrl("tiflOrganizationUrl");

        if(isset($options['id'])){
            $specialty_field = "specialty.code";
            $query->$specialty_field = $options['id'];
        }
        $flag_field = "flag";
        $query->$flag_field = $plag;
        $url = $baseUrl.'?max_results='.$this->MAX_RESULT.'&page='.$options['page'].'&where=';
        $urlquery = $url.json_encode($query);

        return $urlquery;
    }

    // Get Curl with the urlquery
    public function getData($urlquery) {
        error_reporting(0);
        $ch = curl_init();
        $authorization = self::getAuthorization();
        curl_setopt($ch, CURLOPT_URL, $urlquery);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: {$authorization}"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $store = curl_exec($ch);
        curl_close($ch);
        return $store;
    }

    // Get 1 practitioner
    public function getPractitioner($options = []) {
        $baseUrl = self::getUrl("tiflPractitionerUrl");
        $url    = $baseUrl.$options['id'];
        $data   = self::getData($url);
        return json_decode($data, true);
    }

    // Get 1 Orgazation
    public function getOrganization($options = []) {
        $baseUrl = self::getUrl("tiflOrganizationUrl");
        $url    = $baseUrl.$options['id'];
        $data   = self::getData($url);
        return json_decode($data, true);
    }

    // Get List PCP
    public function getListPCP($options = []) {
        $plag   = 'pcp';
        $url    = self::getUrlPractitioners($options,$plag);
        $data   = self::getData($url);
        return json_decode($data, true);
    }

    // Get List Specialist
    public function getListSpecialist($options = []) {
        $plag   = 'specialist';
        $url    = self::getUrlPractitioners($options,$plag);
        $data   = self::getData($url);
        return json_decode($data, true);
    }

    public function getListAncillaries($options = []) {
        $plag   = 'ancillary';
        $url    = self::getUrlOrganzations($options,$plag);
        $data   = self::getData($url);
        return json_decode($data, true);
    }

    public function getListFacilities($options = []) {
        $plag   = 'facility';
        $url    = self::getUrlOrganzations($options,$plag);
        $data   = self::getData($url);
        return json_decode($data, true);
    }

    public function searchPractitioner($options = []){
        $baseUrl = self::getUrl("tiflPractitionerUrl");
        $url    = self::getUrlSearch($options,$baseUrl);
        $data   = self::getData($url);
        return json_decode($data, true);
    }

    public function searchOrganization($options = []){
        $baseUrl = self::getUrl("tiflOrganizationUrl");
        $url    = self::getUrlSearch($options,$baseUrl);
        $data   = self::getData($url);
        return json_decode($data, true);
    }

    public function getSpecialtyPractitioner(){
        $baseUrl = self::getUrl("tiflSpecialtyUrlPractitioner");

        $url = $baseUrl;
        $data   = self::getData($url);
        return json_decode($data, true);
    }

    public function getSpecialtyOrganization(){
        $baseUrl = self::getUrl("tiflSpecialtyUrlOrganization");
        $url = $baseUrl;
        $data   = self::getData($url);
        return json_decode($data, true);
    }

    public function getHospital(){
        $baseUrl = self::getUrl("tiflHospitalUrl");
        $url = $baseUrl;
        $data   = self::getData($url);
        return json_decode($data, true);
    }
}
