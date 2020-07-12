<?php
// $Id: service.php,v 1.2 2004/08/31 14:22:19 ackbarr Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //


/**
 * sstatusService class
 *
 * Information about an individual service
 *
 * <code>
 * $hService =& xoops_getmodulehandler('Service', 'sstatus');
 * $service =& $hService->get(1);
 * $service_id = $service->getVar('id');
 * $memos =& $service->getMemos();
 * echo $service->currentStatus();
 * echo $service->lastUpdated();
 * </code>
 *
 * @author Brian Wahoff <bwahoff@epcusa.com>
 * @access public
 * @package sstatus
 */
class sstatusService extends XoopsObject {
	function sstatusService($id = null) 
	{
		$this->initVar('id', XOBJ_DTYPE_INT, null, false);
		$this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 35);
		$this->initVar('description', XOBJ_DTYPE_TXTAREA, null, false, 1024);
		$this->initVar('status', XOBJ_DTYPE_INT, 1, true);
		$this->initVar('lastUpdated', XOBJ_DTYPE_INT, null, true);
		
		if (isset($id)) {
			if (is_array($id)) {
				$this->assignVars($id);
			}
		} else {
			$this->setNew();
		}
	}
	
	
	/**
	 * retrieve all memos attached to this service object
	 * 
	 * @return array array of {@link sstatusMemo} objects
	 * @access	public	
	 */	
	function getMemos($limit = 0, $start = 0) 
	{
		$arr = array();
		$id = intval($this->getVar('id'));
		if (!$id) {
			return $arr;
		}
		$hMemos    =& xoops_getmodulehandler('memo', 'sstatus');
		$criteria  =& new CriteriaCompo(new Criteria('serviceid', $id));
		$criteria->setSort('posted');
		$criteria->setOrder('DESC');
		$criteria->setLimit($limit);
		$criteria->setStart($start);
		
		$arr       =& $hMemos->getObjects($criteria);
				
		return $arr;
	}
	
   /**
    * Counts the number of memos
    *
    * @return int $count number of memos for a service
    * @access public
    */
	function getMemoCount()
	{
	    $id = intval($this->getVar('id'));
	    $hMemos    =& xoops_getmodulehandler('memo', 'sstatus');
		$criteria  =& new CriteriaCompo(new Criteria('serviceid', $id));
		return $hMemos->getCount($criteria);
	}	
	
	/**
	 * determine the currentStatus of the service
	 * 
	 * @return int $status Current Status
	 * @access	public	
	 */		
	function currentStatus()
	{
		return $this->getVar('status');
	}
	
	/**
	 * determine last time the service was updated relative to the current user
	 * 
	 * @return 	string	Timestamp of last update
	 * @access	public	
	 */		
	function lastUpdated()
	{
		return formatTimestamp($this->getVar('lastUpdated'));
	}
	
   /**
    * create a memo for the service
    *
    * @return object $memo new {@link sstatusMemo} object
    * @access public
    */
	function &createMemo($text, $status=1)
	{
	    global $xoopsUser;
	    
	    $hMemo =& xoops_getmodulehandler('memo', 'sstatus');
        $memo =& $hMemo->create();
        $memo->setVar('serviceid', $this->getVar('id'));
        $memo->setVar('memo', $text);
        $memo->setVar('status', $status);
        $memo->setVar('uid', $xoopsUser->getVar('uid'));
        return $memo;
    }
    
   /**
    * insert new memo into database
    * 
    * @return bool true if insert was successful, false if not
    * @access public
    */
    function insertMemo($memo)
    {
        // If not a status memo, exit function
        if(!is_a($memo, 'sstatusMemo')) return false;
        
        $hMemo =& xoops_getmodulehandler('memo', 'sstatus');
        $memo->setVar('serviceid', $this->getVar('id'));
        return $hMemo->insert($memo);
    }
    
   /**
    * gets the last memo posted to DB
    * 
    * @return object $rec {@link sstatusMemo} object, false if no memos in db
    * @access public
    */
    function &getLastMemo()
    {
       $hMemos =& xoops_getmodulehandler('memo', 'sstatus');
       $crit = new CriteriaCompo(new Criteria('serviceid', $this->getVar('id')));
       $crit->setLimit(1);
       $crit->setOrder("DESC");
       $crit->setSort("posted");
       $arr = $hMemos->getObjects($crit);
       if(count($arr) > 0){
            list($rec) = $arr;
            return $rec;
       } else {
            return false;
       }
    }
}

