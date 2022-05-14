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
    data.responsibles.forEach(element => {
        let responsibleSelect = document.querySelector('.responsible-select-js');
        let newOption = document.createElement("option");
        newOption.value = element.name;
        newOption.text = element.name;
        responsibleSelect.add(newOption, null);
    });
}

let dateCheckbox = document.querySelector('.date-check-input-js');
let dateSelector = document.querySelector('.date-select-js');
dateSelector.onchange = function() {
    ajaxQuery({
        'filterData': dateSelector.value
    });
}
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
