//login popup eka js 1 
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
//mobile list eka js 2 eka
const openModalBtn2 = document.getElementById("openModalBtn2");

if (openModalBtn2) {
    openModalBtn2.addEventListener("click", function (e) {
        e.preventDefault();
        modalOverlay.classList.add("active");
        navMenu.classList.remove("active");
    });
}
// Mobile Menu
const menuToggle = document.getElementById("menuToggle");
const navMenu = document.getElementById("navMenu");

if(menuToggle && navMenu){
    menuToggle.addEventListener("click", () => {
        navMenu.classList.toggle("active");
    });
}
//home page text animation eka js 3 eka 
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