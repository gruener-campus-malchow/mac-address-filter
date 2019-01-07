document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('send').addEventListener('click', postData)
});

function postData() {
    $.post("/api/register.php", $("#register").serialize(), function (response) {
        if (JSON.parse(response)["email_sent"] === true) {
            document.getElementById("msg").innerHTML = "Erfolgreich registriert. Ihnen wurde eine E-Mail mit einer PIN geschickt, mit der Sie sich auf "+"<a href='https://gcmmac.uber.space/poc_login.html'>https://gcmmac.uber.space/poc_login.html</a>"+" einloggen können.";
        } else {
            $("#msg").html("Da ist was schiefgelaufen...");
        }
    });
}