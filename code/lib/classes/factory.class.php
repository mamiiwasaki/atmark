<?php
/*-----------------------------------------------------------------------------
 *	Lightweight framework Rev 1.3
 *-----------------------------------------------------------------------------
 *	2006-07-24 : initial version
 *	2015-05-12 : rebuild by hide
 *	
 *	
 *	factory class
 *----------------------------------------------------------------------------*/
class factory
{
	var $data;
	var $user;
	var $request_module;
	var $request_action;
	var $_chain = array();

	/*--------------------------------------------------------------------------
	 * constructor
	 *-------------------------------------------------------------------------*/
	function __construct(&$data, &$user)
	{
		$this->data =& $data;
		$this->user =& $user;
	}

	/*--------------------------------------------------------------------------
	 * create
	 *-------------------------------------------------------------------------*/
	function create($action, $module = false)
	{
		if ($module === false) {
			$module = $this->request_module;
		}

		include_once(MODULE_DIR. $module. ACTION_DIR_NAME. $action. ".php");
		$action = new $action();
		return $action;
	}
	/*--------------------------------------------------------------------------
	 * forward
	 *-------------------------------------------------------------------------*/
	function forward($action, $module = false)
	{
		$action = $this->create($action, $module);
		$action->initialize($this->data, $this->user, $this);
		$action->dispatch($this->data, $this->user, $this);
	}
	/*--------------------------------------------------------------------------
	 * chain
	 *-------------------------------------------------------------------------*/
	function chain($name, $action, $module = false)
	{
		$this->_chain[] = array($name, array($action, $module));
	}
	/*--------------------------------------------------------------------------
	 * go
	 *-------------------------------------------------------------------------*/
	function go()
	{
		$res = array();
		// s = array();
		$data = clone $this->data;
		foreach ($this->_chain as $value) {
//			$this->data = clone $data;	//	clone -----------------------------------*/
			$this->data = $data;		//	ta;	//	clone -----------------------------------*/
			ob_start();
			$this->forward($value[1][0], $value[1][1]);
			$res[$value[0]] = ob_get_contents();
			ob_clean();
		}
		return $res;
	}
}
