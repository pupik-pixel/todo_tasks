var jsonData;
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
    data.responsibles.forEach(element => {
        let newOption = document.createElement("option");
        newOption.value = element.id;
        newOption.text = element.name;
        responsibleSelector.add(newOption, null);
    });
}

let modalBody = document.querySelector('.modal-body');
let responsibleModalSelector = document.querySelector('.responsible-select-modal-js');
let priorityModalSelector = document.querySelector('.priority-select-modal-js');
let statusModalSelector = document.querySelector('.status-select-modal-js');
function onClickRowHandler(event) {
    let taskData = jsonData.tasksData;
    let taskIndex = event.currentTarget.rowIndex - 1;
    let modalInput = document.querySelector('input[name=\'ID\']');
    modalInput.value = taskData[taskIndex].id;
    modalInput = document.querySelector('input[name=\'caption\']');
    modalInput.value = taskData[taskIndex].caption;
    modalInput = document.querySelector('input[name=\'description\']');
    modalInput.value = taskData[taskIndex].description;
    modalInput = document.querySelector('input[name=\'date_of_creation\']');
    modalInput.value = taskData[taskIndex].date_of_creation;
    modalInput = document.querySelector('input[name=\'expiration_date\']');
    modalInput.value = taskData[taskIndex].expiration_date;
    modalInput = document.querySelector('input[name=\'update_date\']');
    modalInput.value = taskData[taskIndex].update_date;
    modalInput = document.querySelector('input[name=\'cName\']');
    modalInput.value = taskData[taskIndex].cName;

    let priorityData = jsonData.priority;
    $('.priority-select-modal-js').empty();
    newOption = document.createElement("option");
    newOption.value = taskData[taskIndex].priorityId;
    newOption.text = taskData[taskIndex].priority;
    newOption.setAttribute('selected', 'selected');
    priorityModalSelector.add(newOption, null);
    let priorityId = taskData[taskIndex].priorityId;
    for (var key in priorityData) {
        if (priorityData[key].id == priorityId) {
            continue;
        }
        newOption = document.createElement("option");
        newOption.value = priorityData[key].id;
        newOption.text = priorityData[key].value;
        priorityModalSelector.add(newOption, null);
    }

    let statusData = jsonData.status;
    $('.status-select-modal-js').empty();
    newOption = document.createElement("option");
    newOption.value = taskData[taskIndex].statusId;
    newOption.text = taskData[taskIndex].status;
    newOption.setAttribute('selected', 'selected');
    statusModalSelector.add(newOption, null);
    let statusId = taskData[taskIndex].statusId;
    for (var key in statusData) {
        if (statusData[key].id == priorityId) {
            continue;
        }
        newOption = document.createElement("option");
        newOption.value = statusData[key].id;
        newOption.text = statusData[key].value;
        statusModalSelector.add(newOption, null);
    }

    let responsiblesData = jsonData.responsibles;
    $('.responsible-select-modal-js').empty();
    newOption = document.createElement("option");
    newOption.value = taskData[taskIndex].responsible;
    newOption.text = taskData[taskIndex].rName;
    newOption.setAttribute('selected', 'selected');
    responsibleModalSelector.add(newOption, null);
    let responsibleId = taskData[taskIndex].responsible;
    for (var key in responsiblesData) {
        if (responsiblesData[key].id == responsibleId) {
            continue;
        }
        newOption = document.createElement("option");
        newOption.value = responsiblesData[key].id;
        newOption.text = responsiblesData[key].name;
        responsibleModalSelector.add(newOption, null);
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
        ajaxQuery();
        responsibleSelector.removeAttribute("disabled");
    }
    else {
        responsibleSelector.setAttribute("disabled", "disabled");
    }
}
responsibleSelector.onchange = function() {
    ajaxQuery();
}

let sortingCheckbox = document.querySelector('.sort-check-input-js');
sortingCheckbox.onchange = function() {
    if (sortingCheckbox.checked) {
        ajaxQuery();
    }
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
            if (jsonData.isAuthentication) {
                fillTableWithTasks(jsonData);
                console.log(jsonData);
            }
            else {
                window.location.replace('auth.php');
            }
        }
    });
}

let modalSubmitButton = document.querySelector('.update-btn-primary');
modalSubmitButton.onclick = function() {
    let dataFromForm = $('.update-modal-body').serialize() + '&update=true';
    ajaxQuery(dataFromForm);
}

ajaxQuery();
