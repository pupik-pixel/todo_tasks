<?php
require_once('DatabaseData.php');

$aTasksData = DatabaseData::getTasksData();
$aResponsibleForTasks = DatabaseData::getAllResponsibles();
if (DatabaseData::isAuthenticationStatus()) {
    if ($_POST['update']) {
        DatabaseData::updateDataForTask();
    }

    echo json_encode([
        'isAuthentication' =>  DatabaseData::isAuthenticationStatus(),
        'tasksData' => DatabaseData::getTasksData(),
        'responsibles' => DatabaseData::getAllResponsibles(),
        'priority' => DatabaseData::getAllPriority(),
        'status' => DatabaseData::getAllStatus()
    ]);
}
else {
    echo json_encode([
        'isAuthentication' =>  DatabaseData::isAuthenticationStatus()
    ]);
}
?>