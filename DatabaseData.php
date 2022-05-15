<?php

class DatabaseData
{

    private $oConnection;

    function __construct() {
        $this->oConnection = new mysqli('localhost', 'root', '', 'todo_list_task');
    }

    public function closeConnection() {
        $this->oConnection->close();
    }

    public function isAuthenticationStatus()
    {
        $oQueryWithLogin = $this->oConnection->query(
            'select session_id,
                    id,
                    concat (surname, \' \', name, \' \', patronymic_name) as name,
                    supervisor
                    from users 
                    where session_id = \'' . $_COOKIE['session'] . '\'');
        if ($aResult = $oQueryWithLogin->fetch_assoc()) {
            return [
                'status' => true,
                'userId' => $aResult['id'],
                'userName' => $aResult['name']
            ];
        } else {
            return [
                'status' => false
            ];
        }
    }

    public function getTasksData()
    {
        $aResult = [];
        $sQuery = 'select caption, priority.value as priority, 
                expiration_date, 
                concat(r.surname, 
                    \' \',
                r.name,
                    \' \',
                r.patronymic_name) as rName,
                status.value as status,
                description,
                date_of_creation,
                update_date,
                creator,
                responsible,
                tasks.id,
                tasks.priority as priorityId,
                tasks.status as statusId,
                concat(c.surname, 
                    \' \',
                c.name,
                    \' \',
                c.patronymic_name) as cName
                from tasks 
                join status on tasks.status = status.id
                join priority on tasks.priority = priority.id
                join users as r on tasks.responsible = r.id
                join users as c on tasks.creator = c.id';
        if (!empty($_POST['filterData'])) {
            switch ($_POST['filterData']) {
                case 'today':
                {
                    $oCurrentDate = new DateTime(date('Y-m-d'));
                    $sQuery = $sQuery . 'where expiration_date=\'' . $oCurrentDate->format('Y-m-d') . '\'';
                    break;
                }
                case 'duringThisWeek':
                {
                    $oNextWeekDate = new DateTime(date('Y-m-d'));
                    $oNextWeekDate->add(new DateInterval('P1W'));
                    $sQuery = $sQuery . 'where expiration_date<\'' . $oNextWeekDate->format('Y-m-d') . '\'';
                    break;
                }
            }
        }
        if (!empty($_POST['sortByUpdateDate'])) {
            $sQuery = $sQuery . ' order by update_date asc';
        } else {
            $sQuery = $sQuery . ' order by expiration_date asc';
        }
        $oQueryWithLogin = $this->oConnection->query($sQuery);
        while ($aResultRow = $oQueryWithLogin->fetch_assoc()) {
            $aResult[] = $aResultRow;
        }
        return $aResult;
    }

    public function getAllResponsibles()
    {
        $aResult = [];
        $sQuery = 'select users.id,
                concat(users.surname, 
                    \' \',
                users.name,
                    \' \',
                users.patronymic_name) as name
                from tasks
                join users on tasks.responsible = users.id
                where tasks.responsible is not null 
                ';
        if (!empty($_POST['filterResponsible'])) {
            $sQuery = $sQuery . 'and users.id=' . $_POST['filterResponsible'];
        }
        $sQuery = $sQuery . ' group by tasks.responsible';
        $oQueryWithLogin = $this->oConnection->query($sQuery);
        while ($aResultRow = $oQueryWithLogin->fetch_assoc()) {
            $aResult[] = $aResultRow;
        }
        return $aResult;
    }

    public function getAllPriority()
    {
        $aResult = [];
        $sQuery = 'select * from priority';
        $oQueryWithLogin = $this->oConnection->query($sQuery);
        while ($aResultRow = $oQueryWithLogin->fetch_assoc()) {
            $aResult[] = $aResultRow;
        }
        return $aResult;
    }

    public function getAllStatus()
    {
        $aResult = [];
        $sQuery = 'select * from status';
        $oQueryWithLogin = $this->oConnection->query($sQuery);
        while ($aResultRow = $oQueryWithLogin->fetch_assoc()) {
            $aResult[] = $aResultRow;
        }
        return $aResult;
    }

    public function updateDataForTask()
    {
        $oCurrentDate = new DateTime(date('Y-m-d', time()));
        $sCurrentDate = $oCurrentDate->format('Y-m-d');
        $sQuery = 'update tasks set caption =\'' . $_POST['caption'] . '\',' .
            'description = \'' . $_POST['description'] . '\',' .
            'expiration_date = \'' . $_POST['expiration_date'] . '\',' .
            'update_date = \'' . $sCurrentDate . '\',' .
            'priority = \'' . $_POST['priority'] . '\',' .
            'status = \'' . $_POST['status'] . '\',' .
            'responsible = \'' . $_POST['responsible'] . '\'' .
            'where tasks.id = ' . $_POST['id'];
        $this->oConnection->query($sQuery);
    }

    public function insertDataForTask()
    {
        if (empty($_POST['update_date'])) $sUpdateDate = 'null';
        else $sUpdateDate = '\'' . $_POST['update_date'] . '\'';
        $sQuery = 'insert into tasks values (' .
            '\'' . $_POST['caption'] . '\', ' .
            '\'' . $_POST['description'] . '\', ' .
            '\'' . $_POST['date_of_creation'] . '\', ' .
            '\'' . $_POST['expiration_date'] . '\', ' .
            $sUpdateDate . ', ' .
            '\'' . $_POST['priority'] . '\', ' .
            '\'' . $_POST['status'] . '\', ' .
            '\'' . $_POST['creator'] . '\', ' .
            '\'' . $_POST['responsible'] . '\', ' .
            '\'' . $_POST['id'] . '\')';
        $this->oConnection->query($sQuery);
    }

    public function exitUser()
    {
        $sNewSessionId = bin2hex(random_bytes(5));
        $this->oConnection->query('update users set session_id = \'' . $sNewSessionId . '\' where 
         session_id = \'' . $_COOKIE['session'] . '\'');
    }
}