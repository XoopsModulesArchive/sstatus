<?php
//$Id: menu.php,v 1.2 2004/08/30 21:55:20 ackbarr Exp $


// Get the id of the service
    if(isset($_GET['id'])){
        $id = intval($_GET['id']);
    }

$adminmenu[1]['title']  = _MI_SSTATUS_MENU_NEWSERVICE;           // Create a new service
$adminmenu[1]['link']   = "admin/index.php?op=addService";
if(isset($id)){     // Needs to have the id set to be able to view these pages
    $adminmenu[2]['title']  = _MI_SSTATUS_MENU_MODIFYSERVICE;        // Modify a service
    $adminmenu[2]['link']   = "admin/index.php?op=modifyService&id=". $id;
    $adminmenu[3]['title']  = _MI_SSTATUS_MENU_NEWMEMO;               // Add new memo
    $adminmenu[3]['link']   = "admin/index.php?op=addMemo&id=". $id;
    $adminmenu[4]['title']  = _MI_SSTATUS_MENU_VIEWSERVICEDETAILS;   // Display single service details
    $adminmenu[4]['link']   = "admin/index.php?op=viewServiceDetails&id=". $id;
}
$adminmenu[5]['title']  = _MI_SSTATUS_MENU_VIEWSERVICES;         // Display all services
$adminmenu[5]['link']   = "admin/index.php?op=viewServices";
$adminmenu[6]['title']  = _MI_SSTATUS_MENU_GROUP_PERM;           // Display group permissions
$adminmenu[6]['link']   = "admin/index.php?op=groupperm";



?>