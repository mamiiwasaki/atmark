<?php
/*-----------------------------------------------------------------------------
 *	Lightweight framework Rev 1.3
 *-----------------------------------------------------------------------------
 *	2006-07-24 : initial version
 *	2015-05-12 : rebuild by hide
 *	
 *	
 *	data container
 *----------------------------------------------------------------------------*/
class dataContainer
{
	var $_attribute     = array();
	var $_error_code    = array();
	var $_error_message = array();

	/*--------------------------------------------------------------------------
	 * set Attribute
	 *-------------------------------------------------------------------------*/
	function setAttribute($name, $data)
	{
		$this->_attribute[$name] = $data;
	}
	/*--------------------------------------------------------------------------
	 * get Attribute
	 *-------------------------------------------------------------------------*/
	function getAttribute($key) {
		if (is_array($this->_attribute) ? !count($this->_attribute) : true) {
			return null;
		}
		if (func_num_args() == 1) {
			return array_key_exists($key, $this->_attribute) ? $this->_attribute[$key] : null;
		} else {
			$fga = func_get_args();
			foreach ($fga as $id => $node) {
				if ($id == 0) {
					if (array_key_exists($node, $this->_attribute)) {
						$data = $this->_attribute[$node];
					} else {
						return null;
					}
				} elseif ((is_array($data) && !is_array($node)) ? array_key_exists($node, $data) : false) {
					$data = $data[$node];
				} else {
					return null;
				}
			}
			return $data;
		}
	}
	/*--------------------------------------------------------------------------
	 * check Attribute
	 *-------------------------------------------------------------------------*/
	function hasAttribute($name){
		return isset($this->_attribute[$name]);
	}
	/*--------------------------------------------------------------------------
	 * remove Attribute
	 *-------------------------------------------------------------------------*/
	function removeAttribute($name){
		if (isset($this->_attribute[$name])){
			unset($this->_attribute[$name]);
		}
	}
	/*--------------------------------------------------------------------------
	 * clear Attribute
	 *-------------------------------------------------------------------------*/
	function cleanAttributes(){
		$this->_attribute = array();
	}
	/*--------------------------------------------------------------------------
	 * set post/get parameter to attribute
	 *-------------------------------------------------------------------------*/
	function getParameter($name, $default_parameter = false, $post_only = 3){
		if (func_num_args()==5) {
			$get  = func_get_arg(3);
			$post = func_get_arg(4);
		} else {
			$get  =& $_GET;
			$post =& $_POST;
		}
		if (is_array($name)) {
			foreach ($name as $key => $value) {
				//�Ƶ�Ū������򸡺�
				$gc = $this->getParameter(
					$value,
					false,
					$post_only,
					isset($get[$key]) ? $get[$key] : array(),
					isset($post[$key]) ? $post[$key] : array()
				);
				return $gc;
			}
		}
		if (($post_only == 3 || $post_only == 1) && isset($post[$name])) {
			return $post[$name];
		} elseif (($post_only == 3 || $post_only == 2) && isset($get[$name])) {
			return $get[$name];
		} else {
			return $default_parameter;
		}
	}
	/*--------------------------------------------------------------------------
	 * check parameter
	 *-------------------------------------------------------------------------*/
	function hasParameter($name, $post_only = 3){
		return $this->getParameter($name, false, $post_only) !== false;
	}
	/*--------------------------------------------------------------------------
	 * set error
	 *-------------------------------------------------------------------------*/
	function setError($name, $validator, $code, $message){
		$this->_error_code[$name][$validator]    = $code;
		$this->_error_message[$name][$validator] = $message;
	}
	/*--------------------------------------------------------------------------
	 * get all error 
	 *-------------------------------------------------------------------------*/
	function getErrors(){
		$i = 0;
		foreach ($this->_error_message as $key => $values) {
			foreach ($values as $id => $value) {
				$res["message"][$i] = $value;
				$res["code"][$i] = $this->_error_code[$key][$id];
				$res["keys"][$key][] = $i;
				$i++;
			}
		}
		$res["count"] = $i;
		return $res;
	}
	/*--------------------------------------------------------------------------
	 * get error by parameter
	 *-------------------------------------------------------------------------*/
	function getError($name){
		return isset($this->_error_message[$name]) ? array("message" => $this->_error_message[$name], "code" => $this->_error_code[$name]) : null;
	}
	/*--------------------------------------------------------------------------
	 * get error status
	 *-------------------------------------------------------------------------*/
	function hasError(){
		return (count($this->_error_code)+count($this->_error_message)) > 0;
	}
	/*--------------------------------------------------------------------------
	 * return clone
	 *-------------------------------------------------------------------------*/
	function dataClone() {
		return array(
			$this->_attribute,
			$this->_error_code,
			$this->_error_message,
		);
	}
	/*--------------------------------------------------------------------------
	 * replace data container
	 *-------------------------------------------------------------------------*/
	function dataReplace(&$data) {
		$this->_attribute     = $data[0];
		$this->_error_code    = $data[1];
		$this->_error_message = $data[2];
	}
	/*--------------------------------------------------------------------------
	 * clear error
	 *-------------------------------------------------------------------------*/
	function errorFree(){
		$this->_error_code    = array();
		$this->_error_message = array();
	}
}
