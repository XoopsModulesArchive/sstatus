<?php
//$Id: sstatus_blocks.php,v 1.2 2004/08/30 21:55:20 ackbarr Exp $
include_once(XOOPS_ROOT_PATH . '/modules/sstatus/language/english/main.php');

function b_sstatus_overview_show()
{
    include_once(XOOPS_ROOT_PATH . '/class/pagenav.php');
    $block = array();
    $hService =& xoops_getmodulehandler('service', 'sstatus');
    
    $start = $limit = 0;
    if(isset($_GET['limit'])){
        $limit = intval($_GET['limit']);
    }
    if(isset($_GET['start'])){
        $start = intval($_GET['start']);
    }
    // Make sure start is greater than 0
    $start = max($start, 0);
    
    // Make sure limit is between 5 and 20
    $limit = max($limit, 5);
    $limit = min($limit, 20);
    
    $crit = new Criteria('','');
    $crit->setOrder('DESC');
    $crit->setSort('lastUpdated');
    $crit->setLimit($limit);
    $crit->setStart($start);
    $total = $hService->getCount($crit);
    $serviceInfo = $hService->getObjects($crit);
    
    $statusText = array(1 => _SSTATUS_OPTION1, 2 => _SSTATUS_OPTION2, 3 => _SSTATUS_OPTION3);
    
    foreach($serviceInfo as $service) {
    $block['services'][] = array('id'=>$service->getVar('id'),
                    'name'=>$service->getVar('name'),
                    'status'=>$service->getVar('status'),
                    'lastUpdated'=>$service->lastUpdated(),
                    'url'=>XOOPS_URL . '/modules/sstatus/service.php?id=' . $service->getVar('id'),
                    'truncName'=>xoops_substr($service->getVar('name'),0,15),
                    'status_title'=>$statusText[$service->getVar('status')]);
    }
    $block['imagePath'] = XOOPS_URL . '/modules/sstatus/images/status_icon';
    
   return $block;
}

