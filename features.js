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
//todolist popup eka 
const todolistCard = document.getElementById("todolistCard");
const todolistoverlay = document.getElementById("todolistoverlay");
const closetodolist = document.getElementById("closetodolist");
const openAddtodolist = document.getElementById("openAddtodolist");
const addtodolistOverlay = document.getElementById("addtodolistOverlay");
const closeAddtodolist = document.getElementById("closeAddtodolist");
todolistCard.onclick = function(b){
    b.preventDefault();
    todolistoverlay.style.display="flex";
}
closetodolist.onclick=function(){
    todolistoverlay.style.display="none";
}
openAddtodolist.onclick=function(){
    addtodolistOverlay.style.display="flex";
}
closeAddtodolist.onclick=function(){
    addtodolistOverlay.style.display="none";
}
window.onclick=function(b){
    if(b.target == todolistoverlay){
        todolistoverlay.style.display="none";
    }
    if(b.target == addtodolistOverlay){
        addtodolistOverlay.style.display="none";
    }
}
//clear and cut function eka 
const clearAllTodoBtn = document.getElementById("clearAllTodoBtn");
todoListContainer.addEventListener("click", function(e) {
    const card = e.target.closest(".todolistCard");
    if (card) {
        card.classList.toggle("completed"); 
    }
});
clearAllTodoBtn.addEventListener("click", function() {
    if (confirm("Are you sure you want to delete all tasks?")) {
        todoListContainer.innerHTML = '<div class="emptytodolist">No To Do list yet.</div>';
    }
});
