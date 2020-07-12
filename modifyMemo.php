<?php
//$Id: modifyMemo.php,v 1.2 2004/08/30 21:55:20 ackbarr Exp $
require_once('header.php');

// Get the id of the service
if(isset($_GET['id'])){
    $memoid = intval($_GET['id']);
}
// Check user permissions
global $xoopsModule;
$perm_name = _SSTATUS_GROUP_PERM_NAME;
if(isset($_GET['category_id'])){
    $perm_itemid = intval($_GET['category_id']);
} else {
    $perm_itemid = 0;
}
if($xoopsUser){             // Get user groups
    $groups = $xoopsUser->getGroups();
} else {
    $groups = XOOPS_GROUP_ANONYMOUS;
}
$module_id = $xoopsModule->getVar('mid');
$gperm_handler =& xoops_gethandler('groupperm');

// Make sure user doesn't manually type URL
if($gperm_handler->checkRight($perm_name, _SEC_MODIFY_MEMO, $groups, $module_id)){
} else {
    $message = _SSTATUS_MESSAGE_MODIFY_MEMO_PERM_ERROR;
    redirect_header("index.php", 3, $message);
}

if(isset($_POST['modifiedMemo']))      // If the memo was modified
{   
    $hMemo       =& xoops_getmodulehandler('memo', 'sstatus');  // New memo object
    $memoInfo    =& $hMemo->get($memoid);   
    $serviceid = $memoInfo->getVar('serviceid');
    $hService    =& xoops_getmodulehandler('service', 'sstatus');   // New service object
    $serviceInfo =& $hService->get($serviceid);
    //$member_handler =& xoops_gethandler('member');
    //$user =& $member_handler->getUser($memoInfo->getVar('uid'));
    
    
    $memoInfo->setVar('status', $_POST['newStatus']);  // Fill memo object with vars from POST
    global $xoopsUser;
    $userName = $xoopsUser->getVar('uname');
    $time = $memoInfo->getVar('posted');
    $memoInfo->setVar('memo', $_POST['newMemo'] . "\n" . sprintf(_SSTATUS_MODIFIED_BY, $userName, formatTimestamp($time)));

    if($hMemo->insert($memoInfo)) {     // Check for memo to be inserted to DB
        // Notification
        $notification_handler =& xoops_gethandler('notification');
        // Notification array
        $tags[] = array();
        $tags['SERVICE_NAME'] = $serviceInfo->getVar('name');
        $tags['SERVICE_MODIFIED_URL']= XOOPS_URL . '/modules/sstatus/service.php?id=' . $serviceid;
        
        // Trigger events for notification
        $notification_handler->triggerEvent('global', 0, 'modified_service_status', $tags);
        $notification_handler->triggerEvent('service', $serviceid, 'modified_this_service_status', $tags);
        
        $message = _SSTATUS_MESSAGE_MODIFY_MEMO;    // Display message on successful update
    } else {
        $message = _SSTATUS_MESSAGE_MODIFY_MEMO_ERROR;// Display message on unsuccessful update
    }
    redirect_header("modifyService.php?id=" . $serviceid, 3, $message);
} elseif (isset($_POST['deleteMemo'])){    // If the memo was deleted
    $hMemo       =& xoops_getmodulehandler('memo', 'sstatus');  // New memo object
    $memoInfo    =& $hMemo->get($memoid);
    $serviceid = $memoInfo->getVar('serviceid');
    if($hMemo->delete(&$memoInfo)){
        $message = _SSTATUS_MESSAGE_DELETE_MEMO; // Display message on successful delete
    } else {
        $message = _SSTATUS_MESSAGE_DELETE_MEMO_ERROR;          // Display message on unsuccessful delete
    }
    redirect_header("modifyService.php?id=" . $serviceid, 3, $message);
} else {
    // Always set main template before including the header
    $xoopsOption['template_main'] = 'sstatus_modifyMemo.html';
    include(XOOPS_ROOT_PATH . '/header.php');
    
    $hMemo       =& xoops_getmodulehandler('memo', 'sstatus');  // New memo class
    $memoInfo    =& $hMemo->get($memoid);
        
    // Check for delete permission
    if($gperm_handler->checkRight($perm_name, _SEC_DELETE_MEMO, $groups, $module_id)){
        $xoopsTpl->assign('sstatus_showDelete', True);  // Used to show delete button on template
    } else {
        $xoopsTpl->assign('sstatus_showDelete', False); // Used to remove delete button on template
    }
    
    // Smarty variables
    $xoopsTpl->assign('sstatus_modifyMemoTitle', _SSTATUS_MODIFY_MEMO);
    $xoopsTpl->assign('sstatus_submit', _SSTATUS_BUTTON_SUBMIT);
    $xoopsTpl->assign('sstatus_reset', _SSTATUS_BUTTON_RESET);
    $xoopsTpl->assign('sstatus_deleteMemo', _SSTATUS_BUTTON_DELETEMEMO);
    $xoopsTpl->assign('sstatus_id', $memoid);
    //$xoopsTpl->assign('sstatus_serviceid', $serviceid);
    $xoopsTpl->assign('sstatus_oldStatus', $memoInfo->getVar('status'));
    $xoopsTpl->assign('sstatus_oldMemo', $memoInfo->getVar('memo'));
    $xoopsTpl->assign('sstatus_memoStatus', _SSTATUS_STATUS);
    $xoopsTpl->assign('sstatus_memoText', _SSTATUS_MEMO_TEXT);
    $xoopsTpl->assign('sstatus_select_id', array(1,2,3));
    $xoopsTpl->assign('sstatus_options', array(_SSTATUS_OPTION1, _SSTATUS_OPTION2, _SSTATUS_OPTION3));
    
    include(XOOPS_ROOT_PATH . '/footer.php');
}
?>