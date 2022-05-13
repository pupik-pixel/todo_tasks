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
}

$.ajax({
    datatype: "json",
    type: "POST",
    url: 'tasksAction.php',
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
