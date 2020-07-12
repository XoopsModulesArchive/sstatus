<?php
require_once('header.php');

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

// Get user's groups
if($xoopsUser){
    $groups = $xoopsUser->getGroups();
} else {
    $groups = XOOPS_GROUP_ANONYMOUS;
}
$module_id = $xoopsModule->getVar('mid');
$gperm_handler =& xoops_gethandler('groupperm');

// Make sure user doesn't manually type URL
if($gperm_handler->checkRight($perm_name, _SEC_ADD_MEMO, $groups, $module_id)){
} else {
    $message = _SSTATUS_MESSAGE_ADD_MEMO_PERM_ERROR;
    redirect_header("index.php", 3, $message);
}

if(!isset($_POST['newMemoSubmitted']))     // If a new memo is submitted
{
    // Always set main template before including the header
    $xoopsOption['template_main'] = 'sstatus_memo.html';
    include(XOOPS_ROOT_PATH . '/header.php');
    
    // Smarty variables
    $xoopsTpl->assign('sstatus_submit', _SSTATUS_BUTTON_SUBMIT);
    $xoopsTpl->assign('sstatus_reset', _SSTATUS_BUTTON_RESET);
    $xoopsTpl->assign('sstatus_option1', _SSTATUS_OPTION1);
    $xoopsTpl->assign('sstatus_option2', _SSTATUS_OPTION2);
    $xoopsTpl->assign('sstatus_option3', _SSTATUS_OPTION3);
    $xoopsTpl->assign('sstatus_status', _SSTATUS_STATUS);
    $xoopsTpl->assign('sstatus_memoText', _SSTATUS_MEMO_TEXT);
    $xoopsTpl->assign('sstatus_addMemo', _SSTATUS_ADD_MEMO);
    $xoopsTpl->assign('sstatus_serviceid', $id);

include(XOOPS_ROOT_PATH . '/footer.php');
} else {
    $hService =& xoops_getmodulehandler('service', 'sstatus');      // Create service object
    $currService =& $hService->get($id);
    
    if($_POST['newMemo'] == ''){       // Make user type something in for a memo
        $message = _SSTATUS_MESSAGE_ADD_MEMO_BLANK;
        redirect_header("addMemo.php?id=" . $id, 3, $message);
    }
    // Make a new memo from the variables in $_POST
    $memo =& $currService->createMemo($_POST['newMemo'], $_POST['newStatus']);
    if($currService->insertMemo($memo)){    // Make sure the new memo gets inserted into the table
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
        $message = _SSTATUS_MESSAGE_ADD_MEMO;    // Display message for successful insertion of new memo
    } else {
        $message = _SSTATUS_MESSAGE_ADD_MEMO_ERROR;    // Unsuccessful insertion of new memo
    }
    //redirect_header("index.php", 3, $message);
    redirect_header("service.php?id=" . $id, 3, $message);
}
?>