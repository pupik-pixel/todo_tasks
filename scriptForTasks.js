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
function onClickRowHandler(event) {
    let taskData = jsonData.tasksData;
    let taskIndex = event.currentTarget.rowIndex - 1;
    let idInput = document.querySelector('input[name=\'ID\']');
    idInput.value = taskData[taskIndex].id;
    idInput = document.querySelector('input[name=\'caption\']');
    idInput.value = taskData[taskIndex].caption;
    idInput = document.querySelector('input[name=\'description\']');
    idInput.value = taskData[taskIndex].description;
    idInput = document.querySelector('input[name=\'date_of_creation\']');
    idInput.value = taskData[taskIndex].date_of_creation;
    idInput = document.querySelector('input[name=\'expiration_date\']');
    idInput.value = taskData[taskIndex].expiration_date;
    idInput = document.querySelector('input[name=\'update_date\']');
    idInput.value = taskData[taskIndex].update_date;
    idInput = document.querySelector('input[name=\'priority\']');
    idInput.value = taskData[taskIndex].priority;
    idInput = document.querySelector('input[name=\'status\']');
    idInput.value = taskData[taskIndex].status;
    idInput = document.querySelector('input[name=\'cName\']');
    idInput.value = taskData[taskIndex].cName;

    let responsiblesData = jsonData.responsibles;
    $('.responsible-select-modal-js').empty();
    let newOption = document.createElement("option");
    newOption.value = taskData[taskIndex].responsible;
    newOption.text = taskData[taskIndex].rName;
    responsibleModalSelector.setAttribute('selected', 'selected');
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

function ajaxQuery() {
    dataForQuery = {};
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

ajaxQuery();
