
function setPasswordView(event){
    event.preventDefault();
    const passwordView = event.target;

    const passwordInput = passwordView.nextElementSibling; // or use parentnode and then a query selector
    if(passwordView.classList.contains('hideicon')){
        passwordView.classList.remove('hideicon');
        passwordView.classList.add('viewicon');
        passwordInput.type = 'text';
    }else{
        passwordView.classList.remove('viewicon');
        passwordView.classList.add('hideicon');
        passwordInput.type = 'password';
    }   
}


const passwordViews = document.querySelectorAll(".passwordview");
passwordViews.forEach(function(pv) {
    pv.addEventListener('click', setPasswordView);
});