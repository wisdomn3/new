<?php

    /**
    * @package SP VirtueMart Category Menu
    * @author JoomShaper http://www.joomshaper.com
    * @copyright Copyright (c) 2010 - 2013 JoomShaper
    * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
    */    

    // no direct access
    defined('_JEXEC') or die('Restricted access');

    if( !class_exists( 'VmConfig' ) ) require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php');

    $config= VmConfig::loadConfig();
    if( !class_exists( 'VirtueMartModelVendor' ) ) require(JPATH_VM_ADMINISTRATOR.'/models/vendor.php');
    if( !class_exists('TableMedias') ) require(JPATH_VM_ADMINISTRATOR.'/tables/medias.php');
    if( !class_exists('TableCategories') ) require(JPATH_VM_ADMINISTRATOR.'/tables/categories.php');
    if( !class_exists( 'VirtueMartModelCategory') ) require(JPATH_VM_ADMINISTRATOR.'/models/category.php');

    if( !class_exists( 'modSPVMCatMenuHelper') )
    {
        class modSPVMCatMenuHelper
        {
            public $categoryModel;
            private $tree;
            public $settings = array();
            public function getTree()
            {
                return $this->tree;
            }

            private function getItemsCount($categories)
            {
                return count($categories);
            }

            public function generateTreeMenu($categories, $deep=0)
            {
                $increment = 0;
                foreach ($categories as $category) {

                    $child =  $this->categoryModel->getChildCategoryList(1, $category->virtuemart_category_id);
                    $isparent = false;
                    $this->tree .= '<li';
                    if( $deep==0 and is_array($child) and !empty($child) ){
                        $this->tree .= ' class="parent"';
                        $isparent = true;
                        $increment++;
                    }
                    $this->tree .= '>';
                    $link = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$category->virtuemart_category_id);
                    $name = $category->category_name;
                    $catID = $category->virtuemart_category_id;

                    if( $deep==1 ){
                        if( !empty($this->settings['title_wrapper']) ){
                            $this->tree .= '<'.$this->settings['title_wrapper'].'>';
                            if( $this->settings['title_linkable']==1 ) $this->tree .='<a href="'.$link.'"  data-deep="'.$deep.'">';
                            $this->tree .= $name;
                            if( $this->settings['title_linkable']==1 ) $this->tree .= '</a>';
                            $this->tree .='</'.$this->settings['title_wrapper'].'>';
                        } else {
                            if( $this->settings['title_linkable']==1 ) $this->tree .='<a href="'.$link.'" data-deep="'.$deep.'">';
                            $this->tree .= $name;

                            if( $this->settings['title_linkable']==1 ) $this->tree .= '</a>';
                        }
                    } else {
                        $this->tree .='<a href="'.$link.'">';
                        $this->tree .= $name;
                        if($isparent) $this->tree .='<i class="icon-angle-'.((JFactory::getDocument()->direction=='ltr')?'right':'left').' pull-right"></i>';
                        $this->tree .= '</a>';
                    }

                    // child recursion
                    if( is_array($child) and !empty($child) ){
                        $this->tree .= '<ul class="sp-vmcol-'.$this->getItemsCount($child).'">';
                        $this->generateTreeMenu($child, $deep+1 );

                        // adding module position
                        if($isparent){
                            $this->tree .='<li class="sp-vmcategorymenu-module-wrapper"><div class="sp-vmcategorymenu-module inc-'.$increment.'"
                            data-position="sp-vmcategorymenu-'.$increment.'">';
                            $this->tree .=JFactory::getDocument()->loadRenderer( 'modules' )->render("sp-vmcategorymenu-{$increment}", array('style'=>'flat'));
                            $this->tree .='</div></li>';  
                        }
                        $this->tree .= '</ul>';
                    }
                    $this->tree .= '</li>';
                }
            }
        }
}