/**
 * sstatusServiceHandler class
 *
 * Storage Handler for sstatus_Service class
 *
 * @author Brian Wahoff <bwahoff@epcusa.com>
 * @access public
 * @package sstatus
 */
 
class sstatusServiceHandler extends XoopsObjectHandler {
	/**
   * Database connection
   * 
   * @var	object
   * @access	private
   */
	var $_db;
	
	/**
   * Name of child class
   * 
   * @var	string
   * @access	private
   */
	var $classname = 'sstatusservice';
	
	/**
	 * DB Table Name
	 *
	 * @var 		string
	 * @access 	private
	 */
	var $_dbtable = 'sstatus_services';
	
	/**
	 * Constructor
	 *
	 * @param	object   $db    reference to a xoopsDB object
	 */
	function sstatusServiceHandler(&$db) 
	{
		$this->_db = $db;
	}
	
	/**
	 * Singleton - prevent multiple instances of this class
	 *
	 * @param	object  &$db    {@link XoopsHandlerFactory} 
	 * @return object $instance Instance of {@link sstatusServiceHandler} object
	 * @access	public
	 */
	function &getInstance(&$db)
	{
		static $instance;
		if (!isset($instance)) {
			$instance = new sstatusServiceHandler($db);
		}
		return $instance;
	}
	
	/**
	 * create a new service object
	 * @return	object $service new {@link sstatusService} object
	 * @access	public
	 */
	function &create()
	{
		return new $this->classname();
	}
	
	/**
	 * retrieve a service object from the database
	 * @param	int	$id	ID of Service
	 * @return	object	$obj {@link sstatusService} object
	 * @access	public
	 */
	function &get($id)
	{
		$id = intval($id);
		if ($id > 0) {
			$sql = $this->_selectQuery(new Criteria('id', $id));
			if (!$result = $this->_db->query($sql)) {
				return false;
			}
			$numrows = $this->_db->getRowsNum($result);
			if ($numrows == 1) {
				$obj = new $this->classname($this->_db->fetchArray($result));
				return $obj;
			}
		}
		return false;
	}
	
