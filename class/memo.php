<?php
// $Id: memo.php,v 1.2 2004/08/31 14:22:19 ackbarr Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //


/**
 * sstatusMemo class
 *
 * @author Eric Juden <ericj@epcusa.com>
 * @author Brian Wahoff <bwahoff@epcusa.com>
 * @access public
 * @package sstatus
 */
class sstatusMemo extends XoopsObject {
    function sstatusMemo($id = null) 
	{
	    $this->initVar('id', XOBJ_DTYPE_INT, null, false);
	    $this->initVar('serviceid', XOBJ_DTYPE_INT, null, false);
	    $this->initVar('uid', XOBJ_DTYPE_INT, null, false);
		$this->initVar('memo', XOBJ_DTYPE_TXTAREA, null, false, 1024);
		$this->initVar('status', XOBJ_DTYPE_INT, 1, true);
		$this->initVar('posted', XOBJ_DTYPE_INT, null, true);
		
		if (isset($id)) {
			if (is_array($id)) {
				$this->assignVars($id);
			}
		} else {
			$this->setNew();
		}
	}
	
   /**
    * Retrieve the service to which a memo is attached
    * 
    * @return object $service {@link sstatusService} object
    * @access public
    */
	function getService()
	{
	    $id = intval($this->getVar('serviceid'));
	    if(!$id) {
	        return false;
	    }
	    
	    $hService =& xoops_getmodulehandler('service');
	    $service =& $hService->get($id);
	    return $service;
	}
	
   /**
    * Format the date posted in user's local timezone
    *
    * @return string Timestamp of last update
    * @access public
    */
	function posted()
	{
		return formatTimestamp($this->getVar('posted'));
	}
}
	
/**
 * sstatusMemoHandler class
 *
 * Memo Handler for sstatusMemo class
 *
 * @author Eric Juden <ericj@epcusa.com>
 * @author Brian Wahoff <bwahoff@epcusa.com>
 * @access public
 * @package sstatus
 */
 
class sstatusMemoHandler extends XoopsObjectHandler {
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
	var $classname = 'sstatusmemo';
	
    /**
     * DB table name
     * 
     * @var string
     * @access private
     */
     var $_dbtable = 'sstatus_memos';
	
	/**
     * Constructor
     *
     * @param	object   $db    reference to a xoopsDB object
     */	
	function sstatusMemoHandler(&$db)
	{
	    $this->_db = $db;
    }
    
   /**
    * Singleton - prevent multiple instances of this class
    *
    * @param object &$db {@link XoopsHandlerFactory}
    * @return object $instance Instance of {@link sstatusServiceHandler} object
    * @access public
    */
    function &getInstance(&$db)
    {
        static $instance;
        if(!isset($instance)) {
            $instance = new sstatusMemoHandler($db);
        }
        return $instance;
    }
    
   /**
    * create a new memo object
    * @return object $obj new {@link sstatusMemo} object
    * @access public
    */
    function &create()
    {
        return new $this->classname();
    }
    
   /**
    * retruieve a memo object from the database
    * @param int $id ID of Memo
    * @return object $obj instance of {@link sstatusMemo} object
    * @access public
    */
    function &get($id)
    {
        $id = intval($id);
        if($id > 0) {
            $sql = $this->_selectQuery(new Criteria('id', $id));
            if(!$result = $this->_db->query($sql)) {
                return false;
            }
            $numrows = $this->_db->getRowsNum($result);
            if($numrows == 1) {
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
        if(isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' .$criteria->renderWhere();
            if($criteria->getSort() != '') {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' 
                    ' .$criteria->getOrder();
            }
        }
        return $sql;
    }
    
    /**
	 * count objects matching a criteria
	 * 
	 * @param object $criteria {@link CriteriaElement} to match
	 * @return int $count count of objects
	 * @access	public	 
	 */	
    function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->_db->prefix($this->_dbtable);
        if(isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' .$criteria->renderWhere();
        }
        if(!$result =& $this->_db->query($sql)) {
            return 0;
        }
        list($count) = $this->_db->fetchRow($result);
        return $count;
    }
    
    /**
	 * retrieve objects from the database
	 * 
	 * @param object $criteria {@link CriteriaElement} conditions to be met
	 * @return array array of {@link sstatusMemo} objects
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
	* store a memo in the database
	* 
	* @param object $memo reference to the {@link sstatusMemo} object
	* @param bool $force force update on GET requests
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
			$sql = sprintf("INSERT INTO %s (id, serviceid, uid, memo, status, posted) 
			                VALUES (%u, %u, %u, %s, %u, %u)", $this->_db->prefix($this->_dbtable), 
			                $id, $serviceid, $uid, $this->_db->quoteString($memo), $status, 
			                time());
		} else {
			$sql = sprintf("UPDATE %s 
		                    SET serviceid = %u, 
		                        uid = %u,
                                memo = %s,
                                status = %u 
                            WHERE id = %u",
			                $this->_db->prefix($this->_dbtable), 
			                $serviceid, $uid,$this->_db->quoteString($memo), $status, $id);
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
	 * delete a memo from the database
	 * 
	 * @param object $obj reference to the {@link sstatusMemo} obj to delete
	 * @param bool $force Force delete on GET requests
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
	 * delete memo matching a set of conditions
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