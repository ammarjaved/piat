function submitFoam(){
    var isValid = true;
    var validCheck = true;
    var companyCheck = false;
    var res = document.getElementsByName("result");
    var rows = document.querySelectorAll('input[name^="check_list"]');
    var companyName = document.querySelectorAll('select[name^="company_name"');

        for (let index = 0; index < companyName.length; index++) {
            if(companyName[index].value !== ''){
                companyCheck = true;
            }  
        }

        if (!companyCheck) {
            isValid = false
            $('#company_name_error').html("Select Ateendence");
        }else{
            $('#company_name_error').html("");
        }

        for (let i = 0;  i < rows.length;) {
        
            if (!rows[i].checked && !rows[i+1].checked && !rows[i+2].checked) {
                    validCheck = false;
                }
                i = i+3  ;
        }

        if (!validCheck) {
                $('#check_list_error').html("All check list is required");
                isValid= false;
        }else{
            $('#check_list_error').html("");
        }

        if(!isRadioSelected(res)){
            $("#result").html("Select one ");
            isValid =false;
        }else{
            $("#result").html("");
        }

    return isValid;
}

function isRadioSelected(radioButtons) {
    for (var i = 0; i < radioButtons.length; i++) {
        if (radioButtons[i].checked) {
        return true;
        }
    }
    return false;
}