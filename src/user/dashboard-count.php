<?php
$ba = $_SESSION['user_ba'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitButton']) && $_POST['submitButton'] == 'filter') {
    $from = isset($_POST['from_date']) ? $_POST['from_date'] : '';
    $to = isset($_POST['to_date']) ? $_POST['to_date'] : '';
    $date = '';

    if ($from == '' || $to == '') {
        // if dates are null and only ba is selected then first get min and max date
        $stmt = $pdo->prepare("SELECT MAX(tarikh_siap) AS max_date, MIN(tarikh_siap) AS min_date FROM public.ad_service_qr where tarikh_siap != ''");
        $stmt->execute();
        $date = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $from = $from == '' ? $date['min_date'] : $from;
    $to = $to == '' ? $date['max_date'] : $to;
    // echo $from ;
    // echo $to;

    $stmt = $pdo->prepare("SELECT
    (SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND tarikh_siap >= :from AND tarikh_siap <= :to) AS count,
    (SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND (status = 'Complete' OR status = '1') AND tarikh_siap >= :from AND tarikh_siap <= :to) AS complete_count,
    (SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND status = 'Inprogress' AND tarikh_siap >= :from AND tarikh_siap <= :to) AS inprocess_count,
    (SELECT COUNT(*) FROM ad_service_qr WHERE ba LIKE :ba AND status = 'KIV' AND tarikh_siap >= :from AND tarikh_siap <= :to) AS kiv_piat
");


  $stmt->execute([':ba' => "%$ba%", ':from' => $from, ':to' => $to]);
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

    <div class="col-md-12   " onclick="searchTable('')" style="cursor: pointer;">
      
        <div class="mb-0 m-2  p-1" style="background-color:  #14DFE4 !important;">
            <p style="font-weight: 600; " class="mb-2"> Total </p>
            <div class="text-center"><?php echo $count['count']; ?></div>
        </div>
    </div>
    <div class="col-md-4 " onclick="searchTable('Complete')" style="cursor: pointer;">
    
        <div class=" m-2 p-1" style="background-color:  #14DFE4 !important;">
            <p style="font-weight: 600;">Total Complete </p>
            <div class="text-center"><?php echo $count['complete_count']; ?></div>
        </div>
     
    </div>

    <div class="col-md-4 " onclick="searchTable('Inprogress')" style="cursor: pointer;">
    
        <div class=" mx-1 m-2  p-1" style="background-color:  #14DFE4 !important;">
            <p style="font-weight: 600;">Total Inprogress </p>
            <div class="text-center"><?php echo $count['inprocess_count']; ?></div>
        </div>
 
    </div>
    <div class="col-md-4 " onclick="searchTable('KIV')" style="cursor: pointer;">
 
        <div class="ml-0 m-2  p-1" style="background-color:  #14DFE4 !important;">
            <p style="font-weight: 600;">Total KIV </p>
            <div class="text-center"><?php echo $count['kiv_piat']; ?></div>
        </div>
    
    </div>


</div>
