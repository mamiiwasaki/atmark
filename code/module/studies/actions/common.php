<?php
/*-----------------------------------------------------------------------------
 *  Lightweight framework Rev 1.3
 *-----------------------------------------------------------------------------
 *	2009-10-31 : initial version by Hide
 *  2015-05-12 : rebuild
 *
 *	common / common
 *----------------------------------------------------------------------------*/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."config.php");

class app_main extends ActionBase
{
	//	initialize
	function initialize(&$data, &$user, &$factory)
	{
		$this->debug_query  = false ;
		$this->debug_status = false ;
		return ;
	}
	//	dispatch
	function dispatch(&$data, &$user, &$factory)
	{
		//	action も module も無い場合に呼ばれる最後の common 
        $this->viewitem['MENU'] = getMenu();
        $this->viewitem['SUBMENU'] = getSubMenu();
		$this->viewitem['SIDE_BAR'] = getSideBar();
        $this->viewitem['HIGHT_JS'] = getHightJs();
		return ;
	}
}
