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
defined('_JEXEC') or die;

t3import('core.define');
t3import('core.path');

/**
 * T3Framework object
 *
 * @package JAT3.Core
*/
class T3Framework extends JObject
{
    function t3_init()
    {
        t3import('core.parameter');
        t3import('core.extendable');
        t3import('core.template');
        t3import('core.basetemplate');
        t3import('core.cache');
        t3import('core.head');
        t3import('core.hook');
        // Remove JDocumentHTML for compatible J1.6 & J1.7
        // if (!class_exists ('JDocumentHTML', false)) t3import ('core.joomla.documenthtml');
        if (! class_exists('JView', false)) t3import('core.joomla.view');
        if (! class_exists('JModuleHelper', false)) t3import('core.joomla.modulehelper');
        // if (! class_exists('JPagination', false)) t3import('core.joomla.pagination');

        //Load template language
        $this->loadLanguage('tpl_' . T3_ACTIVE_TEMPLATE, JPATH_SITE);

        $params = T3Common::get_template_based_params();
        //instance cache object.
        $devmode = $params ? $params->get('devmode', '0') == '1' : false;
        $t3cache = T3Cache::getT3Cache($devmode);

        //Check if enable T3 info mode. Enable by default (if not set)
        if ($params->get('infomode', 1) == 1) {
            if (! JRequest::getCmd('t3info') && JRequest::getCmd('tp') && JComponentHelper::getParams('com_templates')->get('template_positions_display')) JRequest::setVar('t3info', JRequest::getCmd('tp'));
        }

        $key = T3Cache::getPageKey();
        $user = &JFactory::getUser();
        $data = null;

        if ($devmode || JRequest::getCmd('cache') == 'no') {
            $t3cache->setCaching(false);
            JResponse::allowCache(false);
        } else {
            $t3cache->setCaching(true);
            JResponse::allowCache(true);
            //T3Common::log('allow cache');
        }

        $data = $t3cache->get($key);
        if ($data) {
            $mainframe = JFactory::getApplication();

            // Check HTTP header
            $if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : false;
            $if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) : false;

            $cache_time = (int) substr($data, 0, 20);
            $etag = md5($key);

            if ($if_modified_since && $if_none_match
                && $if_modified_since == $cache_time
                && $if_none_match == $etag
            ) {
                header('HTTP/1.x 304 Not Modified', true);
                $mainframe->close();
            }
            $data = JString::substr($data, 20);

            // Check cached data
            if (! preg_match('#<jdoc:include\ type="([^"]+)" (.*)\/>#iU', $data)) {
                $token = JUtility::getToken();
                $search = '#<input type="hidden" name="[0-9a-f]{32}" value="1" />#';
                $replacement = '<input type="hidden" name="' . $token . '" value="1" />';
                $data = preg_replace($search, $replacement, $data);
                
                JResponse::setHeader('Last-Modified', gmdate('D, d M Y H:i:s', $cache_time) . ' GMT', true);
                JResponse::setHeader('ETag', $etag, true);
                
                JResponse::setBody($data);

                echo JResponse::toString($mainframe->getCfg('gzip'));

                if (JDEBUG) {
                    global $_PROFILER;
                    $_PROFILER->mark('afterCache');
                    echo implode('', $_PROFILER->getBuffer());
                }

                $mainframe->close();
            }
        }
        //Preload template
        t3import('core.preload');
        $preload = T3Preload::getInstance();
        $preload->load();

        $doc = & JFactory::getDocument();
        $t3 = T3Template::getInstance($doc);
        $t3->_html = $data;
    }

    function init_layout()
    {
        $t3 = T3Template::getInstance();
        if (! $t3->_html) $t3->loadLayout();
    }
}