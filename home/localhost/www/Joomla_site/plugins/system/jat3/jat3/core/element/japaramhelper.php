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

/**
 * Radio List Element
 *
 * @package  JAT3.Core.Element
 */
class JFormFieldJaparamhelper extends JFormField
{
    /**
     * The form field type.
     *
     * @var      string
     * @since    1.6
     */
    protected $type = 'Japaramhelper';

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {

        $func = (string) $this->element['function'] ? (string) $this->element['function'] : '';
        $value = $this->value ? $this->value : (string) $this->element['default'];

        if (substr($func, 0, 1) == '@') {
            $func = substr($func, 1);
            if (method_exists($this, $func)) {
                return $this->$func();
            }
        } else {
            $subtype = (isset($this->element['subtype'])) ? trim($this->element['subtype']) : '';
            if (method_exists($this, $subtype)) {
                return $this->$subtype();
            }
        }
        return;
    }

    /**
     * Method to get the field label markup.
     *
     * @return  string  The field label markup.
     */
    function getLabel()
    {
        $func = (string) $this->element['function'] ? (string) $this->element['function'] : '';
        if (substr($func, 0, 1) == '@' || !isset($this->label) || !$this->label)
            return;
        else
            return parent::getLabel();
    }

    /**
     * Render title: name="@title"
     *
     * @return string  Title markup
     */
    function title()
    {
        $_title = (string) $this->element['label'];
        $_description = $this->description;
        $_url = (isset($this->element['url'])) ? (string) $this->element['url'] : '';
        $class = (isset($this->element['class'])) ? (string) $this->element['class'] : '';
        $level = (isset($this->element['level'])) ? (string) $this->element['level'] : '';
        $group = (isset($this->element['group'])) ? (string) $this->element['group'] : '';
        $group = $group ? "id='params$group-group'" : "";
        if ($_title) {
            $_title = html_entity_decode(JText::_($_title));
        }

        if ($_description) {
            $_description = html_entity_decode(JText::_($_description));
        }
        if ($_url) {
            $_url = " <a target='_blank' href='{$_url}' >[" . html_entity_decode(JText::_("Demo")) . "]</a> ";
        }

        $regionID = time() + rand();

        $class_name = trim(str_replace(" ", "", strtolower($_title)));

        if ($level == 1) {
            $html = '
                <h4 rel="' . $level . '" class="block-head block-head-' . $class_name . ' open ' . $class . ' " ' . $group . ' id="' . $regionID . '">
                    <span class="block-setting" >' . $_title . $_url . '</span>
                    <span class="icon-help editlinktip hasTip" title="' . htmlentities($_description) . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    <a class="toggle-btn open" title="' . JText::_('Expand all') . '" onclick="showRegion(\'' . $regionID . '\', \'level' . $level . '\'); return false;">' . JText::_('Expand all') . '</a>
                    <a class="toggle-btn close" title="' . JText::_('Collapse all') . '" onclick="hideRegion(\'' . $regionID . '\', \'level' . $level . '\'); return false;">' . JText::_('Collapse all') . '</a>
                </h4>';
        } else {
            $html = '
                <h4 rel="' . $level . '" class="block-head block-head-' . $class_name . ' open ' . $class . ' " ' . $group . ' id="' . $regionID . '">
                    <span class="block-setting" >' . $_title . $_url . '</span>
                    <span class="icon-help editlinktip hasTip" title="' . htmlentities($_description) . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    <a class="toggle-btn" title="' . JText::_('Click here to expand or collapse') . '" onclick="showHideRegion(\'' . $regionID . '\', \'level' . $level . '\'); return false;">open</a>
                </h4>';
        }
        //<div class="block-des '.$class.'"  id="desc-'.$regionID.'">'.$_description.'</div>';

        return $html;
    }

    /**
     * Render js to control setting form.
     *
     * @return void
     */
    function group()
    {
        if (!defined('_JA_PARAM_HELPER')) {
            define('_JA_PARAM_HELPER', 1);
            $uri = str_replace(DS, "/", str_replace(JPATH_SITE, JURI::base(), dirname(__FILE__)));
            $uri = str_replace("/administrator", "", $uri);
            $javersion = new JVersion();
            if ($javersion->RELEASE == '1.7') {
                JHtml::_('behavior.framework', true);
            } else {
                JHTML::_('behavior.mootools');
            }
            JHTML::stylesheet($uri . '/assets/css/japaramhelper.css');
            JHTML::script($uri . '/assets/js/japaramhelper.js');
        }
        preg_match_all('/jform\\[([^\]]*)\\]/', $this->name, $matches);

        ?>
        <script type="text/javascript">
            window.addEvent("load", function(){
                <?php
        foreach ($this->element->children() as $option) {
                ?>
                    initjapramhelpergroup.delay(100, window, ["<?php echo $option['for']; ?>", {
                        val     : '<?php echo $option['value']; ?>',
                        els_str : '<?php echo JText::_(trim((string) $option));?>',
                        group   : '<?php echo @$matches[1][0]; ?>',
                        hideRow : '<?php echo $option['hideRow']; ?>'
                    }]);
                <?php
        };
                ?>
            });
        </script>
        <?php
        return;
    }

}