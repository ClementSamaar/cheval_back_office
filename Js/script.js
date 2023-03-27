// Popup overlay

function openForm() {
    document.getElementById("myForm").style.display = "block";
  }
  
  function closeForm1() {
    document.getElementById("myForm").style.display = "none";
  }

  function openModif() {
    document.getElementById("myForm1").style.display = "block";
  }

  function closeForm() {
    document.getElementById("myForm1").style.display = "none";
  }

  

  // Case à cocher
  
  var checkAll = document.getElementById("checkAll");
  var checkboxes = document.querySelectorAll(".checkbox");
  
  
  checkAll.addEventListener("change", function(){
    checkboxes.forEach(function(checkbox) {
      checkbox.checked = checkAll.checked;
    });
  });

