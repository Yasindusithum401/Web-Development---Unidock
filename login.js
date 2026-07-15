//login popup eka 
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
//home page text animation eka
const words = [
    "Share, Discover and Access Quality Study Materials Easily.Learn Together, Grow Together."
];
let wordIndex = 0;
let charIndex = 0;
let deleting = false;
const text = document.getElementById("text");
function typing(){
    let current = words[wordIndex];
    if(!deleting){
        text.textContent = current.substring(0,charIndex + 1);
        charIndex++;
        if(charIndex === current.length){
            deleting = true;
            setTimeout(typing,1000);
            return;
        }
    }
    else{
        text.textContent = current.substring(0,charIndex - 1);
        charIndex--;
        if(charIndex === 0){
            deleting = false;
            wordIndex++;
            if(wordIndex === words.length){wordIndex = 0 }
        }
    }
    setTimeout(typing,67);
}
typing();