<?php
require_once('DatabaseData.php');
if (!DatabaseData::isAuthenticationSuccessful()) {
    header("Location: auth.php");
    exit;
}
$aTasksData = DatabaseData::getTasksData();
$aResponsibleForTasks = DatabaseData::getAllResponsibles();
print_r($_POST['timeForFilterDate']);
?>

<div class = "task-table-wrap">
<table class="table">
    <thead>
    <tr>
        <th scope="col">Заголовок</th>
        <th scope="col">Приоритет</th>
        <th scope="col">Дата окончания</th>
        <th scope="col">Ответственный</th>
        <th scope="col">Статус</th>
    </tr>
    </thead>
    <tbody>


    <?
    foreach ($aTasksData as $aTaskItem) {
        ?>

        <tr
            <?
            $oCurrentDate = new DateTime(date('Y-m-d', time()));
            $oExpirationDate = new DateTime($aTaskItem['expiration_date']);
            $iCountDayDifference = $oCurrentDate->diff($oExpirationDate)->format('%R%a');
            if ($iCountDayDifference < 0 && ($aTaskItem['status'] == 'к выполнению' || $aTaskItem['status'] == 'выполняется')) {
                ?>
                class="table-danger"
            <?
            } elseif ($aTaskItem['status'] == 'выполнена') {
                ?>
                class="table-success"
            <?
            } else {
                ?>
                class="table-light"
            <?
            }
            ?>
        >
            <td><?= $aTaskItem['caption'] ?></td>
            <td><?= $aTaskItem['priority'] ?></td>
            <td><?= $aTaskItem['expiration_date'] ?></td>
            <td><?= $aTaskItem['surname'] . ' ' . $aTaskItem['name'].' '.$aTaskItem['patronymic_name'] ?></td>
            <td><?= $aTaskItem['status'] ?></td>
        </tr>

        <?
    }
    ?>

    </tbody>
</table>
    <div>
        <div class="form-check">
            <input class="form-check-input date-check-input-js" type="checkbox" value="" id="flexCheckDefault">
            <label class="form-check-label" for="flexCheckDefault">
                Фильтровать задачи по дате завершения:
            </label>
        </div>
        <select class="form-select date-select-js" aria-label="Default select example" disabled>
            <option selected value="Cегодня">Cегодня</option>
            <option value="В течении этой недели">В течении этой недели</option>
            <option value="Все задачи">Все задачи</option>
        </select>
    </div>
    <div>
        <div class="form-check">
            <input class="form-check-input responsible-check-input-js" type="checkbox" value="" id="flexCheckDefault">
            <label class="form-check-label" for="flexCheckDefault">
                Фильтровать задачи по ответственным:
            </label>
        </div>
        <select class="form-select responsible-select-js" aria-label="Default select example" disabled>
            <?
                foreach ($aResponsibleForTasks as $aResponsible) {
                    $sName = $aResponsible['surname'].' '.$aResponsible['name'].' '.$aResponsible['patronymic_name'];
                    ?>
                    <option selected value="<?= $sName ?>"><?= $sName ?></option>
                <?}
            ?>
        </select>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<link href="styleForTasks.css" rel="stylesheet">
<script src="scriptForTasks.js"></script>