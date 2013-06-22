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
?>
<?php
$menus = Array (
  'mega'=>JText::_('MEGA_MENU'),
  'css'=>JText::_('CSS_MENU'),
  'dropline'=>JText::_('DROPLINE_MENU'),
  'split'=>JText::_('SPLIT_MENU')
);
?>

<h3><?php echo JText::_('MENU_STYLE')?></h3>

<div class="ja-box-usertools">
  <ul class="ja-usertools-menu clearfix">
  <?php foreach ($menus as $menu=>$title) : ?>

    <li class="menu-<?php echo $menu.($this->getParam('menu')==$menu?'-active':'') ?>">
      <input type="radio" id="user_menu_<?php echo $menu ?>" name="user_menu" value="<?php echo $menu ?>" <?php echo $this->getParam('menu')==$menu?'checked="checked"':'' ?> />
      <label for="user_menu_<?php echo $menu ?>" title="<?php echo $title ?>">
        <span><?php echo $title ?></span>
      </label>
    </li>
  <?php endforeach; ?>
  </ul>
</div>