function b_sstatus_admin_show()
{
    global $xoopsUser, $xoopsModule, $_SERVER, $_GET;
    $block = array();
	// Get the id of the service
    if(isset($_GET['id'])){
        $id = intval($_GET['id']);
    }
    // Check user permissions
    $perm_name = _SSTATUS_GROUP_PERM_NAME;
    if(isset($_GET['category_id'])){
        $perm_itemid = intval($_GET['category_id']);
    } else {
        $perm_itemid = 0;
    }
    if($xoopsUser){
        $groups = $xoopsUser->getGroups();
    } else {
        $groups = XOOPS_GROUP_ANONYMOUS;
    }
    
    $module_id = $xoopsModule->getVar('mid');
    $gperm_handler =& xoops_gethandler('groupperm');

	$currPage = $_SERVER['PHP_SELF'];
	$moduleDir = substr(strrchr($currPage, "/"), 1);
	switch($moduleDir){
	    case 'addMemo.php':
	        if($gperm_handler->checkRight($perm_name, _SEC_ADD_SERVICE, $groups, $module_id)){
	            $block['links'][1]['url'] = XOOPS_URL . '/modules/sstatus/addService.php';
	            $block['links'][1]['name'] = _MB_SSTATUS_ADDSERVICE;
	        }
	        $block['links'][3]['url'] = XOOPS_URL . '/modules/sstatus/index.php';
            $block['links'][3]['name'] = _MB_SSTATUS_MAIN;
	        $block['links'][4]['url'] = XOOPS_URL . '/modules/sstatus/service.php?id=' . $id;
            $block['links'][4]['name'] = _MB_SSTATUS_SERVICE;
            break;
            
        case 'addService.php':
            if($gperm_handler->checkRight($perm_name, _SEC_ADD_SERVICE, $groups, $module_id)){
	            $block['links'][1]['url'] = XOOPS_URL . '/modules/sstatus/addService.php';
	            $block['links'][1]['name'] = _MB_SSTATUS_ADDSERVICE;
	        }
            $block['links'][3]['url'] = XOOPS_URL . '/modules/sstatus/index.php';
            $block['links'][3]['name'] = _MB_SSTATUS_MAIN;
            break;
            
        case 'modifyService.php':
            if($gperm_handler->checkRight($perm_name, _SEC_ADD_MEMO, $groups, $module_id)){
                $block['links'][0]['url'] = XOOPS_URL . '/modules/sstatus/addMemo.php?id=' . $id;
                $block['links'][0]['name'] = _MB_SSTATUS_ADDMEMO; 
            }
            if($gperm_handler->checkRight($perm_name, _SEC_ADD_SERVICE, $groups, $module_id)){
	            $block['links'][1]['url'] = XOOPS_URL . '/modules/sstatus/addService.php';
	            $block['links'][1]['name'] = _MB_SSTATUS_ADDSERVICE;
	        }
            $block['links'][3]['url'] = XOOPS_URL . '/modules/sstatus/index.php';
            $block['links'][3]['name'] = _MB_SSTATUS_MAIN;
            $block['links'][4]['url'] = XOOPS_URL . '/modules/sstatus/service.php?id=' . $id;
            $block['links'][4]['name'] = _MB_SSTATUS_SERVICE;
            break;
	    
	    case 'index.php':
	        if($gperm_handler->checkRight($perm_name, _SEC_ADD_SERVICE, $groups, $module_id)){
	            $block['links'][1]['url'] = XOOPS_URL . '/modules/sstatus/addService.php';
	            $block['links'][1]['name'] = _MB_SSTATUS_ADDSERVICE;
	        }
            $block['links'][3]['url'] = XOOPS_URL . '/modules/sstatus/index.php';
            $block['links'][3]['name'] = _MB_SSTATUS_MAIN;
            break;
            
        case 'service.php':
            if($gperm_handler->checkRight($perm_name, _SEC_ADD_MEMO, $groups, $module_id)){
                $block['links'][0]['url'] = XOOPS_URL . '/modules/sstatus/addMemo.php?id=' . $id;
                $block['links'][0]['name'] = _MB_SSTATUS_ADDMEMO; 
            }
            if($gperm_handler->checkRight($perm_name, _SEC_ADD_SERVICE, $groups, $module_id)){
	            $block['links'][1]['url'] = XOOPS_URL . '/modules/sstatus/addService.php';
	            $block['links'][1]['name'] = _MB_SSTATUS_ADDSERVICE;
	        }
	        if($gperm_handler->checkRight($perm_name, _SEC_MODIFY_SERVICE, $groups, $module_id)){
                $block['links'][2]['url'] = XOOPS_URL . '/modules/sstatus/modifyService.php?id=' . $id;
                $block['links'][2]['name'] = _MB_SSTATUS_MODIFYSERVICE;
            }
            $block['links'][3]['url'] = XOOPS_URL . '/modules/sstatus/index.php';
            $block['links'][3]['name'] = _MB_SSTATUS_MAIN;
            break;
            
        default:   // Keep all blocks together for reference
        	//$block['links'][0]['url'] = XOOPS_URL . '/modules/sstatus/addMemo.php?id=' . $id;
            //$block['links'][0]['name'] = _MB_SSTATUS_ADDMEMO; 
            if($gperm_handler->checkRight($perm_name, _SEC_ADD_SERVICE, $groups, $module_id)){
                $block['links'][1]['url'] = XOOPS_URL . '/modules/sstatus/addService.php';
                $block['links'][1]['name'] = _MB_SSTATUS_ADDSERVICE;
            }
            //$block['links'][2]['url'] = XOOPS_URL . '/modules/sstatus/modifyService.php?id=' . $id;
            //$block['links'][2]['name'] = _MB_SSTATUS_MODIFYSERVICE;
            $block['links'][3]['url'] = XOOPS_URL . '/modules/sstatus/index.php';
            $block['links'][3]['name'] = _MB_SSTATUS_MAIN;
            //$block['links'][4]['url'] = XOOPS_URL . '/modules/sstatus/service.php';
            //$block['links'][4]['name'] = _MB_SSTATUS_SERVICE;
            break;
    }
    return $block;
}
?>