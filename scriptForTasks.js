var jsonData;
var operationType;
var currentFilterByResponsible = false;
function fillTableWithTasks(data) {
    $(".table > tbody").remove(); 
    let table = document.querySelector('.table');
    let tbody = table.createTBody();
    data.tasksData.forEach(element => {
        let taskDate = Date.parse(element.expiration_date);
        let nowDate = new Date();
        let dateDiff = nowDate - taskDate;
        let newRow = tbody.insertRow(); 
        newRow.onclick = onClickRowHandler;
        newRow.setAttribute("data-bs-toggle", "modal");
        newRow.setAttribute("data-bs-target", "#editorModal");
        if (dateDiff > 0 && (element.status == 'к выполнению' || element.status == 'выполняется')) {
            newRow.classList.add("table-danger");
        }
        else if (element.status == 'выполнена') {
            newRow.classList.add("table-success");
        }
        else {
            newRow.classList.add("table-light");
        }
        for (var key in element) {
            if (key == 'description') break;
            let newCell = newRow.insertCell();
            let newText = document.createTextNode(element[key]);
            newCell.appendChild(newText);     
        }
    });
    $('.responsible-select-js').empty();
    if (currentFilterByResponsible != false) {
        let newOption = document.createElement("option");
        newOption.value = currentFilterByResponsible[0];
        newOption.text = currentFilterByResponsible[1];
        newOption.setAttribute('selected', 'selected');
        responsibleSelector.add(newOption, null);
    }
    for (var key in data.responsibles) {
        if (data.responsibles[key].id == currentFilterByResponsible[0]) continue;
        let newOption = document.createElement("option");
        newOption.value = data.responsibles[key].id;
        newOption.text = data.responsibles[key].name;
        responsibleSelector.add(newOption, null);
    }
}

let responsibleModalSelector = document.querySelector('.responsible-select-modal-js');
let priorityModalSelector = document.querySelector('.priority-select-modal-js');
let statusModalSelector = document.querySelector('.status-select-modal-js');
function onClickRowHandler(event) {
    operationType = 'update';
    let tasksData = jsonData.tasksData;
    let supervisorIdForCurrentUser = jsonData.isAuthentication.supervisor;
    let taskIndex = event.currentTarget.rowIndex - 1;
    let elementArray = ['id', 'caption', 'description', 'date_of_creation', 'expiration_date', 'update_date', 'cName'];
    for (var key of elementArray) {
        let modalInput = document.querySelector('input[name=\''+ key + '\']');
        if (key == 'id' || key == 'date_of_creation' || key == 'update_date' || key == 'cName') {
            modalInput.classList.remove('form-control');
            modalInput.classList.add('input-group-text');
        }
        else if (supervisorIdForCurrentUser == tasksData[taskIndex].creator) {
            modalInput.classList.remove('form-control');
            modalInput.classList.add('input-group-text');
        }
        else {
            modalInput.classList.add('form-control');
            modalInput.classList.remove('input-group-text');
        }
        modalInput.value = tasksData[taskIndex][key];
    }

    fillSelectorsInModal('priority', priorityModalSelector, tasksData, taskIndex, supervisorIdForCurrentUser);
    fillSelectorsInModal('status', statusModalSelector, tasksData, taskIndex);
    fillSelectorsInModal('responsible', responsibleModalSelector, tasksData, taskIndex, supervisorIdForCurrentUser);
}

function fillSelectorsInModal(keyString, keyModalSelector, tasksData, taskIndex, supervisorIdForCurrentUser = '') {
    let keyData;
    if (keyString == 'responsible') keyData = jsonData.isAuthentication.subordinates;
    else keyData = jsonData[keyString];
    $('.' + keyString + '-select-modal-js').empty();
    newOption = document.createElement("option");
    if (keyString == 'priority' || keyString == 'status') {
        newOption.value = tasksData[taskIndex][keyString + 'Id'];
        newOption.text = tasksData[taskIndex][keyString];
    }
    else {
        newOption.value = tasksData[taskIndex].responsible;
        newOption.text = tasksData[taskIndex].rName;
    }
    newOption.setAttribute('selected', 'selected');
    keyModalSelector.add(newOption, null);
    let keyId;
    if (keyString == 'priority' || keyString == 'status') keyId = tasksData[taskIndex][keyString + 'Id'];
    else
    keyId = tasksData[taskIndex].responsible;
    for (var key in keyData) {
        if (keyData[key].id == keyId) {
            continue;
        }
        newOption = document.createElement("option");
        newOption.value = keyData[key].id;
        if (keyString == 'priority' || keyString == 'status') {
            newOption.text = keyData[key].value;
        }
        else {
            newOption.text = keyData[key].name;
        }
        keyModalSelector.add(newOption, null);
    }
}

