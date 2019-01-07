document.addEventListener('DOMContentLoaded', function () {
    document.getElementById("insertBtn").addEventListener("click", insertMac);
    document.getElementById("logoutBtn").addEventListener("click", logout)

});

function insertMac() {
    $.post("/api/insertmac.php", $("#macInputForm").serialize(), function (response) {
        console.log(response);
        if (JSON.parse(response)["success"] === true) {
            $("#msg").html("MAC erfolgreich hinzugefügt");
            updateTable();
        } else {
            $("#msg").html("Nicht zulässige Eingabe");
        }
    });
}

function updateTable() {
    $("#macTable").html("");
    $.getJSON("/api/returnmacs.php",
    function (data) {
        $.each(data, function (i, item) {
            $('<tr>').append(
                $('<td>').text(item.mac),
                $('<td>').text(item.deviceName)
            ).appendTo('#macTable');
        });
    });
}

function logout() {
    $.get("/api/logout.php");
    window.location.replace("/poc_login.html");
}

updateTable();