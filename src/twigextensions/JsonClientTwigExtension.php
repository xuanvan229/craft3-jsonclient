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

    public function fetchJson($options = [])
    {
        //return \view::render('settings', []);
        // return 'twitter feed...';

        if (!isset($options['url'])) {
          die('Required url parameter not set!');
        }
	$cookie = self::getCookie();
        $data = self::getUrl($options['url'], $cookie);

        return json_decode($data, true);

    }

		// Function for cURL
		private static function getUrl($url, $cookie) {
			error_reporting(0);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: {$cookie})");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$store = curl_exec($ch);
			curl_close($ch);

			return $store;
		}



}
