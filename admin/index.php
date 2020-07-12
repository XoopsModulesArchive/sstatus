<?php
// $Id: index.php,v 1.4 2004/08/30 21:55:20 ackbarr Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//include(XOOPS_ROOT_PATH . '/include/cp_header.php');
include '../../../include/cp_header.php';
include_once(XOOPS_ROOT_PATH . '/modules/sstatus/class/service.php');
include_once(XOOPS_ROOT_PATH . '/modules/sstatus/class/memo.php');
include_once(XOOPS_ROOT_PATH . '/class/pagenav.php');
include_once(XOOPS_ROOT_PATH . '/modules/sstatus/language/english/main.php');

$op = 'default';

if ( isset( $_GET['op'] ) )
{
    $op = $_GET['op'];
    if ( isset( $_GET['serviceid'] ) )
    {
        $serviceid = intval( $_GET['serviceid'] );
    } 
} 

switch ( $op )
{
    case "addService":
        addService();
        break;
    
    case "modifyService":
        modifyService();
        break;
        
    case "modifyMemo":
        modifyMemo();
        break;
        
    case "addMemo":
        addMemo();
        break;
        
    case "viewServices":
        viewServices();
        break;
        
    case "viewServiceDetails":
        viewServiceDetails();
        break;
    case "groupperm":
        groupPerm();
        break;
    case "default":
    default:
        sstatus_default();
        break;
}

function addService()   // same as addService.php
{

    if(!isset($_POST['addedService']))
    {
        xoops_cp_header();
        echo "<table width='100%' cellpadding='2' cellspacing='1' class='outer'>
              <tr class='odd'><td><h1>" . _AM_SSTATUS_ADDSERVICE_TEXT . "</h1></td></tr></table>";
        echo "<form method='post' action='index.php'>";
        echo "<table width='100%' cellpadding='2' cellspacing='1' class='outer'>";
        echo "<tr class='odd'><th nowrap align='right' width='10%'>" . _AM_SSTATUS_NAME . 
              "</th><td><input type='text' name='serviceName'></td></tr>";
        echo "<tr class='odd'><th nowrap align='right'>" . _AM_SSTATUS_DESC . "</th><td>
              <textarea name='description' rows='4' cols='60'></textarea></td></tr>";
        echo "<tr class='odd'><th nowrap align='right'>" . _AM_SSTATUS_STATUS . "</th><td>
              <select name='startStatus'>
                  <option value='1'>" . _AM_SSTATUS_OPTION1 ."
                  <option value='2'>" . _AM_SSTATUS_OPTION2 ."
                  <option value='3'>" . _AM_SSTATUS_OPTION3 ."
              </select></td></tr>";
        echo "<tr class='odd'><th nowrap align='right'>" . _AM_SSTATUS_MEMO_TEXT . "</th><td>
              <textarea name='memo' rows='4' cols='60'></textarea>"
             . _AM_SSTATUS_ADD_OPTIONAL . "</td></tr>";
        echo "<tr class='odd'><th></th><td>
              <input type='submit' value='" . _AM_SSTATUS_BUTTON_SUBMIT . "' name='addedService'>
              <input type='hidden' name='op' value='addService'>
              <input type='reset' value='" . _AM_SSTATUS_BUTTON_RESET . "'></td></tr></table></form>";
        xoops_cp_footer();
    } else {
        // Add new service
        $hService =& xoops_getmodulehandler('service', 'sstatus');
        $service =& $hService->create();
        
        $service->setVar('name', $_POST['serviceName']);
        $service->setVar('description', $_POST['description']);
        $service->setVar('status', $_POST['startStatus']);
        
        if($hService->insert($service))
        {
            if(strlen($_POST['memo']) > 0) {
                $memo =& $service->createMemo($_POST['memo'], $_POST['startStatus']);
                $service->insertMemo($memo);
            }
            // Notification
            $notification_handler =& xoops_gethandler('notification');
            // Notification array
            $tags = array();
            $tags['SERVICE_NAME'] = $service->getVar('name');
            
            $notification_handler->triggerEvent('global', 0, 'new_service', $tags);
            $message = _SSTATUS_MESSAGE_ADD_SERVICE;     // Successfully added new service
        } else {
            $message = _SSTATUS_MESSAGE_ADD_SERVICE_ERROR . $service->getHtmlErrors();   
        }
        redirect_header("index.php", 3, $message);    
    }
}

