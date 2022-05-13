let dateCheckbox = document.querySelector('.date-check-input-js');
let dateSelector = document.querySelector('.date-select-js');
dateCheckbox.onchange = function() {
    if (dateCheckbox.checked) {
        let xhr = new XMLHttpRequest();
        let body = 'enableFilterDate=true&timeForFilterDate=' + dateSelector.value;
        xhr.open("POST", 'tasks.php');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(body);
        dateSelector.removeAttribute("disabled");
    }
    else {
        dateSelector.setAttribute("disabled", "disabled");
    }
}