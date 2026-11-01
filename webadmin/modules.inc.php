<?php
/**
 * Module loading handler
 *
 * Loads module functions and classes when needed, instead of all at once
 */

/**
 * Make sure required global variables are defined
 */
global $_GLOBALS;
if (!isset($_GLOBALS)) {
    $_GLOBALS = [];
}

global $_GLOBALS;
if (!array_key_exists("modules", $_GLOBALS)) {
    $_GLOBALS['modules'] = [];
}

if (!function_exists('moduleExists')) {
    /**
     * Determines whether the given module has been defined
     * $module_name => The name of the module to test
     *
     * @param string $module_name
     *
     * @return bool TRUE if defined, otherwise FALSE
     */
    function moduleExists($module_name)
    {
        global $_GLOBALS;
        return array_key_exists($module_name, $_GLOBALS["modules"]) && is_array($_GLOBALS['modules'][$module_name]);
    }
}
if (!function_exists('moduleLoaded')) {
    /**
     * bool moduleLoaded($module_name)
     *
     * Determines whether the given module has been loaded already
     * $module_name => The name of the module to test
     *
     * @param string $module_name
     *
     * @return bool TRUE if already loaded, otherwise FALSE
     */
    function moduleLoaded($module_name)
    {
        global $_GLOBALS;
        return moduleExists($module_name) && $_GLOBALS['modules'][$module_name]['loaded'];
    }
}
if (!function_exists('moduleValidName')) {
    /**
     * bool moduleValidName(string $module_name)
     *
     * Determines whether the name given is valid, based on the variable naming rules
     * $module_name => The module name to test
     *
     * @param string $module_name
     *
     * @return bool TRUE if valid, otherwise FALSE
     */
    function moduleValidName($module_name)
    {
        if (!is_string($module_name)) {
            return false;
        }

        return (preg_match('/^[a-z_][a-z0-9_]*$/', $module_name) == 1);
    }
}
if (!function_exists('moduleLoad')) {
    /**
     * mixed moduleLoad(string $module_name[, string $instantiate_class])
     *
     * Loads the specified module, and optionally instantiates and returns a shared
     * instance of a specified class
     * $module_name => The name of the module to load
     * $instantiate_class => The name of the class to instantiate
     *
     * @param string       $module_name
     * @param false|string $instantiate_class
     *
     * @return mixed
     * FALSE if there is a problem loading the module
     * null if there is a problem instantiating the specified class
     * object handle if an object was produced or found
     * TRUE if there were no problems in loading the dependancies
     */
    function moduleLoad($module_name, $instantiate_class = false)
    {
        global $_GLOBALS;

        /**
         * Check if a valid request
         */
        if (!moduleExists($module_name) || !moduleValidName($module_name)) {
            return false;
        }

        /**
         * Attempt to include the files needed
         */
        foreach ($_GLOBALS['modules'][$module_name]['include_files'] as $file_name) {
            if (is_file($file_name)) {
                include_once $file_name;
            }
        }

        $_GLOBALS['modules'][$module_name]['loaded'] = true;

        /**
         * Return TRUE if we just wanted to include the files
         */
        if (false === $instantiate_class) {
            return true;
        }

        /**
         * Return NULL if we can't instantiate
         */
        if (!class_exists($instantiate_class)) {
            return null;
        }

        /**
         * Return the object if already instantiated
         */
        if (is_object($_GLOBALS['modules'][$module_name][$instantiate_class])) {
            return $_GLOBALS['modules'][$module_name][$instantiate_class];
        }

        /**
         * Instantiate the object
         */
        return $_GLOBALS['modules'][$module_name][$instantiate_class] = new $instantiate_class();
    }
}
if (!function_exists('moduleOverride')) {
    /**
     * mixed moduleOverride(string $module_name[, string $file_name1 [, string $...]])
     *
     * Addes or overwrites a module's dependancies
     * $module_name => The name of the module to add/overwrite
     * array => The ordered list of files to include for this module
     *
     * @param string   $module_name
     * @param string[] $files, ...
     *
     * @return string|false module_name on success and FALSE on failure
     */
    function moduleOverride($module_name, ...$files)
    {
        global $_GLOBALS;

        /**
         * Return false if we can't change the module
         */
        if (!moduleValidName($module_name)) {
            return false;
        }

        if (moduleLoaded($module_name)) {
            return false;
        }

        /**
         * Apply the changes
         */
        $_GLOBALS['modules'][$module_name] = [
            'include_files' => [],
            'loaded'        => false,
            'instances'     => [],
        ];

        /**
         * Add the files to the dependancies
         */
        foreach ($files as $file_name) {
            $_GLOBALS['modules'][$module_name]['include_files'][] = $file_name;
        }

        /**
         * Return the module name if there were no problems
         */
        return $module_name;
    }
}
