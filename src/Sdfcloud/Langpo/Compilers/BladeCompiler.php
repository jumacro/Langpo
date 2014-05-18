<?php

namespace Sdfcloud\Langpo\Compilers;

use Illuminate\View\Compilers\BladeCompiler as LaravelBladeCompiler;
use Illuminate\Filesystem\Filesystem;

/**
 * BladeCompiler 
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
class BladeCompiler extends LaravelBladeCompiler {

    /**
     * 
     * @param string $cachePath
     */
    public function setCachePath($cachePath) {
        $this->cachePath = $cachePath;
    }

    /**
     * 
     * @return string
     */
    public function getCachePath() {
        return $this->cachePath;
    }

    /**
     * 
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function setFiles(Filesystem $files) {
        $this->files = $files;
    }

    /**
     * 
     * @param string $path
     * @return string
     */
    public function getCompiledPath($path) {
        return parent::getCompiledPath($path) . ".php";
    }

    /**
     * 
     * @param string $path
     * @return string
     * 
     */
    public function compile($path) {
        $contents = $this->compileString($this->files->get($path));

        if (!is_null($this->cachePath)) {
            $compiled_path = $this->getCompiledPath($path);
            $this->files->put($compiled_path, $contents);
        }

        return $compiled_path;
    }

}
