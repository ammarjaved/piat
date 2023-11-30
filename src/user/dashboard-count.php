<?php
$ba = $_SESSION['user_ba'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitButton']) && $_POST['submitButton'] == 'filter') {
    if (!isset($_POST['date_type'])) {
      
      $_SESSION['message'] = 'inserted failed';
       $_SESSION['alert'] = 'alert-danger';
    }else{
    $from = isset($_POST['from_date']) ? $_POST['from_date'] : '';
    $to = isset($_POST['to_date']) ? $_POST['to_date'] : '';
    $date = '';

    if ($from == '' || $to == '') {
        // if dates are null and only ba is selected then first get min and max date
        $stmt = $pdo->prepare("SELECT MAX(tarikh_siap) AS max_date, MIN(tarikh_siap) AS min_date FROM public.ad_service_qr where tarikh_siap != ''");
        $stmt->execute();
        $comp_date = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("SELECT MAX(csp_paid_date) AS max_date, MIN(csp_paid_date) AS min_date FROM public.ad_service_qr ");
        $stmt->execute();
        $csp_date = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (isset($_POST['date_type'])) {
        if ($_POST['date_type'] == "CSP") {
             $col_name = 'csp_paid_date';
             $from = $from == '' ? $comp_date['min_date'] : $from;
             $to = $to == '' ? $comp_date['max_date'] : $to;
        }else if ($_POST['date_type'] == "Completion") {
           $col_name = 'tarikh_siap';
           $from = $from == '' ? $csp_date['min_date'] : $from;
           $to = $to == '' ? $csp_date['max_date'] : $to;
        }else{
           $from_siap = $from == '' ? $comp_date['min_date'] : $from;
             $to_siap = $to == '' ? $comp_date['max_date'] : $to; 
             $from_paid = $from == '' ? $csp_date['min_date'] : $from;
             $to_paid = $to == '' ? $csp_date['max_date'] : $to;
           $col_name = 'both';
        }
     }
  

     if ($col_name == 'both') {

        $stmt = $pdo->prepare("SELECT
        (SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND tarikh_siap >= :from_paid AND tarikh_siap <= :to_paid OR ba LIKE :ba AND csp_paid_date >= :from_siap AND csp_paid_date <= :to_siap) AS count,
        (SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND status = 'Complete'  AND tarikh_siap >= :from_paid AND tarikh_siap <= :to_paid OR status = 'Complete'  AND ba LIKE :ba AND csp_paid_date >= :from_siap AND csp_paid_date <= :to_siap) AS complete_count,
        (SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND status = 'Inprogress' AND tarikh_siap >= :from_paid AND tarikh_siap <= :to_paid OR  status = 'Inprogress'AND  ba LIKE :ba AND csp_paid_date >= :from_siap AND csp_paid_date <= :to_siap) AS inprocess_count,
        (SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND status = 'KIV' AND tarikh_siap >= :from_paid AND tarikh_siap <= :to_paid OR status = 'KIV' AND ba LIKE :ba AND csp_paid_date >= :from_siap AND csp_paid_date <= :to_siap) AS kiv_piat
    ");
     $stmt->bindParam(':from_paid' ,$from_siap);
     $stmt->bindParam(':to_paid',$to_siap);
     $stmt->bindParam(':from_siap' ,$from_paid);
     $stmt->bindParam(':to_siap',$to_paid);
     $stmt->bindValue(':ba', '%' . $ba . '%', PDO::PARAM_STR);
     $stmt->execute();
 
     }else{

        $stmt = $pdo->prepare("SELECT
        (SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND ".$col_name." >= :from AND ".$col_name." <= :to) AS count,
        (SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND (status = 'Complete' OR status = '1') AND ".$col_name." >= :from AND ".$col_name." <= :to) AS complete_count,
        (SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND status = 'Inprogress' AND ".$col_name." >= :from AND ".$col_name." <= :to) AS inprocess_count,
        (SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND status = 'KIV' AND ".$col_name." >= :from AND ".$col_name." <= :to) AS kiv_piat
    ");
    
    
      $stmt->execute([':ba' => "%$ba%", ':from' => $from, ':to' => $to]);
     }


}
} else {
    $stmt = $pdo->prepare("SELECT
        (SELECT COUNT(*) FROM ad_service_qr WHERE ba = :ba) AS count,
        (SELECT COUNT(*) FROM ad_service_qr WHERE ba = :ba AND (status = 'Complete' OR status = '1')) AS complete_count,
        (SELECT COUNT(*) FROM ad_service_qr WHERE ba = :ba AND status = 'Inprogress') AS inprocess_count,
        (SELECT COUNT(*) FROM ad_service_qr WHERE ba = :ba AND status = 'KIV') AS kiv_piat
    ");
    $stmt->bindParam(':ba', $_SESSION['user_ba']);
    $stmt->execute();
}


$count = $stmt->fetch(PDO::FETCH_ASSOC);
 
?>

 


<div class="row text-center m-4">

    <div class="col-md-12   " onclick="adminSearch('<?php echo $_SESSION['user_ba'] ?>', '')" style="cursor: pointer;">
      
        <div class="mb-0 m-2  p-1" style="background-color:  #14DFE4 !important;">
            <p style="font-weight: 600; " class="mb-2"> Total </p>
            <div class="text-center"><?php echo $count['count']; ?></div>
        </div>
    </div>
    <div class="col-md-4 " onclick="adminSearch('<?php echo $_SESSION['user_ba'] ?>','Complete')" style="cursor: pointer;">
    
        <div class=" m-2 p-1" style="background-color:  #14DFE4 !important;">
            <p style="font-weight: 600;">Total Complete </p>
            <div class="text-center"><?php echo $count['complete_count']; ?></div>
        </div>
     
    </div>

    <div class="col-md-4 " onclick="adminSearch('<?php echo $_SESSION['user_ba'] ?>','Inprogress')" style="cursor: pointer;">
    
        <div class=" mx-1 m-2  p-1" style="background-color:  #14DFE4 !important;">
            <p style="font-weight: 600;">Total Inprogress </p>
            <div class="text-center"><?php echo $count['inprocess_count']; ?></div>
        </div>
 
    </div>
    <div class="col-md-4 " onclick="adminSearch('<?php echo $_SESSION['user_ba'] ?>','KIV')" style="cursor: pointer;">
 
        <div class="ml-0 m-2  p-1" style="background-color:  #14DFE4 !important;">
            <p style="font-weight: 600;">Total KIV </p>
            <div class="text-center"><?php echo $count['kiv_piat']; ?></div>
        </div>
    
    </div>


</div>
