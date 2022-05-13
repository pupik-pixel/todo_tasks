<?php
require_once('DatabaseData.php');
if (!DatabaseData::isAuthenticationSuccessful()) {
    header("Location: auth.php");
    exit;
}
$aTasksData = DatabaseData::getTasksData();
print_r($aTasksData);
?>

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
            <td><?= $aTaskItem['name'] . ' ' . $aTaskItem['surname'] ?></td>
            <td><?= $aTaskItem['status'] ?></td>
        </tr>

        <?
    }
    ?>

    </tbody>
</table>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">