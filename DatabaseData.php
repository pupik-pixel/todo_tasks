<?php

class DatabaseData
{

    private $oConnection;

    function __construct() {
        $this->oConnection = new mysqli('localhost', 'root', '', 'todo_list_task');
    }

    function __destruct() {
        $this->oConnection->close();
    }

    public function isAuthenticationStatus()
    {
        $aResult = [];
        $oQuery = $this->oConnection->query(
            'select session_id,
                    id,
                    concat (surname, \' \', name, \' \', patronymic_name) as name,
                    supervisor
                    from users 
                    where session_id = \'' . $_COOKIE['session'] . '\'');
        if ($aResultFromUserTable = $oQuery->fetch_assoc()) {
            $aResult = [
                'status' => true,
                'userId' => $aResultFromUserTable['id'],
                'userName' => $aResultFromUserTable['name'],
                'supervisor' => $aResultFromUserTable['supervisor']
            ];
            $oQuery = $this->oConnection->query(
                'select id,
                concat(users.surname, 
                    \' \',
                users.name,
                    \' \',
                users.patronymic_name) as name
                    from users 
                    where supervisor = \'' . $aResult['userId'] . '\'');
            while ($aResultFromUserTable = $oQuery->fetch_assoc()) {
                $aResult['subordinates'][] = $aResultFromUserTable;
            }
            return $aResult;
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
        if (!empty($_POST['filterData']) && !empty($_POST['filterResponsible'])) {
            $sQuery = $this->addFilterByDate($sQuery);
            $sQuery = $sQuery.' and responsible = \''.$_POST['filterResponsible'].'\'';
        }
        elseif (!empty($_POST['filterData'])) {
            $sQuery = $this->addFilterByDate($sQuery);
        }
        elseif (!empty($_POST['filterResponsible'])) {
            $sQuery = $sQuery.' where responsible = \''.$_POST['filterResponsible'].'\'';
        }
        if (!empty($_POST['sortByUpdateDate'])) {
            $sQuery = $sQuery . ' order by update_date asc';
        } else {
            $sQuery = $sQuery . ' order by expiration_date asc';
        }
        $oQuery = $this->oConnection->query($sQuery);
        while ($aResultRow = $oQuery->fetch_assoc()) {
            $aResult[] = $aResultRow;
        }
        return $aResult;
    }

    private function addFilterByDate($sQuery) {
        switch ($_POST['filterData']) {
            case 'today':
            {
                $oCurrentDate = new DateTime(date('Y-m-d'));
                $sQuery = $sQuery . ' where expiration_date=\'' . $oCurrentDate->format('Y-m-d') . '\'';
                break;
            }
            case 'duringThisWeek':
            {
                $oNextWeekDate = new DateTime(date('Y-m-d'));
                $oNextWeekDate->add(new DateInterval('P1W'));
                $sQuery = $sQuery . ' where expiration_date<\'' . $oNextWeekDate->format('Y-m-d') . '\'';
                break;
            }
        }
        return $sQuery;
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
                group by tasks.responsible';
        $sQuery = $sQuery . ' ';
        $oQuery = $this->oConnection->query($sQuery);
        while ($aResultRow = $oQuery->fetch_assoc()) {
            $aResult[] = $aResultRow;
        }
        return $aResult;
    }

    public function getAllPriority()
    {
        $aResult = [];
        $sQuery = 'select * from priority';
        $oQuery = $this->oConnection->query($sQuery);
        while ($aResultRow = $oQuery->fetch_assoc()) {
            $aResult[] = $aResultRow;
        }
        return $aResult;
    }

    public function getAllStatus()
    {
        $aResult = [];
        $sQuery = 'select * from status';
        $oQuery = $this->oConnection->query($sQuery);
        while ($aResultRow = $oQuery->fetch_assoc()) {
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