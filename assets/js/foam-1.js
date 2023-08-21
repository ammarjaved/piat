var golbal_isVaild = true;
$(document).ready(function(){
      
  
    $("input[type='number']").on("blur", function() {
        var inputValue = $(this).val();
        var parentElement = $(this).parent();
        var errorSpan = parentElement.find('span');

        if (inputValue < 1  && inputValue != "") {
            if (errorSpan.length === 0) {
                parentElement.prepend("<span class='text-danger'>Number must be greater than 0</span>");
            }
             golbal_isVaild = false;
        } else {
            if (errorSpan.length > 0) {
                errorSpan.remove();
            }
            golbal_isVaild = true;
        }

    });
})




function checkPiat(radio) {
    var piatYesRadio = document.getElementById("piat_yes");
    var piatNoRadio = document.getElementById("piat_no");
    var submitButton = document.getElementById("next_foam");
    console.log(submitButton);

    if (radio.value === "OH") {
        piatYesRadio.disabled = false;
        piatYesRadio.checked = true;
        piatNoRadio.disabled = true;
        submitButton.style.display = "block ";
        // submitButton.value = "next";
    } else if (radio.value === "UG") {
        piatNoRadio.disabled = false;
        piatNoRadio.checked = true;
        piatYesRadio.disabled =true;
        submitButton.style.display = "none ";
        // submitButton.value = "submit";
    }
  }





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
    var isValid = true
    for(var i = 0 ; i < class_error.length ; i++){      
        class_error[i].parentNode.parentNode.firstElementChild.lastElementChild.innerHTML = class_error[i].value === ""?  "This feild is required" : "";
        if(isValid){
        isValid =  class_error[i].value === ""? false : true
        }
    }   


    var jenisSn = document.getElementsByName("jenis_sn");
    var jenisSambungan = document.getElementsByName("jenis_sambungan");   

     
    if (!isRadioSelected(jenisSn)) {
        jenisSn[0].parentNode.parentNode.firstElementChild.lastElementChild.innerHTML ="This feild is required";
        isValid = false;
    }else{
        jenisSn[0].parentNode.parentNode.firstElementChild.lastElementChild.innerHTML = ""
    }

    if (!isRadioSelected(jenisSambungan)) {
        jenisSambungan[0].parentNode.parentNode.firstElementChild.lastElementChild.innerHTML =  "This feild is required";
        isValid = false;
    }else{
        jenisSambungan[0].parentNode.parentNode.firstElementChild.lastElementChild.innerHTML = ""
    }

    if(golbal_isVaild === true){
      return isValid
    }
    return golbal_isVaild;
  }

  function isRadioSelected(radioButtons) {
    for (var i = 0; i < radioButtons.length; i++) {
        if (radioButtons[i].checked) {
        return true;
        }
    }
    return false;
  }


  function getSnDetail(event){
    $.ajax({
        url: "./services/getSNDetail.php?sn="+event.value,
        type: "GET",
        dataType: "json",
        success: function(response) {
            if(response.data.jenis_sambungan == "OH"){
                document.getElementById("jenis_sambungan_oh").checked = true
                document.getElementById("piat_yes").checked = true
              
            }else{
                document.getElementById("jenis_sambungan_ug").checked = true
                document.getElementById("piat_no").checked = true
            }
            let ba =   $("#ba option:first");
            ba.val(response.data.ba);
            ba.text(response.data.ba);
            $("#id").val(response.data.id)
           
        },
        error: function(xhr, status, error) {
            console.log("Error: " + error);
        }
    });
}