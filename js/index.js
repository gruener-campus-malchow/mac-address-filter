var indexRoot = document.getElementById("index-root");
var loginRoot = document.getElementById("login-root");
var registrationRoot = document.getElementById("registration-root");

var indexRootLoginButton = document.getElementById("index-root-button-login");
var indexRootRegistrationButton = document.getElementById("index-root-button-registration");
var loginRootBackButton = document.getElementById("login-root-button-back");
var registrationRootBackButton = document.getElementById("registration-root-button-back");

indexRootLoginButton.addEventListener("click", switchToLogin);
indexRootRegistrationButton.addEventListener("click", switchToRegistration);

loginRootBackButton.addEventListener("click", switchToIndex);
registrationRootBackButton.addEventListener("click", switchToIndex);

function switchToLogin()
{
	indexRoot.style.display = "none";
	loginRoot.style.display = "block";
}

function switchToRegistration()
{
	indexRoot.style.display = "none";
	registrationRoot.style.display = "block";
}

function switchToIndex()
{
	indexRoot.style.display = "block";
	loginRoot.style.display = "none";
	registrationRoot.style.display = "none";
}
