<?php
/**
 *------------------------------------------------------------------------------
 * PDO class 2016/01/05 Rel 1.6 by Hide		modify TYPO & etc .....
 *------------------------------------------------------------------------------
 * HOW TO USE
 * ----------
 *   $con = new DBIO();
 *   $con->connect();
 *   $sql = "select * from table where id='?'";
 *   $prm = array('bar');
 *   $con->sqbind($sql,$prm);
 *   $result = $con->fetch();
 * 	 $con->close();
 *
 *------------------------------------------------------------------------------
 * DBIO->DBIO($target = 'default')				//	constructor
 * DBIO->connnect()								//	connect db
 * DBIO->close()								//	close db
 * DBIO->sqlbind($sql, $params)					//	bind place holder
 * DBIO->query($sql , $params)					//	exec query (alias : sqlbind)
 * DBIO->fetch()								//	compat : fetch
 * DBIO->fetch_assoc()							//	compat : fetch_assoc
 * DBIO->numrows()								//	compat : numrows (mysql only)
 * DBIO->affected_rows()						//	compat : affected rows
 * DBIO->getAll($sql, $params = null)			//	exec + get all records
 * DBIO->getAllAssoc($sql, $params = null)		//	exec + get all records (assoc)
 * DBIO->getRow($sql, $params = null)			//	exec + get one record
 * DBIO->getRowAssoc($sql, $params = null)		//	exec + get one record  (assoc)
 * DBIO->getOne($sql, $params,$key)				//	exec + one record
 * DBIO->getColumn($sql, $params)				//	exec + get column name
 *----------------------------------------------------------------------------*/
class DBIO{
	// private  -------------------------------------------------------------------------
	private $dsn ;				//	(D)ata(S)ource(N)ame
	private $db_user ;			//	user
	private $db_pass ;			//	password
	private $link = false;		//	link status
	private $dbh;				//	db handler
	private $sth;				//	db statement
	var $pkey_name='ID';

	// constructor
	function __construct() {
		$this->dsn     = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8';
		$this->db_user = DB_USER;
		$this->db_pass = DB_PASS;
		//logsave("DBIO" , "INIT TARGET (".$target.")");
		return $this->connect();
	}

	// connect
	function connect(){
		//	already connected
		if($this->link)
			return false;

		try {
			$this->dbh  = new PDO($this->dsn , $this->db_user, $this->db_pass);
			$this->sth  = null;
			$this->link = true;
		} catch (PDOException $e) {
			$this->link = false;
			logsave("dbio", $this->dbh);
			logsave("DBIO" , "Connection failed (".$this->dsn.")");
			return false ;
		}
		return true ;
	}

	// close
	function close(){
		//	db not connect
		if(!$this->link)
			return false ;

		$this->dbh = null;
		$this->sth = null;
		$this->link = false ;
		return true;
	}

	// bind
	function sqlbind($sql, $params = NULL){
		//	db not connect
		if(!$this->link)
			return false ;

		$this->sth = $this->dbh->prepare($sql);
		if($params != NULL){
			for ($i=0; $i<count($params); ++$i){
				// int型とdate型に空文字を入れるとエラーになるので、空だったら、nullをセットするように変更
				// $this->sth->bindValue(($i+1), $params[$i]);

				if(is_null($params[$i]) || $params[$i]==='') {
					$this->sth->bindValue(($i+1), null, PDO::PARAM_NULL);
				} else {
					$this->sth->bindValue(($i+1), $params[$i], PDO::PARAM_STR);
				}
			}
		}
		$result = $this->sth->execute();

		if($result === false){
			$e = $this->sth->errorInfo ();
			logsave("DBIO query failed!! " , $e[2]."(".$sql.")");
			//echo $sql;
		//	print_r($e);
			//echo("PDO_ERROR\n\n");
		}

		return $result;
	}

	// escape
	function escape($str){
		//	db not connect
		if(!$this->link)
			return false ;

		return $this->dbh->quote($str);
	}

	// query ( alias : sqlbind )
	function query($sql , $params = NULL){
		return $this->sqlbind($sql,$params);
	}

	// fetch
	function fetch(){
		//	link check
		if(!$this->link)
			return false;

		$row = $this->sth->fetch();
		return $row;
	}

	// fetch assoc
	function fetch_assoc(){
		//	link check
		if(!$this->link)
			return false;

		return $this->sth->fetch(PDO::FETCH_ASSOC);
	}

	// numrows
	function numrows(){
		//	link check
		if(!$this->link)
			return false;

		//	obsolete function
		return $this->sth->rowCount();
	}

	//	affected rows
	function affected_rows(){
		//	link check
		if(!$this->link)
			return false;

		return $this->sth->rowCount();
	}

	// get All result by array
	function getAll($sql, $params = null) {
		//	link check
		if(!$this->link)
			return false;
		$this->sqlbind($sql, $params);
		$row = $this->sth->fetchAll();
		$this->sth->closeCursor();

		return $row;
	}

	// get All result by array ( assoc )
	function getAllAssoc($sql, $params = null) {
		//	link check
		if(!$this->link)
			return false;
		$this->sqlbind($sql, $params);
		$row = $this->sth->fetchAll(PDO::FETCH_ASSOC );
		$this->sth->closeCursor();

		return $row;
	}

	// get One result by array
	function getRow($sql, $params = null) {
		//	link check
		if(!$this->link)
			return false;

		$this->sqlbind($sql, $params);
		$row = $this->sth->fetch();
		$this->sth->closeCursor();

		return $row;
	}

	// get One Result by array (assoc)
	function getRowAssoc($sql, $params = null) {
		//	link check
		if(!$this->link)
			return false;

		$this->sqlbind($sql, $params);
		$row = $this->sth->fetch(PDO::FETCH_ASSOC);
		$this->sth->closeCursor();

		return $row;
	}

	// get One result by Value
	function getOne($sql, $params = null, $key = 0) {
		//	link check
		if(!$this->link)
			return false;

		$this->sqlbind($sql, $params);
		$row = $this->sth->fetch();
		$this->sth->closeCursor();

		return $row[$key];
	}

	// get Column Name by array
	function getColumn($sql, $params) {
		$this->sqlbind($sql, $params);

		$columns = array();
		for ($i = 0; $i < $this->sth->columnCount(); $i++) {
			$meta = $this->sth->getColumnMeta($i);
			$columns[] = $meta['name'];
		}
		$this->sth->closeCursor();
		return $columns;
	}

	// get fields
	function getFields($table_name=''){
		if (empty($table_name)) $table_name = $this->table_name;
		$result = $this->getAllAssoc("DESC $table_name");
		$arr = array();
		if(!empty($result)){
			foreach($result as $val){
				$arr[$val["Field"]] = $val;
			}
		}
		return $arr;
	}
}
