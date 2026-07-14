const openModalBtn = document.getElementById('openModalBtn');
const closeModalBtn = document.getElementById('closeModalBtn');
const modalOverlay = document.getElementById('modaloverlay');
const switchToSignUp = document.getElementById('switchToSignUp');
const switchToLogIn = document.getElementById('switchToLogIn');
const loginFormContainer = document.getElementById('loginFormContainer');
const signupFormContainer = document.getElementById('signupFormContainer');

if(openModalBtn) {
    openModalBtn.addEventListener('click', (event) => {
        event.preventDefault();
        modalOverlay.classList.add('active');
       
        loginFormContainer.classList.add('active');
        signupFormContainer.classList.remove('active');
    });
}
if(closeModalBtn) {
    closeModalBtn.addEventListener('click', () => {
        modalOverlay.classList.remove('active');
    });
}
if(modalOverlay) {
    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) {
            modalOverlay.classList.remove('active');
        }
    });
}
if(switchToSignUp) {
    switchToSignUp.addEventListener('click', (e) => {
        e.preventDefault();
        loginFormContainer.classList.remove('active');
        signupFormContainer.classList.add('active');
    });
}
if(switchToLogIn) {
    switchToLogIn.addEventListener('click', (e) => {
        e.preventDefault();
        signupFormContainer.classList.remove('active');
        loginFormContainer.classList.add('active');
    });
}