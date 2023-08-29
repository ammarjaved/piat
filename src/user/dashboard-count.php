<?php 

 

$stmt = $pdo->prepare("SELECT a.count ,b.complete_count, c.inprocess_count , d.kiv_piat FROM 
(SELECT count(*) as count FROM ad_service_qr WHERE ba = :ba  )a,
(SELECT count(*) as complete_count FROM ad_service_qr WHERE ba = :ba AND status = 'Complete' OR ba=:ba AND status = '1')b,
(SELECT count(*) as inprocess_count FROM ad_service_qr WHERE ba = :ba AND status = 'Inprocess' )c,
(SELECT count(*) as kiv_piat FROM ad_service_qr WHERE ba = :ba AND status = 'KIV')d");


$status = "Inprocess";
$stmt->bindParam(':ba', $_SESSION['user_ba']);

$stmt->execute();

$count = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<div class="row text-center m-4">
    
    <div class="col-md-12   " >
    <div class="mb-0 m-2  p-1" style="background-color:  #14DFE4 !important;">
        <p style="font-weight: 600; " class="mb-2"> Total  </p>
        <div class="text-center"><?php echo $count['count']?></div></div>
    </div>
    <div class="col-md-4 " >
    <div class=" m-2 p-1" style="background-color:  #14DFE4 !important;">
        <p style="font-weight: 600;">Total  Complete </p>
        <div class="text-center"><?php echo $count['complete_count']?></div>
    </div>
    </div>

    <div class="col-md-4 ">
    <div class=" mx-1 m-2  p-1" style="background-color:  #14DFE4 !important;">
        <p style="font-weight: 600;">Total  INPROCESS </p>
        <div class="text-center"><?php echo $count['inprocess_count']?></div>
    </div>
    </div>
    <div class="col-md-4 ">
    <div class="ml-0 m-2  p-1" style="background-color:  #14DFE4 !important;">
        <p style="font-weight: 600;">Total  KIV </p>
        <div class="text-center"><?php echo $count['kiv_piat']?></div>
    </div>
    </div>

        
</div>


