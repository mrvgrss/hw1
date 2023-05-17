// trim() remove empty spaces
// /[\d\W]/ contain numeric value
function validateName(event) {
    const nameValue = event.currentTarget.value.trim();
    if(nameValue === '' || nameValue.length > 20 || /[\d\W]/.test(nameValue)){
        showError(nameInput.nextElementSibling); 
    }else{
        hideError(nameInput.nextElementSibling);
    }
}
function validateSurname(event) {
    const surnameValue = event.currentTarget.value.trim();
    if(surnameValue === '' || surnameValue.length > 20 || /[\d\W]/.test(surnameValue)){
        showError(surnameInput.nextElementSibling);
    }else{
        hideError(surnameInput.nextElementSibling);
    }
}

function validateEmail(event) {
    const emailValue = event.currentTarget.value.trim();
    if (!isValidEmail(emailValue)){
        showError(emailInput.nextElementSibling);
    }else{
        hideError(emailInput.nextElementSibling);
    }
}

function validatePassword(event){
    const passwordValue = event.currentTarget.value;
    if(passwordValue.length < 8){
        showError(passwordInput.nextElementSibling);
    }else{
        hideError(passwordInput.nextElementSibling);
    }
}

function validateConfirmPassword(event){
    const confirmPasswordValue = event.currentTarget.value;
    const passwordValue = passwordInput.value;
    if(confirmPasswordValue !== passwordValue){
        showError(confirmPasswordInput.nextElementSibling); 
    } else {
      hideError(confirmPasswordInput.nextElementSibling);
    }
}

function isValidEmail(email) {
    const emailRegex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
    return emailRegex.test(email);
  }

function showError(element) {
    element.style.display = 'block';
}

function hideError(element) {
    element.style.display = 'none';
}

const form = document.querySelector('form[name="input_form"]');
const nameInput = document.querySelector('input[name="name"]');
const surnameInput = document.querySelector('input[name="surname"]');
const emailInput = document.querySelector('input[name="email"]');
const passwordInput = document.querySelector('input[name="password"]');
const confirmPasswordInput = document.querySelector('input[name="confirmpassword"]');
const submitButton = document.querySelector('#submit');

nameInput.addEventListener('blur',validateName);
surnameInput.addEventListener('blur',validateSurname);
emailInput.addEventListener('blur', validateEmail);
passwordInput.addEventListener('blur', validatePassword);
confirmPasswordInput.addEventListener('blur', validateConfirmPassword);
// form.addEventListener('submit', validateForm());