function modifyService()    // same as modifyService.php
{

    xoops_cp_header();
    // Get the id of the service
    if(isset($_GET['id'])){
        $id = intval($_GET['id']);
    }
    
    if(isset($_POST['modified'])){
        $hService    =& xoops_getmodulehandler('service', 'sstatus');
        $serviceInfo =& $hService->get($id);
        $serviceInfo->setVar('name', $_POST['newServiceName']);
        $serviceInfo->setVar('description', $_POST['newServiceDescription']);
        if($hService->insert($serviceInfo)) {
            // Notification
            $notification_handler =& xoops_gethandler('notification');
            // Notification array
            
            $tags[] = array();
            $tags['SERVICE_MODIFIED_URL'] = XOOPS_URL . '/modules/sstatus/service.php?id=' . $id;
            
            // Trigger notification events (Global and individual)
            $notification_handler->triggerEvent('global', 0, 'modified_service_status', $tags);
            $notification_handler->triggerEvent('service', $id, 'modified_this_service_status', $tags);
            $message = _SSTATUS_MESSAGE_MODIFY_SERVICE;
        } else {
            $message = _SSTATUS_MESSAGE_MODIFY_SERVICE_ERROR;
        }
        redirect_header("index.php?op=viewServiceDetails&id=" . $id, 3, $message);
    } elseif (isset($_POST['deleteService'])){
        $hService    =& xoops_getmodulehandler('service', 'sstatus');
        $serviceInfo =& $hService->get($id);
        
        // Delete service
        if($hService->delete(&$serviceInfo)){
            global $xoopsModule;
            $module_id = $xoopsModule->getVar('mid');
            xoops_groupperm_deletebymoditem($module_id, _AM_SSTATUS_GROUP_PERM_NAME, $id);
            // Notification
            $notification_handler =& xoops_gethandler('notification');  // New notification object
            // Notification array
            $tags[] = array();
            $tags['SERVICE_NAME'] = $serviceInfo->getVar('name');
            
            $notification_handler->triggerEvent('global', 0, 'removed_service', $tags);
            $notification_handler->triggerEvent('service', $id, 'removed_this_service', $tags);
            
            global $xoopsModule;
            $module_id = $xoopsModule->getVar('mid');   // Get module id
            xoops_notification_deletebyitem($module_id, 'global', 0);       // Remove notification for global
            xoops_notification_deletebyitem($module_id, 'service', $id);    // Remove notification for service
            
            $message = _SSTATUS_MESSAGE_DELETE_SERVICE;      // Display on successful delete
        } else {
            $message = _SSTATUS_MESSAGE_DELETE_SERVICE_ERROR;
        }
        redirect_header("index.php?op=viewServices", 3, $message);
    } else {
        $start = $limit = 0;
        if(isset($_GET['limit'])){
            $limit = intval($_GET['limit']);
        }
        if(isset($_GET['start'])){
            $start = intval($_GET['start']);
        }
        // Make sure start is greater than 0
        $start = max($start, 0);
        // Make sure limit is betwen 10 and 40
        $limit = max($limit, 10);
        $limit = min($limit, 40);
        
        $hService       =& xoops_getmodulehandler('service', 'sstatus');
        $serviceInfo    =& $hService->get($id);
        $member_handler =& xoops_gethandler('member');
        $memoList       = $serviceInfo->getMemos($limit, $start);
        $total          = $serviceInfo->getMemoCount();
        $pageNav        = new XoopsPageNav($total, $limit, $start, "op=modifyService&id=$id&start");
        
        foreach($memoList as $memo){
            $user =& $member_handler->getUser($memo->getVar('uid'));
            $aMemos[] = array('id'=>$memo->getVar('id'),
                           'serviceid'=>$memo->getVar('serviceid'),
                           'uid'=>$memo->getVar('uid'),
                           'memo'=>$memo->getVar('memo'),
                           'status'=>$memo->getVar('status'),
                           'posted'=>$memo->posted(),
                           'uname'=>$user->getVar('uname'),
                           'userinfo'=>XOOPS_URL . '/userinfo.php?uid=' . $memo->getVar('uid'),
                           'memoInfo'=> 'index.php?op=modifyMemo&id=' . $memo->getVar('id'));
        }
        if(!$lastRec  =& $serviceInfo->getLastMemo()) {
            $sstatus_lastUser = '';
            $userInfoPage = '';
        } else {
            $lastUser =& $member_handler->getUser($lastRec->getVar('uid'));
            $sstatus_lastUser = $lastUser->getVar('uname');
            $userInfoPage = XOOPS_URL . '/userinfo.php?uid=' . $lastRec->getVar('uid');
        }
        
        // Define variables
        $sstatus_serviceid = $serviceInfo->getVar('id');
        $sstatus_serviceName = $serviceInfo->getVar('name');
        $sstatus_serviceDesc = $serviceInfo->getVar('description');
        $sstatus_serviceStatus = $serviceInfo->getVar('status');
        $sstatus_lastUpdated = $serviceInfo->lastUpdated();
        
        echo "<table width='100%' cellpadding='2' cellspacing='1' class='outer'>
              <tr class='odd'><td><h1>". _AM_SSTATUS_MODIFY_SERVICE ."</h1></td></tr></table>";
        echo "<form method='post' action='index.php?op=modifyService&id=". $id ."'>";
        echo "<table width='100%' cellpadding='2' cellspacing='1' class='outer'>
              <tr class='odd'><th nowrap>". _AM_SSTATUS_NAME ."</th>";
        echo "<td nowrap><input type='text' name='newServiceName' value='". $sstatus_serviceName ."'></td></tr>";
        echo "<tr class='odd'><th nowrap>". _AM_SSTATUS_DESC ."</th>
              <td>
              <textarea name='newServiceDescription' rows='4' cols='60'>". $sstatus_serviceDesc ."</textarea></td></tr>";
        echo "<tr class='odd'><th></th>
              <td nowrap>
              <input type='submit' value='". _AM_SSTATUS_BUTTON_SUBMIT ."' name='modified'>
              <input type='reset' value='". _AM_SSTATUS_BUTTON_RESET ."'>
              <input type='submit' value='". _AM_SSTATUS_BUTTON_DELETESERVICE ."' name='deleteService'>
              </td></tr></table></form>";
        echo "<h2>". _AM_SSTATUS_PREVIOUS_INFO ."</h2>";
        echo "<table border='1' width='100%' cellpadding='2' cellspacing='1' class='outer'>
              <tr class='odd'><th nowrap>". _AM_SSTATUS_STATUS ."</th>
              <th nowrap>". _AM_SSTATUS_MEMO_TEXT ."</th>
              <th nowrap>". _AM_SSTATUS_POSTED ."</th>
              <th nowrap>". _AM_SSTATUS_UPDATED_BY ."</th></tr>";
    if(isset($aMemos)){ // Make sure $aMemos is not empty
        foreach($aMemos as $memo){
            echo "<tr class='odd'><td nowrap><img src='../images/status_icon". $memo['status'].".GIF' border='0'></td>
                  <td nowrap><a href='". $memo['memoInfo'] ."'>". $memo['memo'] ."</a></td>
                  <td nowrap>". $memo['posted'] ."</td>
                  <td nowrap><a href='". $memo['userinfo'] ."'>". $memo['uname'] ."</a></td></tr>";
        }
    }
        echo "</table>";
        echo "<div id='pageNav'>" . $pageNav->renderNav() . "</div>";
        xoops_cp_footer();
    }      
}

