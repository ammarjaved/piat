var golbal_isVaild = true;

function handleKeyPress(event) {
    var val = event.target.value;

    if (val.length < 10) {
        $('#sn_exits').html("Sn no must be at least 10 characters");
        golbal_isVaild = false;
        return false;
    }else{
        $('#sn_exits').html("");
        golbal_isVaild = true;
    }

    var url = '';
    var dat = '';

    if ($(`#${event.target.id}`).hasClass('edit')) {
        let id = $('#id').val();
        dat = { sn: event.target.value, id: id };
    } else {
        dat = { sn: event.target.value };
    }

    $.ajax({
        url: "./services/snNo.php",
        type: "GET",
        data: dat,
        dataType: "json",
        success: function(response) {
            if (response.success === true) {
                $('#sn_exits').html("Sn no already exists");
                golbal_isVaild = false;
            } else {
                $('#sn_exits').html("");
                golbal_isVaild = true;
            }
        },
        error: function(xhr, status, error) {
            console.log("Error: " + error);
        }
    });
}


function submitFoam(){

    var class_error = document.getElementsByClassName("required");
    var isValid = true;
    for(var i = 0 ; i < class_error.length ; i++){    
        
        class_error[i].parentNode.parentNode.firstElementChild.lastElementChild.lastElementChild.innerHTML = class_error[i].value === ""?  "This feild is required" : "";
        if(isValid){
        isValid =  class_error[i].value === ""? false : true
        }
    }   
    if(golbal_isVaild === true){
        return isValid
      }
      return golbal_isVaild;
    }
  

