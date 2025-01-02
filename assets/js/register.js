window.onload = () => {
const registration_form = document.body.querySelector("#registration_form");
if (registration_form) {
  const allowEmail = document.body.querySelector('#allowEmail');
  const allowPassword = document.body.querySelector('#allowPassword');
  const allowAgreeTerms = document.body.querySelector('#allowAgreeTerms');
  const message = document.body.querySelector('#message');
  let information = 'Suivez les instructions ...';
  const password_criteria = document.body.querySelector('#password_criteria');
  info(message,information);
  yellowFont(message);
  const registration_form_email = registration_form.querySelector(
    "#registration_form_email"
  );
  registration_form_email.addEventListener("focus", function () {
    information = "Indiquez votre adresse courriel...";
    focusEmail(this,message,information,allowEmail,password_criteria);
  });
  registration_form_email.addEventListener('change',function(){
    changeEmail(this,message,allowEmail);
  });

  const registration_form_plainPassword =registration_form.querySelector('#registration_form_plainPassword');  // field input password
  const password_length_criteria = document.body.querySelector('#password_length_criteria');
  const password_special_character_criteria = document.body.querySelector('#password_special_character_criteria');
  const password_uppercase_criteria = document.body.querySelector('#password_uppercase_criteria');
  const password_number_criteria = document.body.querySelector('#password_number_criteria');
  const password_lowercase_criteria = document.body.querySelector('#password_lowercase_criteria');
  const all_password_criteria = document.body.querySelectorAll('li[data-password-criteria]');

  registration_form_plainPassword.addEventListener('focus',function({currentTarget}){
    this.value = "";
   
    const password = currentTarget.value;

    password_length_criteria.textContent = `12 caractères au total (${password.length}) `;

    if(password.length === 0){
      all_password_criteria.forEach(li => li.className = "");
      password_length_criteria.textContent = "12 caractères au total";
    }
    information = 'Indiquez votre mot de passe';
    focusPassword(this,message,information,allowPassword,password_criteria);
    clearBorder(this);
  });
  document.body.querySelector('#registration_form_plainPassword').addEventListener('input',({currentTarget}) => {
      const password = currentTarget.value;

      password_length_criteria.textContent = `12 caractères au total (${password.length}) `;

      if(password.length === 0){
        all_password_criteria.forEach(li => li.className = "");
        password_length_criteria.textContent = "12 caractères au total";
        return;
      }

      password_length_criteria.className = `password-criteria-${password.length === 12}`;
      password_special_character_criteria.className = `password-criteria-${(/[ !"#$%&'()*+,-.\/:;<=>?@\]^_`{|}~]/).test(password)}`;
      password_uppercase_criteria.className = `password-criteria-${(/[A-Z]/).test(password)}`;
      password_number_criteria.className = `password-criteria-${(/[0-9]/).test(password)}`;
      password_lowercase_criteria.className = `password-criteria-${(/[a-zà-ú]/).test(password)}`;
      });
    registration_form_plainPassword.addEventListener('blur',function(){
        for(var i =0; i < all_password_criteria.length; i++)
        {
          //console.log(all_password_criteria[i].classList);
            if(all_password_criteria[i].classList.contains('password-criteria-true')){
              message.innerHTML="";
              successBorder(this);     
              blurPassword(password_criteria);
              var mention ="Mot de passe OK !";
              greenAllow(allowPassword,mention);
              return;
            }
            else if(all_password_criteria[i].classList.contains('password-criteria-false')){
              var mention ="Mot de passe incorrect !";
              redAllow(allowPassword,mention); 
              alertBorder(this); 
              return;
            }
        }
      
    });  
    const registration_form_agreeTerms = document.body.querySelector('#registration_form_agreeTerms');
    const agreeSmall = document.body.querySelector('#agreeSmall');
    registration_form_agreeTerms.addEventListener('focus',function(){
      if(allowAgreeTerms.textContent !=""){
        var information = "";
        info(message,information);
        return;
      }
      information = "Acceptez les conditions pour continuer...";
      info(message,information);
      this.style.outline='none';
      agreeSmall.classList.remove('font-semibold');
      blurPassword(password_criteria);
    });
    registration_form_agreeTerms.addEventListener('input',function(){
      agreeTermsControl(this,agreeSmall);
      blurPassword(password_criteria);
    });
    registration_form_agreeTerms.addEventListener('blur',function(){

    });
 /*-------submit--------*/
 
}
}
/*------traitement---*/

const focusEmail = function (champ,screen,slogan,allowed,structure_password) {
  champ.value = "";
  clearBorder(champ);
  info(screen,slogan);
  yellowFont(screen);
  clearAllowed(allowed);
  structure_password.style.display='none';
};


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

const focusPassword = function (champ,screen,slogan,allowed,structure_password) {
  champ.value = "";
  clearBorder(champ);
  info(screen,slogan);
  yellowFont(screen);
  clearAllowed(allowed);
  structure_password.style.display="block";
};

const blurPassword = function(structure_password){
  structure_password.style.display="none";
}

const agreeTermsControl = function(champ,label){
  if(champ.checked){
    var information = "";
    info(message,information);
    var mention ="Conditions générales acceptées  OK !";
    greenAllow(allowAgreeTerms,mention);
    champ.style.outline='2px solid lightgreen';
    label.classList.remove('font-semibold');
 
  }
  else if(!champ.checked){
    var information = "Acceptez les conditions pour continuer...";
    info(message,information);
    var mention ="";
    redAllow(allowAgreeTerms,mention);
    champ.style.outline='2px solid red';
    label.classList.add('font-semibold');
    
  }
}

const info = function(screen,slogan){
    screen.innerHTML = slogan;
}

const clearAllowed = function(champ){
  champ.style.display="none";
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