function modifyMemo()   // same as modifyMemo.php
{

    xoops_cp_header();

    // Get the id of the service
    if(isset($_GET['id'])){
        $memoid = intval($_GET['id']);
    }
    if(isset($_POST['modifiedMemo']))
    {   
        $hMemo       =& xoops_getmodulehandler('memo', 'sstatus');
        $memoInfo    =& $hMemo->get($memoid);
        $serviceid = $memoInfo->getVar('serviceid');
        
        $memoInfo->setVar('status', $_POST['newStatus']);
        global $xoopsUser;
        $userName = $xoopsUser->getVar('uname');
        $time = $memoInfo->getVar('posted');
        $memoInfo->setVar('memo', $_POST['newMemo'] . "\n" . sprintf(_SSTATUS_MODIFIED_BY, $userName, formatTimestamp($time)));
        
        if($hMemo->insert($memoInfo)) {
            // Notification
            $notification_handler =& xoops_gethandler('notification');
            // Notification array
            $tags[] = array();
            $tags['SERVICE_NAME'] = $serviceInfo->getVar('name');
            $tags['SERVICE_MODIFIED_URL']= XOOPS_URL . '/modules/sstatus/service.php?id=' . $serviceid;
            
            // Trigger events for notification
            $notification_handler->triggerEvent('global', 0, 'modified_service_status', $tags);
            $notification_handler->triggerEvent('service', $serviceid, 'modified_this_service_status', $tags);
            $message = '_SSTATUS_MESSAGE_MODIFY_MEMO';
        } else {
            $message = _SSTATUS_MESSAGE_MODIFY_MEMO_ERROR;
        }
        redirect_header("index.php?op=modifyService&id=" . $serviceid, 3, $message);
    } elseif (isset($_POST['deleteMemo'])){
        $hMemo       =& xoops_getmodulehandler('memo', 'sstatus');
        $memoInfo    =& $hMemo->get($memoid);
        $serviceid = $memoInfo->getVar('serviceid');
        if($hMemo->delete(&$memoInfo)){
            $message = _SSTATUS_MESSAGE_DELETE_MEMO;
        } else {
            $message = _SSTATUS_MESSAGE_DELETE_MEMO_ERROR;
        }
        redirect_header("index.php?op=modifyService&id=" . $serviceid, 3, $message);
    } else {
        $hMemo       =& xoops_getmodulehandler('memo', 'sstatus');
        $memoInfo    =& $hMemo->get($memoid);
        $sstatus_oldStatus = $memoInfo->getVar('status');
        $sstatus_oldMemo = $memoInfo->getVar('memo');
     
        echo "<table width='100%' cellpadding='2' cellspacing='1' class='outer'>";
        echo "<tr class='odd'><td><h1>". _AM_SSTATUS_MODIFY_MEMO ."</h1></td></tr></table>";
        echo "<form method='post' action='index.php?op=modifyMemo&id=". $memoid ."'>";
        echo "<table width='100%' cellpadding='2' cellspacing='1' class='outer'>";
        echo "<tr class='odd'><th nowrap>". _AM_SSTATUS_STATUS ."</th>";
        echo "<td><select name='newStatus'>";
        $select_array = array('1'=>_AM_SSTATUS_OPTION1, '2'=>_AM_SSTATUS_OPTION2, '3'=>_AM_SSTATUS_OPTION3);
            foreach ($select_array as $id=>$value){
                if($sstatus_oldStatus == $id){
                    $selected = "selected='selected'";
                } else {
                    $selected = "";
                }
                echo "<option value='".$id ."'". $selected .">". $value ."</option>";
            }
        echo "</select></td></tr>";
        echo "<tr class='odd'><th nowrap>"._AM_SSTATUS_MEMO_TEXT."</th><td>
              <textarea name='newMemo' rows='4' cols='60'>". $sstatus_oldMemo ."</textarea>
              </td></tr>";
        echo "<tr class='odd'><th></th>
              <td nowrap>
              <input type='submit' value='". _AM_SSTATUS_BUTTON_SUBMIT ."' name='modifiedMemo'>
              <input type='reset' value='". _AM_SSTATUS_BUTTON_RESET ."'>
              <input type='submit' value='"._AM_SSTATUS_BUTTON_DELETEMEMO ."' name='deleteMemo'>
              </td></tr></table></form>";
        xoops_cp_footer();
    }
}

