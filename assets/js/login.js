window.onload = () => {
let form_login = document.querySelector("#form_login");
if (form_login) {
  let inputEmail = form_login.querySelector("#inputEmail");
  inputEmail.addEventListener("focus", function () {
    clearEmail(this);
  });
  inputEmail.addEventListener("change", function () {
    controlEmail(this);
  });

 let inputPassword = form_login.querySelector('#inputPassword');
 inputPassword.addEventListener('focus',function(){
    clearPassword(this);
 });
 inputPassword.addEventListener('change',function(){
    controlPassword(this);
 });

 let inputSubmit =form_login.querySelector('#inputSubmit');
 inputSubmit.addEventListener('click',function(event){
    let inputs = form_login.getElementsByTagName('input');
    let fieldSuccess = [];
    let counter = 0;
    let nbBorder =0;
    for(var i = 0; i < inputs.length; i++){
        if(inputs[i].type =='email' || inputs[i].type =='password'){
            fieldSuccess[i] = inputs[i];
            if(fieldSuccess[i].value== ''){
                alertBorder(fieldSuccess[i]);
                counter++;
            }
            if(fieldSuccess[i].classList.contains('border-green-600')){
                nbBorder++;
            }
        }
    }
    if(!counter == 0 ||  !fieldSuccess.length == nbBorder){
        event.preventDefault();
        event.stopImmediatePropagation();
        return false;
    }
 });
}
}
/*------traitement---*/
const clearEmail = function (champ) {
  champ.value = "";
  clearBorder(champ);
};
const controlEmail = function (champ) {
  let emailRegexp = new RegExp(
    "^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
  );
  if (champ.value.match(emailRegexp)) {
    successBorder(champ);
  } else {
    alertBorder(champ);
  }
};
const clearPassword = function(champ){
    champ.value="";
    clearBorder(champ);
}
const controlPassword = function(champ){
    let passwordRegexp = new RegExp(
        "^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$ %^&*-]).{10,12}$"
      );
      if(champ.value.match(passwordRegexp)){
        successBorder(champ);
      }else{
        alertBorder(champ);
      }
}

/*-----DOM------*/
const clearBorder = function (champ) {
  champ.classList.remove("border-solid", "border-2" , "border-green-600");
  champ.classList.remove("border-solid" ,"border-2", "border-red-600");
};
const alertBorder = function (champ) {
  champ.classList.remove("border-solid", "border-2", "border-green-600");
  champ.classList.add("border-solid",  "border-2", "border-red-600");
};
const successBorder = function (champ) {
  champ.classList.remove("border-solid", "border-2", "border-red-600");
  champ.classList.add("border-solid", "border-2", "border-green-600");
};