function fillSelectorsInModalWithoutData(keyString, keyModalSelector) {
    let keyData;
    if (keyString == 'responsible') keyData = jsonData.isAuthentication.subordinates;
    else keyData = jsonData[keyString];
    $('.' + keyString + '-select-modal-js').empty();
    for (var key in keyData) {
        newOption = document.createElement("option");
        newOption.value = keyData[key].id;
        if (keyString == 'priority' || keyString == 'status') {
            newOption.text = keyData[key].value;
        }
        else {
            newOption.text = keyData[key].name;
        }
        keyModalSelector.add(newOption, null);
    }
}

let dateCheckbox = document.querySelector('.date-check-input-js');
let dateSelector = document.querySelector('.date-select-js');
dateCheckbox.onchange = function() {
    if (dateCheckbox.checked) {
        ajaxQuery();
        dateSelector.removeAttribute("disabled");
    }
    else {
        ajaxQuery();
        dateSelector.setAttribute("disabled", "disabled");
    }
}
dateSelector.onchange = function() {
    ajaxQuery();
}

let responsibleCheckbox = document.querySelector('.responsible-check-input-js');
let responsibleSelector = document.querySelector('.responsible-select-js');
responsibleCheckbox.onchange = function() {
    if (responsibleCheckbox.checked) {
        currentFilterByResponsible = [responsibleSelector.value, responsibleSelector.options[responsibleSelector.selectedIndex].text];
        ajaxQuery();
        responsibleSelector.removeAttribute("disabled");
    }
    else {
        currentFilterByResponsible = false;
        ajaxQuery();
        responsibleSelector.setAttribute("disabled", "disabled");
    }
}
responsibleSelector.onchange = function() {
    currentFilterByResponsible = [responsibleSelector.value, responsibleSelector.options[responsibleSelector.selectedIndex].text];
    ajaxQuery();
}

let sortingCheckbox = document.querySelector('.sort-check-input-js');
sortingCheckbox.onchange = function() {
    ajaxQuery();
}

function ajaxQuery(dataForQuery = {}) {
    if (dateCheckbox.checked) Object.assign(dataForQuery, {
        'filterData': dateSelector.value
    });
    if (responsibleCheckbox.checked) Object.assign(dataForQuery, {
        'filterResponsible': responsibleSelector.value
    });
    if (sortingCheckbox.checked) Object.assign(dataForQuery, {
        'sortByUpdateDate': true
    });
    $.ajax({
        type: "POST",
        url: 'tasksAction.php',
        data: dataForQuery,
        success: function (response) {
            jsonData = JSON.parse(response);
            if (jsonData.isAuthentication.status) {
                fillTableWithTasks(jsonData);
                console.log(jsonData);
            }
            else {
                window.location.replace('auth.html');
            }
        }
    });
}

let addTaskButton = document.querySelector('.btn-add-task');
addTaskButton.setAttribute("data-bs-toggle", "modal");
addTaskButton.setAttribute("data-bs-target", "#editorModal");
addTaskButton.onclick = function() {
    operationType = 'insert';
    let elementArray = ['id', 'caption', 'description', 'date_of_creation', 'expiration_date', 'update_date', 'cName'];
    for (var key of elementArray) {
        let modalInput = document.querySelector('input[name=\''+ key + '\']');
        if (key == 'cName') {
            modalInput.value = jsonData.isAuthentication.userName;
            modalInput.classList.remove('form-control');
            modalInput.classList.add('input-group-text');
        }
        else {
            modalInput.value = '';
            modalInput.classList.add('form-control');
            modalInput.classList.remove('input-group-text');
        }
    }
    fillSelectorsInModalWithoutData('priority', priorityModalSelector);
    fillSelectorsInModalWithoutData('status', statusModalSelector);
    fillSelectorsInModalWithoutData('responsible', responsibleModalSelector);
}

let buttonExit = document.querySelector('.btn-exit');
buttonExit.onclick = function() {
    ajaxQuery({
        'exitTheApplication': true
    });
}

let modalSubmitButton = document.querySelector('.save-btn-primary');
modalSubmitButton.onclick = function() {
    let addingString;
    if (operationType == 'update') addingString = '&update=true';
    if (operationType == 'insert') addingString = '&insert=true&creator=' + jsonData.isAuthentication.userId;
    let dataFromForm = $('.modal-body').serialize() + addingString;
    ajaxQuery(dataFromForm);
}

ajaxQuery();
