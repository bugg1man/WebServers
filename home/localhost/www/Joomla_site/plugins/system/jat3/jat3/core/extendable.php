<?php
/**
 * ------------------------------------------------------------------------
 * JA T3 System plugin for Joomla 1.7
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

// No direct access
defined('_JEXEC') or die();

define('_PHP_', intval(phpversion()));

if (! function_exists('property_exists')) {
    /**
     * Check property of object exists or not
     *
     * @param object $oObject    Checked object
     * @param string $sProperty  Property name
     *
     * @return bool  TRUE if exists, otherwise FALSE
     */
    function property_exists($oObject, $sProperty)
    {
        if (is_object($oObject)) {
            $oObject = get_class($oObject);
        }

        return array_key_exists($sProperty, get_class_vars($oObject));
    }
}

/**
 * Check method of object is callable or not
 *
 * @param object $oObject  Checked object
 * @param string $sMethod  Method name
 *
 * @return bool  TRUE if exists, otherwise FALSE
 */
function method_callable($oObject, $sMethod)
{
    // must be object or string
    if (! is_object($oObject) && ! is_string($oObject)) {
        return false;
    }

    return array_key_exists($sMethod, array_flip(get_class_methods($oObject)));
}

/**
 * Make object extendable
 *
 * @param string $classname  Class name
 *
 * @return void
 */
function make_object_extendable($classname)
{
    if (_PHP_ < 5) {
        overload($classname);
    }
}

if (_PHP_ >= 5) {
    include_once dirname(__FILE__) . DS . 'object.5.php';
} else {
    include_once dirname(__FILE__) . DS . 'object.4.php';
}