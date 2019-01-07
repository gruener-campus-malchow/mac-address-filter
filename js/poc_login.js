document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('send').addEventListener('click', postData)
});

function postData() {
    $.post("/api/login.php", $("#login").serialize(), function (response) {
        if (JSON.parse(response)["logged_in"] === true) {
            window.location.href = "poc_main.html";
        } else {
            $("#msg").html("Falsche E-Mail oder PIN");
        }
    });
}