function addMemo()  // same as addMemo.php
{

    // Get the id of the service
    if(isset($_GET['id'])){
        $id = intval($_GET['id']);
    }
    
    if(!isset($_POST['newMemoSubmitted']))
    {
        xoops_cp_header();        
        echo "<table width='100%' cellpadding='2' cellspacing='1' class='outer'>";
        echo "<tr class='odd'><td><h1>" . _AM_SSTATUS_ADD_MEMO_TEXT . "</h1></td></tr></table>";
        echo "<form method='post' action='index.php?op=addMemo&id=". $id ."'>";
        echo "<table width='100%' cellpadding='2' cellspacing='1' class='outer'>";
        echo "<tr class='odd'><th nowrap align='right'>" . _AM_SSTATUS_STATUS . "</th><td>
              <select name='newStatus'>
                  <option value='1'>" . _AM_SSTATUS_OPTION1 ."
                  <option value='2'>" . _AM_SSTATUS_OPTION2 ."
                  <option value='3'>" . _AM_SSTATUS_OPTION3 ."
              </select></td></tr>";
        echo "<tr class='odd'><th nowrap align='right'>" . _AM_SSTATUS_MEMO_TEXT . "</th>
              <td>
              <textarea name='newMemo' rows='4' cols='60'></textarea>
              </td></tr>";
        echo "<tr class='odd'><th></th><td>
              <input type='submit' value='" . _AM_SSTATUS_BUTTON_SUBMIT . "' name='newMemoSubmitted'>
              <input type='reset' value='" . _AM_SSTATUS_BUTTON_RESET . "'></td></tr></table></form>";
           
        xoops_cp_footer();
    } else {
        $hService =& xoops_getmodulehandler('service', 'sstatus');
        $currService =& $hService->get($id);
        
        if($_POST['newMemo'] == ''){
            $message = _SSTATUS_MESSAGE_ADD_MEMO_BLANK;
            redirect_header("addMemo.php?id=" . $id, 3, $message);
        }
    
        $memo =& $currService->createMemo($_POST['newMemo'], $_POST['newStatus']);
        if($currService->insertMemo($memo)){
            $currService->setVar('status', $_POST['newStatus']);
            $hService->insert($currService);
            // Notification
            $notification_handler =& xoops_gethandler('notification');
            // Notification array
            $tags[] = array();      // Create array for notification variables
            $tags['SERVICE_NAME'] = $currService->getVar('name');
            $tags['SERVICE_UPDATED_URL'] = XOOPS_URL . '/modules/sstatus/service.php?id=' . $id;
            
            // Trigger notification events
            $notification_handler->triggerEvent('global', 0, 'changed_status', $tags);
            $notification_handler->triggerEvent('service', $id, 'changed_this_service_status', $tags);
            $message = _SSTATUS_MESSAGE_ADD_MEMO;
        } else {
            $message = _SSTATUS_MESSAGE_ADD_MEMO_ERROR;
        }
        redirect_header("index.php?op=viewServiceDetails&id=". $id, 3, $message);
    }
}

