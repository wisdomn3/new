<?php

    /**
    * @package SP VirtueMart Category Menu
    * @author JoomShaper http://www.joomshaper.com
    * @copyright Copyright (c) 2010 - 2013 JoomShaper
    * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
    */ 

    // no direct access
    defined('_JEXEC') or die('Restricted access');

    require('helper.php');
    if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php');

    VmConfig::loadConfig();

    // Settings 
    $moduleclass_sfx = $params->get('moduleclass_sfx','');
    $title_wrapper = $params->get('title_wrapper','h4');
    $title_linkable = $params->get('title_linkable',1);

    // module info
    $module_id = $module->id;
    $module_name   = basename(dirname(__FILE__));

    // get categories
    $category_model = VmModel::getModel('Category');
    $categories = $category_model->getChildCategoryList(1, 0);  // vendor id, category id

    if(empty($categories)) return false;

    $modSPVMCatMenuHelper = new modSPVMCatMenuHelper();
    $modSPVMCatMenuHelper->categoryModel = $category_model;
    $modSPVMCatMenuHelper->settings = array('title_wrapper'=>$title_wrapper, 'title_linkable'=>$title_linkable);

    $modSPVMCatMenuHelper->generateTreeMenu($categories, 0);
    $tree = $modSPVMCatMenuHelper->getTree();

    $doc      = JFactory::getDocument();
    $cssFile  = JPATH_THEMES. '/'.$doc->template.'/css/'.$module_name.'.css';

    if(file_exists($cssFile)) {
        $doc->addStylesheet(JURI::base(true) . '/templates/'.$doc->template.'/css/'. $module_name . '.css');
    } else {
        $doc->addStylesheet(JURI::base(true) . '/modules/'.$module_name.'/assets/css/style.css');
    }
    require JModuleHelper::getLayoutPath($module_name, $params->get('layout', 'default'));