function fillTableWithTasks(data) {
    $(".table > tbody").remove(); 
    let table = document.querySelector('.table');
    let tbody = table.createTBody();
    data.tasksData.forEach(element => {
        let taskDate = Date.parse(element.expiration_date);
        let nowDate = new Date();
        let dateDiff = nowDate - taskDate;
        let newRow = tbody.insertRow(); 
        if (dateDiff > 0 && (element.status == 'к выполнению' || element.status == 'выполняется')) {
            newRow.classList.add("table-danger");
        }
        else if (element.status == 'выполнена') {
            newRow.classList.add("table-success");
        }
        else {
            newRow.classList.add("table-light");
        }
        (Object.values(element)).forEach(element => {
            let newCell = newRow.insertCell();
            let newText = document.createTextNode(element);
            newCell.appendChild(newText); 
        });
    });
    $('.responsible-select-js').empty();
    data.responsibles.forEach(element => {
        let newOption = document.createElement("option");
        newOption.value = element.id;
        newOption.text = element.name;
        responsibleSelector.add(newOption, null);
    });
}

let dateCheckbox = document.querySelector('.date-check-input-js');
let dateSelector = document.querySelector('.date-select-js');
dateCheckbox.onchange = function() {
    if (dateCheckbox.checked) {
        ajaxQuery({
            'filterData': dateSelector.value
        });
        dateSelector.removeAttribute("disabled");
    }
    else {
        dateSelector.setAttribute("disabled", "disabled");
    }
}
dateSelector.onchange = function() {
    ajaxQuery({
        'filterData': dateSelector.value
    });
}

let responsibleCheckbox = document.querySelector('.responsible-check-input-js');
let responsibleSelector = document.querySelector('.responsible-select-js');
responsibleCheckbox.onchange = function() {
    if (responsibleCheckbox.checked) {
        ajaxQuery({
            'filterResponsible': responsibleSelector.value
        });
        responsibleSelector.removeAttribute("disabled");
    }
    else {
        responsibleSelector.setAttribute("disabled", "disabled");
    }
}
responsibleSelector.onchange = function() {
    ajaxQuery({
        'filterResponsible': responsibleSelector.value
    });
}

function ajaxQuery(dataForQuery = null) {
    $.ajax({
        type: "POST",
        url: 'tasksAction.php',
        data: dataForQuery,
        success: function (response) {
            let jsonData = JSON.parse(response);
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
