<?php 

 

$stmt = $pdo->prepare("SELECT a.klb_count , b.klt_count, c.klp_count , d.kls_count FROM (SELECT count(*) as klb_count FROM ad_service_qr WHERE ba = 'KLB - 6121' AND status = :status)a,
(SELECT count(*) as klt_count FROM ad_service_qr WHERE ba = 'KLT - 6122' AND status = :status)b,
(SELECT count(*) as klp_count FROM ad_service_qr WHERE ba = 'KLP - 6123' AND status = :status)c,
(SELECT count(*) as kls_count FROM ad_service_qr WHERE ba = 'KLS - 6124' AND status = :status)d");

$status = "Inprocess";
$stmt->bindParam(':status',$status);

$stmt->execute();

$count = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<div class="row text-center">
    <div class="col-md-1"></div>
    <div class="col-md-2 c bg-light m-2 p-2" style="background-color:  #14DFE4 !important;">
        <p style="font-weight: 600;">Total  Pending KLB </p>
        <div class="text-center"><?php echo $count['klb_count']?></div>
    </div>
    <div class="col-md-2 bg-light m-2 p-2" style="background-color:  #14DFE4 !important;">
        <p style="font-weight: 600;">Total  Pending KLT </p>
        <div class="text-center"><?php echo $count['klt_count']?></div>
    </div>
    <div class="col-md-2 bg-light m-2 p-2" style="background-color:  #14DFE4 !important;">
        <p style="font-weight: 600;">Total  Pending KLP </p>
        <div class="text-center"><?php echo $count['klp_count']?></div>
    </div>
    <div class="col-md-2 bg-light m-2 p-2" style="background-color:  #14DFE4 !important;">
        <p style="font-weight: 600;">Total Pending KLS </p>
        <div class="text-center"><?php echo $count['kls_count']?></div>
    </div>
</div>


