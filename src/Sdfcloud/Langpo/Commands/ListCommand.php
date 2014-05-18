<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sdfcloud\Langpo\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Sdfcloud\Langpo\Facades\Langpo;

/**
 * List Command 
 * 
 * 
 * 
 * PHP 5.0 / Laravel 4.0
 * 
 * @author        Mithun Das (mithundas79) on behalf of Pinpoint Media Design (pinpointgraphics)
 * @copyright     Copyright 2014, Pinpoint Media Design
 * @package       Sdfcloud.Langpo
 * @property      Langpo $Langpo
 * @since         SDFCloud 3.0
 * 
 */
class ListCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'langpo:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lists all locales and encodings supported by the application (read from config file)';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire() {
        /**
         * lists
         */
        $locales = Config::get("langpo::locales.list");
        $encodings = Config::get("langpo::encodings.list");

        /**
         * list locales
         */
        $this->info("  listing supported locales: [" . count($locales) . "]");

        // loop through locales
        foreach ($locales as $l)
            $this->comment("  - " . $l);

        /**
         * list encodings
         */
        $this->info("  listing supported encodings: [" . count($encodings) . "]");

        // loop through encodings
        foreach ($encodings as $e)
            $this->comment("  - " . $e);

        /**
         * list current default settings
         */
        $this->info("  current default setting: [" . Langpo::getLocaleAndEncoding() . "]");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions() {
        return array();
    }

}
