<?php
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    session_start();


    $piatDate = isset($_POST["piat_date"]) && $_POST["piat_date"] !== "" ? $_POST["piat_date"] : null;
    $projectNo = isset($_POST["project_no"]) ? $_POST["project_no"] : null;
    $projectName = isset($_POST["project_name"]) ? $_POST["project_name"] : null;
    $feederCircuit = isset($_POST["feeder_circuit"]) ? $_POST["feeder_circuit"] : null;
    $feederCircuitFrom = isset($_POST["feeder_circuit_from"]) && $_POST["feeder_circuit_from"] !== "" ? $_POST["feeder_circuit_from"] : null;
    $feederCircuitTo = isset($_POST["feeder_circuit_to"]) && $_POST["feeder_circuit_to"] !== "" ? $_POST["feeder_circuit_to"] : null;
    $feederCircuitLength = isset($_POST["feeder_circuit_length"]) ? $_POST["feeder_circuit_length"] : null;
    $voltageLevel = isset($_POST["voltage_level"]) ? $_POST["voltage_level"] : null;
    $cableType = isset($_POST["cable_type"]) ? $_POST["cable_type"] : null;
    $company = isset($_POST["company"]) ? $_POST["company"] : null;
    $companyName = isset($_POST["company_name"]) ? $_POST["company_name"] : null;
    $companyPhoneNo = isset($_POST["company_phone_no"]) ? $_POST["company_phone_no"] : null;
    $companySign = isset($_POST["company_sign"]) ? $_POST["company_sign"] : null;
    $checklistItems = isset($_POST["check_list"]) ? $_POST["check_list"] : null;
    $result = isset($_POST["result"]) ? $_POST["result"] : null;
    $attemptNo = isset($_POST["attempt_no"]) ? $_POST["attempt_no"] : null;
    $prepareBy = isset($_POST["prepare_by"]) ? $_POST["prepare_by"] : null;
    $prepSign = isset($_POST['prep_sign']) ? $_POST["prep_sign"] : null;

    $companyJson = $company ? json_encode($company) : null;
    $companyNameJson = $companyName ? json_encode($companyName) : null;
    $companyPhoneNoJson = $companyPhoneNo ? json_encode($companyPhoneNo) : null;
    $companySignJson = $companySign ? json_encode($companySign) : null;
    $checklistItemsJson = $checklistItems ? json_encode($checklistItems) : null;
    $sn_id = $_SESSION['sn_id'];
   

        for ($i = 0; $i < 31; $i++) {
            $checklistArry[$i] = [];
            $checklistArry[$i] =  array_key_exists($i, $checklistItems) ? $checklistItems[$i] : '';

        }
        $checklistArryJson = json_encode($checklistArry);


    try {

        $id = isset($_POST['id']) ? $_POST['id'] : '';

        if($id == ''){
 
            $stmt = $pdo->prepare("INSERT INTO public.inspection_checklist
            (piat_date, project_no, project_name, feeder_circuit, feeder_circuit_from, feeder_circuit_to,
            feeder_circuit_length, voltage_level, cable_type, company, company_name, company_phone_no,
            company_sign, inspection_checklist, result, attempt_no, prepare_by, prep_sign, ad_service_id)
            VALUES
            (:piatDate, :projectNo, :projectName, :feederCircuit, :feederCircuitFrom, :feederCircuitTo,
            :feederCircuitLength, :voltageLevel, :cableType, :company, :companyName, :companyPhoneNo,
            :companySign, :inspectionChecklist, :result, :attemptNo, :prepareBy, :prepSign , :serviceId)");
            $stmt->bindParam('serviceId',$sn_id);
        }
        else{
    
            $stmt = $pdo->prepare("UPDATE public.inspection_checklist SET
            piat_date = :piatDate,
            project_no = :projectNo,
            project_name = :projectName,
            feeder_circuit = :feederCircuit,
            feeder_circuit_from = :feederCircuitFrom,
            feeder_circuit_to = :feederCircuitTo,
            feeder_circuit_length = :feederCircuitLength,
            voltage_level = :voltageLevel,
            cable_type = :cableType,
            company = :company,
            company_name = :companyName,
            company_phone_no = :companyPhoneNo,
            company_sign = :companySign,
            inspection_checklist = :inspectionChecklist,
            result = :result,
            attempt_no = :attemptNo,
            prepare_by = :prepareBy,
            prep_sign = :prepSign
            WHERE ad_service_id = :id");
            $stmt->bindParam(':id', $sn_id);
        }
        $stmt->bindParam(':piatDate', $piatDate);
        $stmt->bindParam(':projectNo', $projectNo);
        $stmt->bindParam(':projectName', $projectName);
        $stmt->bindParam(':feederCircuit', $feederCircuit);
        $stmt->bindParam(':feederCircuitFrom', $feederCircuitFrom);
        $stmt->bindParam(':feederCircuitTo', $feederCircuitTo);
        $stmt->bindParam(':feederCircuitLength', $feederCircuitLength);
        $stmt->bindParam(':voltageLevel', $voltageLevel);
        $stmt->bindParam(':cableType', $cableType);
        $stmt->bindParam(':company', $companyJson);
        $stmt->bindParam(':companyName', $companyNameJson);
        $stmt->bindParam(':companyPhoneNo', $companyPhoneNoJson);
        $stmt->bindParam(':companySign', $companySignJson);
        $stmt->bindParam(':inspectionChecklist', $checklistArryJson);
        $stmt->bindParam(':result', $result);
        $stmt->bindParam(':attemptNo', $attemptNo);
        $stmt->bindParam(':prepareBy', $prepareBy);
        $stmt->bindParam(':prepSign',$prepSign);

        $stmt->execute();


        unset($_SESSION['nama_jalan'] );
        unset($_SESSION['span_sum'] );
        unset($_SESSION['user_data'] );
        unset( $_SESSION['no_sn']);
        unset($_SESSION['alamat']);
        unset( $_SESSION['tarikh_siap']);
        unset( $_SESSION['cable_type']);
        if($id != ''){
            unset( $_SESSION['sn_id']);
            unset( $_SESSION['foam']);
        }
     
        $_SESSION['alert'] = 'alert-success';
        $_SESSION['message'] = 'inserted successfully'; 
        // header("Location: foam.php");
       
        // exit();
    } catch (PDOException $e) {
       
        // echo "Error: " . $e->getMessage();
        //  exit();

        $_SESSION['message'] = 'inserted failed'; 
        $_SESSION['alert'] = 'alert-danger';
        header("Location: ../foam.php");
        exit;
        
    }
    $pdo = null;
    header("Location: ../index.php");
}
?>
