// Upload car 1 Popup
const uploadCard = document.getElementById("uploadCard");
const uploadOverlay = document.getElementById("uploadOverlay");
const closeUpload = document.getElementById("closeUpload");
const pdfInput = document.getElementById("resource");
uploadCard.addEventListener("click", function(e){
    e.preventDefault();
    uploadOverlay.style.display = "flex";

});
closeUpload.addEventListener("click", function(){
    uploadOverlay.style.display = "none";
});
window.addEventListener("click", function(e){
    if(e.target === uploadOverlay){
        uploadOverlay.style.display = "none";
    }
});
// PDF vitharai ganna eka(php ekath add karanna )
pdfInput.addEventListener("change", function(){
    const file = this.files[0];
    if(file){
        if(file.type !== "application/pdf"){
            alert("Please upload PDF files only.");
            this.value = "";
        }
    }
});
// Reminder Popup eka
const reminderCard = document.getElementById("reminderCard");
const reminderOverlay = document.getElementById("reminderOverlay");
const closeReminder = document.getElementById("closeReminder");
const openAddReminder = document.getElementById("openAddReminder");
const addReminderOverlay = document.getElementById("addReminderOverlay");
const closeAddReminder = document.getElementById("closeAddReminder");
reminderCard.onclick = function(e){
    e.preventDefault();
    reminderOverlay.style.display="flex";
}
closeReminder.onclick=function(){
    reminderOverlay.style.display="none";
}
openAddReminder.onclick=function(){
    addReminderOverlay.style.display="flex";
}
closeAddReminder.onclick=function(){
    addReminderOverlay.style.display="none";
}
window.onclick=function(e){
    if(e.target == reminderOverlay){
        reminderOverlay.style.display="none";
    }
    if(e.target == addReminderOverlay){
        addReminderOverlay.style.display="none";
    }
}