function viewServices() // same as index.php
{

    xoops_cp_header();
    $start = $limit = 0;
    if(isset($_GET['limit'])){
        $limit = intval($_GET['limit']);
    }
    if(isset($_GET['start'])){
        $start = intval($_GET['start']);
    }
    
    // Make sure start is greater than 0
    $start = max($start, 0);
    
    // Make sure limit is betwen 10 and 40
    $limit = max($limit, 15);
    $limit = min($limit, 40);

    $hService =& xoops_getmodulehandler('service', 'sstatus');
    $crit = new Criteria('','');
    $crit->setOrder('DESC');
    $crit->setSort('lastUpdated');
    
    $crit->setLimit($limit);
    $crit->setStart($start);
    $total = $hService->getCount($crit);
    
    $serviceInfo = $hService->getObjects($crit);
    
    $pageNav = new  XoopsPageNav($total, $limit, $start, 'op=viewServices&start');
        
    $aServices = array();
    foreach($serviceInfo as $service) {
        $aServices[] = array('id'=>$service->getVar('id'),
                        'name'=>$service->getVar('name'),
                        'status'=>$service->getVar('status'),
                        'lastUpdated'=>$service->lastUpdated(),
                        'url'=>XOOPS_URL . '/modules/sstatus/admin/index.php?op=viewServiceDetails&id=' .
                        $service->getVar('id'));
    }
    echo "<table width='100%' cellpadding='2' cellspacing='1' class='outer'><tr class='odd'><td>";
    echo "<h1>" . _AM_SSTATUS_INDEX_TITLE . "</h1>";
    echo "</td></tr></table>";
    echo "<table border='1' width='100%' cellpadding='2' cellspacing='1' class='outer'><tr class='odd'>";
    echo "<th nowrap>" . _AM_SSTATUS_NAME . "</th>";
    echo "<th nowrap>" . _AM_SSTATUS_CURRSTATUS . "</th>";
    echo "<th nowrap>" . _AM_SSTATUS_LASTUPDATED . "</th></tr>";  
    foreach($aServices as $service){
        echo "<tr class='odd'><td nowrap>";
        echo "<a href='" . $service['url'] . "'>" . $service['name'] . "</a></td>";
        echo "<td nowrap><img src='../images/status_icon" . $service['status'] . ".GIF' border='0'></td>";
        echo "<td>" . $service['lastUpdated'] . "</td></tr>";
    }
    echo "</table>";
    echo "<table border='0' width='100%' cellpadding='2' cellspacing='1' class='outer'>";
    echo "<tr class='odd'><td><a href='index.php?op=addService'>" . _AM_SSTATUS_ADDSERVICE_TEXT . "</a></td></tr>";
    echo "</table>";
    echo "<div id='pageNav'>" . $pageNav->renderNav() . "</div>";
    xoops_cp_footer();
}

