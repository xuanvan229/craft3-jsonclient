<?php

/**
 * Craft jsonclient plugin
 *
 * @author    dolphiq
 * @copyright Copyright (c) 2017 dolphiq
 * @link      https://dolphiq.nl/
 */

namespace loca\jsonclient;

use Craft;
use craft\base\Plugin;
use loca\jsonclient\twigextensions\JsonClientTwigExtension;


// use dolphiq\jsonclient\controllers\jsonclientController;



class JsonClientPlugin extends \craft\base\Plugin
{
    public static $plugin;

    public $hasCpSettings = false;

    // table schema version
    public $schemaVersion = '1.0.0';

    public function init()
    {
        parent::init();

        self::$plugin = $this;

        Craft::$app->view->twig->addExtension(new JsonClientTwigExtension());

        Craft::info('loca/jsonclient plugin loaded', __METHOD__);
    }
}
