<?php

namespace Sdfcloud\Langpo;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;

/**
 * Langpo Class
 * 
 * Langpo parsing the pot file and lang string extract
 * 
 * PHP 5.0 / Laravel 4.0
 * 
 * @author        Mithun Das (mithundas79) on behalf of Pinpoint Media Design (pinpointgraphics)
 * @copyright     Copyright 2014, Pinpoint Media Design
 * @package       Sdfcloud.Langpo
 * @property      Lang $Lang
 * @since         SDFCloud 3.0
 * 
 */
class Langpo {

    /**
     * current locale
     *
     * @var string
     */
    protected $locale = null;

    /**
     * current encoding
     *
     * @var string
     */
    protected $encoding = null;

    /**
     * constructor
     * accepts an optional $locale, otherwise the default will be used
     *
     * @param string $locale
     */
    public function __construct() {
        // getting locale from session and use that locale
        // otherwise use default
        $session_locale = Session::get('langpo_locale', null);
        $locale = (is_null($session_locale)) ? Config::get("langpo::config.default_locale") : $session_locale;
        Session::forget('langpo_locale');

        // check if encoding is present in the session
        $session_encoding = Session::get('langpo_encoding', null);
        $encoding = (is_null($session_encoding)) ? Config::get("langpo::config.default_encoding") : $session_encoding;
        Session::forget('langpo_encoding');

        //set encoding and locale
        $this->setEncoding($encoding)->setLocale($locale);
        
        
        //set textdomain
        $textdomain = Config::get("langpo::config.textdomain");
        $path = Config::get("langpo::config.path_to_mo");
        $this->setTextDomain($textdomain, $path);
    }

    /**
     * This method will set encoding and return the current object to support method chaining
     * 
     * @param string $encoding
     * @return \Sdfcloud\Langpo\Langpo
     * @throws InvalidEncodingException
     */
    public function setEncoding($encoding) {
        // fetch encodings list
        $encodings = Config::get('langpo::encodings.list');

        // check encoding validity
        if (!in_array($encoding, $encodings))
            throw new InvalidEncodingException("The provided encoding [$encoding] does not exist in the list of valid encodings [config/encodings.php]");

        // set encoding
        $this->encoding = $encoding;

        // save locale to session
        Session::put('langpo_encoding', $this->encoding);

        // return - with allowing object chaining
        return $this;
    }

    public function setLocale($locale) {
        // fetch locales list
        $locales = Config::get('langpo::locales.list');

        // check validiy
        if (!in_array($locale, $locales))
            throw new InvalidLocaleException("The provided locale [$locale] does not exist in the list of valid locales [config/locales.php]");

        // set locale
        $this->locale = $locale;

        // get locale and encoding
        $localecodeset = $this->getLocaleAndEncoding();

        // set environment variable
        if (!putenv('LC_ALL=' . $localecodeset))
            throw new EnvironmentNotSetException("The given locale [$localecodeset] could not be set as environment [LC_ALL] variable; it seems it does not exist on this system");

        if (!putenv('LANG=' . $localecodeset))
            throw new EnvironmentNotSetException("The given locale [$localecodeset] could not be set as environment [LANG] variable; it seems it does not exist on this system");


        if (!setlocale(LC_ALL, $localecodeset) && !\App::runningInConsole())
            throw new LocaleNotFoundException("The given locale [$localecodeset] could not be set; it seems it does not exist on this system");

        // save locale to session
        Session::put('langpo_locale', $this->locale);

        // return - allow object chaining
        return $this;
    }

    /**
     * merge the locale and encoding into a single string
     * 
     * @return string
     */
    public function getLocaleAndEncoding() {
        // windows compatibility - use only the locale, not the encoding
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            return $this->getLocale();
        else
            return $this->getLocale() . "." . $this->getEncoding();
    }

    /**
     * get locale
     * 
     * @return string
     * @throws LocaleNotSetException
     */
    public function getLocale() {
        // sanity check
        if (!$this->hasLocale())
            throw new LocaleNotSetException("The locale needs to be set before calling L4gettext::getLocale()");

        // return locale
        return $this->locale;
    }

    /**
     * check locale's presence
     * 
     * @return boolean
     */
    public function hasLocale() {
        // check if locale has been set
        if (isset($this->locale) && !is_null($this->locale))
            return true;
        else
            return false;
    }

    /**
     * get the encoding
     * 
     * @return sring
     * @throws EncodingNotSetException
     */
    public function getEncoding() {
        // sanity check
        if (!$this->hasEncoding())
            throw new EncodingNotSetException("The encoding needs to be set before calling L4gettext::getEncoding()");

        // return encoding
        return $this->encoding;
    }

    /**
     * check if encoding is present in object
     * 
     * @return boolean
     */
    public function hasEncoding() {
        // check if encoding has been set
        if (isset($this->encoding) && !is_null($this->encoding))
            return true;
        else
            return false;
    }

    /**
     * Set text domain
     * 
     * @param type $textdomain
     * @param type $path
     * @return \Sdfcloud\Langpo\Langpo
     */
    public function setTextDomain($textdomain, $path) {
        // full path to localization messages
        $full_path = app_path() . DIRECTORY_SEPARATOR . $path;

        // sanity check - path must exist relative to app/ folder
        if (!File::isDirectory($full_path))
            $this->createFolder($path);

        // bind text domain
        bindtextdomain($textdomain, $full_path);

        // set text domain
        textdomain($textdomain);

        // allow object chaining
        return $this;
    }

    /**
     * auto creates the LC_MESSAGES folder for each set locale, if not exist
     * 
     * 
     * @param type $path
     * @return \Sdfcloud\Langpo\Langpo
     * @throws LocaleFolderCreationException
     */
    public function createFolder($path) {
        // set full path
        $full_path = app_path() . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $this->getLocale() . DIRECTORY_SEPARATOR . 'LC_MESSAGES';

        // check if the folder exists
        if (!File::isDirectory($full_path)) {
            // folder does not exist, attempt to create it
            // throws an ErrorException when failed
            if (!File::makeDirectory($full_path, 0755, true))
                throw new LocaleFolderCreationException("The locale folder [$full_path] does not exist and could not be created automatically; please create the folder manually");
        }

        // allow object chaining
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function __toString() {
        return (string) $this->getLocaleAndEncoding();
    }

}