function viewServiceDetails()   // Same as service.php
{

    xoops_cp_header(); 
    // Get the id of the service
    if(isset($_GET['id'])){
        $id = intval($_GET['id']);
    }
        
    $start = $limit = 0;
    if(isset($_GET['limit'])){
        $limit = intval($_GET['limit']);
    }
    if(isset($_GET['start'])){
        $start = intval($_GET['start']);
    }
    $start = max($start, 0);        // Make sure start is greater than 0
    $limit = max($limit, 10);       // Make sure limit is betwen 10 and 40
    $limit = min($limit, 40);
    
    $hService       =& xoops_getmodulehandler('service', 'sstatus');
    $serviceInfo    =& $hService->get($id);
    $member_handler =& xoops_gethandler('member');
    $memoList       = $serviceInfo->getMemos($limit, $start);
    
    $total          = $serviceInfo->getMemoCount();
    $pageNav        = new XoopsPageNav($total, $limit, $start, "op=viewServiceDetails&id=$id&start");
      
    foreach($memoList as $memo){
        $user =& $member_handler->getUser($memo->getVar('uid'));
        $aMemos[] = array('id'=>$memo->getVar('id'),
                       'serviceid'=>$memo->getVar('serviceid'),
                       'uid'=>$memo->getVar('uid'),
                       'memo'=>$memo->getVar('memo'),
                       'status'=>$memo->getVar('status'),
                       'posted'=>$memo->posted(),
                       'uname'=>$user->getVar('uname'),
                       'userinfo'=>XOOPS_URL . '/userinfo.php?uid=' . $memo->getVar('uid'));
    }
    if(!$lastRec  =& $serviceInfo->getLastMemo()) {
        $sstatus_lastUser = '';
        $userInfoPage = '';
    } else {
        $lastUser =& $member_handler->getUser($lastRec->getVar('uid'));
        $sstatus_lastUser = $lastUser->getVar('uname');
        $userInfoPage = XOOPS_URL . '/userinfo.php?uid=' . $lastRec->getVar('uid');
    }
    // Define variables
    $sstatus_serviceid = $serviceInfo->getVar('id');
    $sstatus_serviceName = $serviceInfo->getVar('name');
    $sstatus_serviceDesc = $serviceInfo->getVar('description');
    $sstatus_serviceStatus = $serviceInfo->getVar('status');
    $sstatus_lastUpdated = $serviceInfo->lastUpdated();
    
    echo "<table width='100%' cellpadding='2' cellspacing='1' class='outer'><tr class='odd'><td>
          <h1>" . _AM_SSTATUS_SERVICE_INFO_TITLE . "</h1></td></tr></table>";
    echo "<table border='1' width='100%' cellpadding='2' cellspacing='1' class='outer'>";
    echo "<tr class='odd'><th nowrap>". _AM_SSTATUS_NAME ."</th>
          <th nowrap>". _AM_SSTATUS_DESC ."</th>
          <th nowrap>". _AM_SSTATUS_CURRSTATUS ."</th>
          <th nowrap>". _AM_SSTATUS_LASTUPDATED ."</th>
          <th nowrap>". _AM_SSTATUS_UPDATED_BY ."</th></tr>";
    echo "<tr class='odd'><td nowrap>". $sstatus_serviceName ."</td>
          <td>". $serviceInfo->getVar('description') ."</td>
          <td nowrap><img src='../images/status_icon". $sstatus_serviceStatus .".GIF' border='0'></td>
          <td>". $sstatus_lastUpdated ."</td>
          <td nowrap><a href='". $userInfoPage ."'>". $sstatus_lastUser ."</a></td></tr></table>";
    echo "<table width='100%' cellpadding='2' cellspacing='1' class='outer'><tr class='odd'><td>
          <h2>" . _AM_SSTATUS_PREVIOUS_INFO . "</h2></td></tr></table>";
    echo "<table border='1' width='100%' cellpadding='2' cellspacing='1' class='outer'>";
    echo "<tr class='odd'><th nowrap>". _AM_SSTATUS_STATUS ."</th>
          <th nowrap>". _AM_SSTATUS_MEMO_TEXT ."</th>
          <th nowrap>". _AM_SSTATUS_POSTED ."</th>
          <th nowrap>". _AM_SSTATUS_UPDATED_BY ."</th></tr>";
if(isset($aMemos)){
    foreach ($aMemos as $memo){
        echo "<tr class='odd'><td nowrap><img src='../images/status_icon". $memo['status'] .".GIF' border='1'></td>
              <td nowrap>". $memo['memo'] ."</td>
              <td nowrap>". $memo['posted'] ."</td>
              <td nowrap><a href='". $memo['userinfo'] ."'>". $memo['uname'] ."</a></td></tr>";
    }
}
    echo "</table>";
    echo "<div id='pageNav'>" . $pageNav->renderNav() . "</div>";
    echo "<table border='0' width='100%' cellpadding='2' cellspacing='1' class='outer'>
          <tr class='odd'><td><a href='index.php?op=modifyService&id=". $serviceInfo->getVar('id') ."'>". _AM_SSTATUS_MODIFY_SERVICE ."</a></td></tr>
          <tr class='odd'><td><a href='index.php?op=addMemo&id=". $sstatus_serviceid ."'>". _AM_SSTATUS_ADDMEMO_TEXT ."</a></td></tr>
          </table>";
xoops_cp_footer();
}