	/**
	 * Create a "select" SQL query
	 * @param object $criteria {@link CriteriaElement} to match
	 * @return	string SQL query
	 * @access	private
	 */	
	function _selectQuery($criteria = null)
	{
		$sql = sprintf('SELECT * FROM %s', $this->_db->prefix($this->_dbtable));
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
			if ($criteria->getSort() != '') {
				$sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
			}
		}
		return $sql;
	}

	/**
	 * count objects matching a criteria
	 * 
	 * @param object $criteria {@link CriteriaElement} to match
	 * @return int count of objects
	 * @access	public	 
	 */	
	function getCount($criteria = null)
	{
		$sql = 'SELECT COUNT(*) FROM '.$this->_db->prefix($this->_dbtable);
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
		}
		if (!$result =& $this->_db->query($sql)) {
			return 0;
		}
		list($count) = $this->_db->fetchRow($result);
		return $count;
	}
	
	
	/**
	 * retrieve objects from the database
	 * 
	 * @param object $criteria {@link CriteriaElement} conditions to be met
	 * @return array array of {@link sstatusService} objects
	 * @access	public	
	 */	
	function &getObjects($criteria = null)
	{
		$ret    = array();
		$limit  = $start = 0;
		$sql    = $this->_selectQuery($criteria);
		if (isset($criteria)) {		
			$limit = $criteria->getLimit();
			$start = $criteria->getStart();
		}

		$result = $this->_db->query($sql, $limit, $start);
		// If no records from db, return empty array
		if (!$result) {
			return $ret;
		}
		
		// Add each returned record to the result array
		while ($myrow = $this->_db->fetchArray($result)) {
			$obj = new $this->classname($myrow);
			$ret[] =& $obj;
			unset($obj);
		}
		return $ret;
	}
	
	/**
	* store a service in the database
	* 
	* @param object $service reference to the {@link sstatusService} object
	* @param bool $force
	* @return bool FALSE if failed, TRUE if already present and unchanged or successful
	* @access	public
	*/
	function insert(&$obj, $force = false)
	{
		// Make sure object is of correct type
		if (get_class($obj) != $this->classname) {
			return false;
		}
		
		// Make sure object needs to be stored in DB
		if (!$obj->isDirty()) {
			return true;
		}
		
		// Make sure object fields are filled with valid values
		if (!$obj->cleanVars()) {
			return false;
		}
		
		// Copy all object vars into local variables
		foreach ($obj->cleanVars as $k => $v) {
			${$k} = $v;
		}
		
		// Create query for DB update
		if ($obj->isNew()) {
			// Determine next auto-gen ID for table
			$id = $this->_db->genId($this->_db->prefix($this->_dbtable).'_uid_seq');
			$sql = sprintf("INSERT INTO %s (id, name, description, status, lastUpdated) VALUES (%u, %s, %s, %u, %u)", $this->_db->prefix($this->_dbtable), $id, $this->_db->quoteString($name), $this->_db->quoteString($description), $status, time());
		} else {
			$sql = sprintf("UPDATE %s SET name = %s, description = %s, status = %u, lastUpdated = %u WHERE id = %u", $this->_db->prefix($this->_dbtable), $this->_db->quoteString($name), $this->_db->quoteString($description), $status, time(), $id);
		}
		
		// Update DB
		if (false != $force) {
			$result = $this->_db->queryF($sql);
		} else {
			$result = $this->_db->query($sql);
		}
		
		if (!$result) {
			return false;
		}
		
		//Make sure auto-gen ID is stored correctly in object
		if (empty($id)) {
			$id = $this->_db->getInsertId();
		}
		$obj->assignVar('id', $id);
		return true;
	}
	
	
	/**
	 * delete a service from the database
	 * 
	 * @param object $obj reference to the {@link sstatusService} obj to delete
	 * @param bool $force
	 * @return bool FALSE if failed.
	 * @access	public
	 */
	function delete(&$obj, $force = false)
	{
		if (get_class($obj) != $this->classname) {
			return false;
		}
		
		// Remove all service memos first
		$hMemo  =& xoops_getmodulehandler('memo', 'sstatus');
		if (!$hMemo->deleteAll(new Criteria('serviceid', $obj->getVar('id')))) {
			return false;
		}
	    	
		$sql = sprintf("DELETE FROM %s WHERE id = %u", $this->_db->prefix($this->_dbtable), $obj->getVar('id'));
		if (false != $force) {
			$result = $this->_db->queryF($sql);
		} else {
			$result = $this->_db->query($sql);
		}
		if (!$result) {
			return false;
		}
		return true;
	}
	
	/**
	 * delete services matching a set of conditions
	 * 
	 * @param object $criteria {@link CriteriaElement} 
	 * @return bool FALSE if deletion failed
	 * @access	public	 
	 */
	function deleteAll($criteria = null)
	{
		$sql = 'DELETE FROM '.$this->_db->prefix($this->_dbtable);
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
		}
		if (!$result = $this->_db->query($sql)) {
			return false;
		}
		return true;
	}
}

?>