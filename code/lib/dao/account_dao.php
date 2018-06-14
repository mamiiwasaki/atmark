<?php
/*-----------------------------------------------------------------------------
 *	Lightweight framework Rev 1.0
 *-----------------------------------------------------------------------------
 * 2016-02-04 : initial version Hide
 *
 * 銀行口座DAO
 *----------------------------------------------------------------------------*/
require_once CLASSES_DIR . 'dao_util.trait.php';

class account_dao extends DBIO {
	use daoUtilTrait;

	var $table_name = "M_ACCOUNT";
    
    // -------------------------------------------------------------------------
	//	銀行口座情報取得
	// -------------------------------------------------------------------------
	function getAccountInfo($code){
		return $this->getRowAssoc("select * from {$this->table_name} where ACCOUNT_CODE=?", array($code));
	}
}
