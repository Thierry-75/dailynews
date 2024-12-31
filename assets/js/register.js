window.onload = () => {
let registration_form = document.querySelector("#registration_form");
if (registration_form) {
  let allowEmail = document.querySelector('#allowEmail');
  let allowPassword = document.querySelector('#allowPassword');
  let message = document.querySelector('#message');
  let information = 'Suivez les instructions ...';
  info(message,information);
  yellowFont(message);
  let registration_form_email = registration_form.querySelector(
    "#registration_form_email"
  );
  registration_form_email.addEventListener("focus", function () {
    information = "Indiquez votre adresse courriel";
    focusEmail(this,message,information,allowEmail);
  });
  registration_form_email.addEventListener('change',function(){
    changeEmail(this,message,allowEmail);
  });
}
}
/*------traitement---*/
const focusEmail = function (champ,screen,slogan,allowed) {
  champ.value = "";
  clearBorder(champ);
  info(screen,slogan);
  yellowFont(screen);
  clearAllowed(allowed);

};

const clearAllowed = function(champ){
    champ.style.display="none";
}

const changeEmail = function (champ,screen,allowed){
  let emailRegexp = new RegExp(
    "^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
  );
  let slogan ="";
  if(champ.value.match(emailRegexp)){
    successBorder(champ);
    info(screen,slogan);
    var mention ="Adresse courriel OK !";
    greenAllow(allowed,mention);
  }else{
    alertBorder(champ);
    info(screen,slogan);
    var mention ="Adresse courriel incorrecte !";
    redAllow(allowed,mention);
  }
}


/*-----DOM------*/
const info = function(screen,slogan){
    screen.innerHTML = slogan;
}

const greenAllow = function(champ,slogan){
    info(champ,slogan);
    fontGreenAllow(champ);
}

const fontGreenAllow = function(champ){
  champ.style.display = "block";
  champ.classList.remove('text-red-300');
  champ.classList.add('text-green-300');
}

const redAllow = function(champ,slogan){
  info(champ,slogan);
  fontRedAllow(champ);
}

const fontRedAllow = function(champ){
  champ.style.display = "block";
  champ.classList.remove('text-green-300');
  champ.classList.add('text-red-300');
}


const yellowFont = function(champ){
  champ.classList.remove("text-gray-300","text-red-200");
  champ.classList.add("text-yellow-400");
}
const redFont = function(champ){
  champ.classList.remove("text-gray-300","text-yellow-400");
  champ.classList.add("text-red-300");
}

const clearBorder = function (champ) {
  champ.classList.remove("border-solid", "border-2", "border-green-600");
  champ.classList.remove("border-solid", "border-2", "border-red-600");
  champ.classList.add("border-none");
};
const alertBorder = function (champ) {
  champ.classList.remove("border-none");
  champ.classList.add("border-solid", "border-2", "border-red-600");
};
const successBorder = function (champ) {
  champ.classList.remove( "border-none"); 
  champ.classList.add("border-solid", "border-2", "border-green-600");
};