function groupPerm()    // Shows the group permissions page
{
    global $xoopsModule;
    include_once XOOPS_ROOT_PATH.'/class/xoopsform/grouppermform.php';
    $module_id = $xoopsModule->getVar('mid');
    $title_of_form = _AM_SSTATUS_GROUP_PERM_TITLE;
    $perm_name = _AM_SSTATUS_GROUP_PERM_NAME;
    $perm_desc = _AM_SSTATUS_GROUP_PERM_DESC;
    
    $hService =& xoops_getmodulehandler('service', 'sstatus');
    $crit = new Criteria('','');
    $crit->setOrder('DESC');
    $crit->setSort('lastUpdated');
    $serviceInfo = $hService->getObjects($crit); 
    
    $item_list = array(_SEC_ADD_SERVICE => _SSTATUS_CATEGORY1, _SEC_DELETE_SERVICE => _SSTATUS_CATEGORY2,
                       _SEC_DELETE_MEMO => _SSTATUS_CATEGORY3, _SEC_MODIFY_SERVICE => _SSTATUS_CATEGORY4,
                       _SEC_MODIFY_MEMO => _SSTATUS_CATEGORY5, _SEC_ADD_MEMO => _SSTATUS_CATEGORY6);
        
    $form = new XoopsGroupPermForm($title_of_form, $module_id, $perm_name, $perm_desc);
    foreach($item_list as $item_id => $item_name){
        $form->addItem($item_id, $item_name);
    }
    xoops_cp_header();
    echo $form->render();
    xoops_cp_footer();
}

function sstatus_default()      // Displays all of the menu items available
{
    global $xoopsModule;
    $module_id = $xoopsModule->getVar('mid');
    xoops_cp_header();
    
    // Get the id of the service
    if(isset($_GET['id'])){
        $id = intval($_GET['id']);
    }
    
    echo "<h4>" . _AM_SSTATUS_ADMIN_TITLE . "</h4>";
    echo "<table width='100%' border='0' cellspacing='1' class='outer'><tr class='odd'><td>";
    echo "<ul><li><a href='index.php?op=addService'>" . _AM_SSTATUS_ADDSERVICE_TEXT . "</a></li>";
    if(isset($id)){
        echo "<li><a href='index.php?op=modifyService&id=". $id."'>" . _AM_SSTATUS_MODIFYSERVICE_TEXT . "</a></li>";
        echo "<li><a href='index.php?op=addMemo&id=". $id."'>" . _AM_SSTATUS_ADDMEMO_TEXT . "</a></li>";
        echo "<li><a href='index.php?op=viewServiceDetails&id=". $id."'>" . _AM_SSTATUS_VIEWSERVICEDETAILS_TEXT . "</a></li>";
    }
    echo "<li><a href='index.php?op=viewServices'>" . _AM_SSTATUS_VIEWSERVICES_TEXT . "</a></li>";
    echo "<li><a href='index.php?op=groupperm'>" . _AM_SSTATUS_GROUP_PERM . "</a></li>";
    echo "<li><a href='". XOOPS_URL ."/modules/system/admin.php?fct=preferences&op=showmod&mod=". $module_id ."'>". _AM_SSTATUS_MENU_PREFERENCES ."</a></li>";
    echo "</ul></td></tr></table>";
    xoops_cp_footer();
}
?>