function filterTable(inputId,tableId){
 const q=document.getElementById(inputId).value.toLowerCase();
 document.querySelectorAll("#"+tableId+" tbody tr").forEach(row=>{
   row.style.display=row.innerText.toLowerCase().includes(q)?"":"none";
 });
}
function updateDelivery(order){
 alert("Update delivery status for "+order+". Connect this action to your PHP/MySQL backend.");
}
function reviewAction(status){
 alert(status==="Pending"?"Approve/reject review from this action.":"Review action opened.");
}
