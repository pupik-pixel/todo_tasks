<?php
require_once('DatabaseData.php');

$databaseData = new DatabaseData();

if ($_POST['exitTheApplication']) {
    $aAuthData = $databaseData->exitUser();
}

$aAuthData = $databaseData->isAuthenticationStatus();
if ($aAuthData['status']) {
    if ($_POST['update']) {
        $databaseData->updateDataForTask();
    }
    if ($_POST['insert']) {
         $databaseData->insertDataForTask();
    }
    echo json_encode([
        'isAuthentication' => $aAuthData,
        'tasksData' => $databaseData->getTasksData(),
        'responsibles' => $databaseData->getAllResponsibles(),
        'priority' => $databaseData->getAllPriority(),
        'status' => $databaseData->getAllStatus()
    ]);
}
else {
    echo json_encode([
        'isAuthentication' =>  $aAuthData
    ]);
}
exit();
?>