<?php

namespace Sdfcloud\Langpo\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Sdfcloud\Langpo\Facades\BladeCompiler;

/**
 * Compile Command 
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
class CompileCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'langpo:compile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compiles all blade templates in the given path';

    /**
     * Contsructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * 
     * @return void 
     * @throws \Sdfcloud\Langpo\NoTemplatesToCompileException
     */
    public function fire() {
        /**
         * compiler settings
         */
        $compiler_input = app_path() . DIRECTORY_SEPARATOR . $this->option('input_folder') . DIRECTORY_SEPARATOR;
        $compiler_output = storage_path() . DIRECTORY_SEPARATOR . $this->option('output_folder') . DIRECTORY_SEPARATOR;
        $compiler_levels = $this->option("levels");

        // add info
        $this->comment("  checking folder [$compiler_input] for blade templates, [$compiler_levels] levels deep");

        /**
         * determine glob pattern based on number of levels
         */
        $pattern = getGlobPattern($compiler_levels);

        // set final pattern
        $compiler_pattern = $compiler_input . "{" . $pattern . "}*.blade.php";

        /**
         * set proper cache path for compiler
         */
        BladeCompiler::setCachePath($compiler_output);

        /**
         * check if compiler output folder exists
         * and if not, attempt to create it
         */
        if (!File::isDirectory($compiler_output)) {
            File::makeDirectory($compiler_output);
        }

        /**
         * get all blade templates from the input folder using the generated pattern
         * the GLOB_BRACE constant is used to transform the {x, y, z} pattern to OR x, OR y, OR z
         */
        $templates = File::glob($compiler_pattern, GLOB_BRACE);

        // sanity check
        if (count($templates) < 1) {
            throw new \Sdfcloud\Langpo\NoTemplatesToCompileException("No templates were found to compile in [$compiler_input]");
        }

        // file array and counter
        $f = array();
        $i = 0;

        // loop through all files and compile each
        foreach ($templates as $tpl) {
            $f[] = BladeCompiler::compile($tpl);
            $i++;
        }

        // output success
        $this->info("  [$i] blade templates found and successfully compiled");
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
        /**
         * set defaults
         */
        $defaults = array(
            'input_folder' => Config::get("langpo::config.compiler.input_folder"),
            'output_folder' => Config::get("langpo::config.compiler.output_folder"),
            'levels' => Config::get("langpo::config.compiler.levels"),
        );

        /**
         * return the options array
         */
        return array(
            array('input_folder', 'i', InputOption::VALUE_REQUIRED, 'The input folder to scan for blade template files, relative to the app/ folder', $defaults['input_folder']),
            array('output_folder', 'o', InputOption::VALUE_REQUIRED, 'The output folder to place the compiled templates, relative to the app/storage folder', $defaults['output_folder']),
            array('levels', 'l', InputOption::VALUE_REQUIRED, 'The number of subdirectories to scan for blade templates', $defaults['levels']),
        );
    }

}
