<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep;

use Illuminate\Container\Container;
use rsanchez\Deep\Model\Field;
use rsanchez\Deep\Model\Channel;
use rsanchez\Deep\Model\Entry;
use rsanchez\Deep\Model\Site;
use rsanchez\Deep\Model\UploadPref;
use rsanchez\Deep\Repository\FieldRepository;
use rsanchez\Deep\Repository\ChannelRepository;
use rsanchez\Deep\Repository\SiteRepository;
use rsanchez\Deep\Repository\UploadPrefRepository;
use rsanchez\Deep\Hydrator\HydratorFactory;

/**
 * IoC Container
 */
class Entries extends Container
{
    /**
     * Constructor
     *
     * Build all the dependencies
     */
    public function __construct()
    {
        $this->singleton('Field', function ($app) {
            return new Field();
        });

        $this->singleton('Channel', function ($app) {
            return new Channel();
        });

        $this->singleton('Site', function ($app) {
            return new Site();
        });

        $this->singleton('UploadPref', function ($app) {
            return new UploadPref();
        });

        $this->singleton('FieldRepository', function ($app) {
            return new FieldRepository($app->make('Field'));
        });

        $this->singleton('ChannelRepository', function ($app) {
            return new ChannelRepository($app->make('Channel'), $app->make('FieldRepository'));
        });

        $this->singleton('SiteRepository', function ($app) {
            return new SiteRepository($app->make('Site'));
        });

        $this->singleton('UploadPrefRepository', function ($app) {
            return new UploadPrefRepository($app->make('UploadPref'));
        });

        $this->singleton('HydratorFactory', function ($app) {
            return new HydratorFactory($app->make('SiteRepository'), $app->make('UploadPrefRepository'));
        });

        $this->singleton('Entry', function ($app) {
            Entry::setFieldRepository($app->make('FieldRepository'));
            Entry::setChannelRepository($app->make('ChannelRepository'));
            Entry::setHydratorFactory($app->make('HydratorFactory'));

            return new Entry();
        });
    }

    /**
     * Shortcut to the dependency-injected Entry model
     * @return mixed
     */
    public static function __callStatic($name, $args)
    {
        static $app;

        if (is_null($app)) {
            $app = new static();
        }

        return call_user_func_array(array($app->make('Entry'), $name), $args);
    }
}
