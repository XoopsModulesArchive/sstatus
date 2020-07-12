<?php
//$Id: modifyService.php,v 1.2 2004/08/30 21:55:20 ackbarr Exp $
require_once('header.php');
include_once(XOOPS_ROOT_PATH . '/class/pagenav.php'); 

// Get the id of the service
if(isset($_GET['id'])){
    $id = intval($_GET['id']);
}
// Check user permissions
global $xoopsModule;
$perm_name = _SSTATUS_GROUP_PERM_NAME;
if(isset($_GET['category_id'])){
    $perm_itemid = intval($_GET['category_id']);
} else {
    $perm_itemid = 0;
}
if($xoopsUser){
    $groups = $xoopsUser->getGroups();  // Get user's groups
} else {
    $groups = XOOPS_GROUP_ANONYMOUS;    // Not logged in
}
$module_id = $xoopsModule->getVar('mid');   // Get the module_id
$gperm_handler =& xoops_gethandler('groupperm');    // New group permissions object

// Make sure user doesn't manually type URL
if($gperm_handler->checkRight($perm_name, _SEC_MODIFY_SERVICE, $groups, $module_id)){
} else {
    $message = _SSTATUS_MESSAGE_MODIFY_SERVICE_PERM_ERROR;
    redirect_header("index.php", 3, $message);
}

if(isset($_POST['modified'])){ // If service is modified
    $hService    =& xoops_getmodulehandler('service', 'sstatus');   // New service object
    $serviceInfo =& $hService->get($id);
    $serviceInfo->setVar('name', $_POST['newServiceName']);                    // Set vars for
    $serviceInfo->setVar('description', $_POST['newServiceDescription']);      // new service object
    if($hService->insert($serviceInfo)) {   // Check to see if updated service was inserted into DB
        // Notification
        $notification_handler =& xoops_gethandler('notification');
        // Notification array
        
        $tags[] = array();
        $tags['SERVICE_NAME'] = $serviceInfo->getVar('name');
        $tags['SERVICE_MODIFIED_URL'] = XOOPS_URL . '/modules/sstatus/service.php?id=' . $id;
        
        // Trigger notification events (Global and individual)
        $notification_handler->triggerEvent('global', 0, 'modified_service_status', $tags);
        $notification_handler->triggerEvent('service', $id, 'modified_this_service_status', $tags);
        
        $message = _SSTATUS_MESSAGE_MODIFY_SERVICE;  // Display message on successful update
    } else {
        $message = _SSTATUS_MESSAGE_MODIFY_SERVICE_ERROR;  // Display message on unsuccessful update
    }
    redirect_header("service.php?id=" . $id, 3, $message);
} elseif (isset($_POST['deleteService'])){// If service is deleted
    $hService    =& xoops_getmodulehandler('service', 'sstatus');   // New service object
    $serviceInfo =& $hService->get($id);
       
    if($hService->delete(&$serviceInfo)){   // Check to see if service is deleted
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
        
        $message = _SSTATUS_MESSAGE_DELETE_SERVICE;         // Display on successful delete
    } else {
        $message = _SSTATUS_MESSAGE_DELETE_SERVICE_ERROR;   // Display on unsuccessful delete
    }
    redirect_header("index.php", 3, $message);
} else {        
    // Always set main template before including the header
    $xoopsOption['template_main'] = 'sstatus_edit.html';
    include(XOOPS_ROOT_PATH . '/header.php');
    
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
    $limit = max($limit, $sstatus_max);
    $limit = min($limit, 40);
    
    $hService       =& xoops_getmodulehandler('service', 'sstatus');    // New service object
    $serviceInfo    =& $hService->get($id);
    $member_handler =& xoops_gethandler('member');                      // New member object
    $memoList       = $serviceInfo->getMemos($limit, $start);
    $total          = $serviceInfo->getMemoCount();                     // Count memos for pagenav total
    $pageNav        = new XoopsPageNav($total, $limit, $start, "id=$id&start");         // create new pagenav object
    
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
                       'memoInfo'=> 'modifyMemo.php?id=' . $memo->getVar('id'));
    }
    if(!$lastRec  =& $serviceInfo->getLastMemo()) {     // If there are no memos
    if(isset($memo)){
        $lastUser = $memo->getVar('uname');                 // get the user that last updated
    }
        
        $xoopsTpl->assign('sstatus_userInfo', '');      // Set vars to empty
        $xoopsTpl->assign('sstatus_lastUser', '');      // strings
    } else {
        $lastUser =& $member_handler->getUser($lastRec->getVar('uid'));
        $xoopsTpl->assign('sstatus_userInfo', XOOPS_URL . '/userinfo.php?uid=' . $lastRec->getVar('uid'));
        $xoopsTpl->assign('sstatus_lastUser', $lastUser->getVar('uname'));
    }    
    
    
    if($gperm_handler->checkRight($perm_name, _SEC_DELETE_SERVICE, $groups, $module_id)){
        $xoopsTpl->assign('sstatus_showDelete', True);      // Show delete button on template - has permission
    } else {
        $xoopsTpl->assign('sstatus_showDelete', False);     // Remove delete button from template - no permission
    }
    
    // Check for permission to modify memo
    if($gperm_handler->checkRight($perm_name, _SEC_MODIFY_MEMO, $groups, $module_id)){
        $xoopsTpl->assign('sstatus_showUrl', True);         // Show the link to edit memos - has permission
    } else {
        $xoopsTpl->assign('sstatus_showUrl', False);        // Remove link to edit memos - no permission
    }
    
    // Smarty variables
    $xoopsTpl->assign('sstatus_modifyServiceTitle', _SSTATUS_MODIFY_SERVICE);
    $xoopsTpl->assign('sstatus_submit', _SSTATUS_BUTTON_SUBMIT);
    $xoopsTpl->assign('sstatus_reset', _SSTATUS_BUTTON_RESET);
    $xoopsTpl->assign('sstatus_serviceName', _SSTATUS_NAME);
    $xoopsTpl->assign('sstatus_serviceDesc', _SSTATUS_DESC);
    $xoopsTpl->assign('sstatus_oldName', $serviceInfo->getVar('name'));
    $xoopsTpl->assign('sstatus_oldDesc', $serviceInfo->getVar('description'));
    $xoopsTpl->assign('sstatus_serviceid', $id);
    if(isset($aMemos)){
        $xoopsTpl->assign('sstatus_memoList', $aMemos);
    }
    $xoopsTpl->assign('sstatus_previousInfo', _SSTATUS_PREVIOUS_INFO);
    $xoopsTpl->assign('sstatus_pageNav', $pageNav->renderNav());
    $xoopsTpl->assign('sstatus_updatedBy', _SSTATUS_UPDATED_BY);
    $xoopsTpl->assign('sstatus_posted', _SSTATUS_POSTED);
    $xoopsTpl->assign('sstatus_status', _SSTATUS_STATUS);
    $xoopsTpl->assign('sstatus_memoText', _SSTATUS_MEMO_TEXT);
    $xoopsTpl->assign('sstatus_deleteService', _SSTATUS_BUTTON_DELETESERVICE);

    include(XOOPS_ROOT_PATH . '/footer.php');
}
?>