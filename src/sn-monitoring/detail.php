<?php


include '../partials/header.php';
include '../services/connection.php';


$stmt = $pdo->prepare('SELECT * FROM public.ad_service_qr WHERE no_sn = :sn');
$stmt->bindParam(':sn', $_REQUEST['no_sn']);
$stmt->execute();
$record = $stmt->fetch(PDO::FETCH_ASSOC);

$pdo = null;
if (!$record) {
    header('location: ../index.php');
}
?>

<div class="d-flex justify-content-end">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php">index</a></li>
            <li class="breadcrumb-item active" aria-current="page">detail sn monitoring</li>
        </ol>
    </nav>
</div>

<div class="container shadow p-5  my-5 bg-white foam-1 mt-2">

 
   
        <div class="table-responsive table-bordered" style="overflow-y:auto ; "> <!-- TABLE # 1 -->
            <table class="table">
                <thead>
                    <th colspan="2" class="text-center">SN Monitoring</th>
                </thead>
                <tbody>
                    <tr>
                        <th>BA<br> <span class="text-danger"></span></th>
                        <td>
                                <?php echo $record['ba'] ?>
                        </td>
                    </tr>
                    
                    <tr>
                        <th>Alamat<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['alamat']; ?></td>
                    </tr>
                    <tr>
                        <th>User status<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['user_status']; ?></td>
                    </tr>
                  
                    <tr>
                        <th>SN Number<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['no_sn']; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Jenis SN<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['jenis_sn']; ?></td>
                    </tr>
                    <tr>
                        <th>CSP Paid Date<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['csp_paid_date']; ?></td>
                    </tr>
                    <tr>
                        <th>Aging (days)<br> <span class="text-danger"></span></th>
                        <?php   
                    
                    if ($record['csp_paid_date'] != '') {
                        # code...
                   
                $agingDateTime = new DateTime( $record['csp_paid_date']);

                $todayDateTime = $record['tarikh_siap'] != '' ? new DateTime($record['tarikh_siap'])  : new DateTime();


                $interval = $agingDateTime->diff($todayDateTime);
                $differenceInDays = $interval->format('%a'); }else{
                    $differenceInDays= 0;
                } ?>
 
                        <td><?php echo $differenceInDays +1 ?></td>
                    </tr>
                    <tr>
                        <th>Jenis Sambungan<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['jenis_sambungan']== "OH" ? 'OH/Combine Service':'UG' ?></td>
                    </tr>
                    <tr>
                        <th>PIC<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['pic']; ?></td>
                    </tr>
                    <tr>
                        <th>Vendor<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['vendor']; ?></td>
                    </tr>
                    
                    <tr>
                        <th>Remark<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['remark']; ?></td>
                    </tr>
                     

                    <tr id="comp_date">
                        <th>Completion Date<br> <span class="text-danger"></span></th>
                        <td colspan="2"><?php echo $record['tarikh_siap']; ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Construction Status<br> <span class="text-danger"></span></th>
                        <td colspan="2">
                           <?php echo $record['status']; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>ERMS Status</th>
                        <td colspan="2"><?php echo $record['erms_status']; ?></td>
                    </tr>
                    <tr>
                        <th>Work Type</th>
                        <td colspan="2"><?php echo $record['work_type']; ?></td>
                    </tr>

                </tbody>
            </table>

            <div class="text-center m-3">
            <a href="../index.php" ><button type="button" class="btn btn-sm btn-primary"> GO BACK</button></a>
                <a href="./edit.php?no_sn=<?php echo $record['no_sn'] ?>"> <button class="btn btn-sm btn-success">Update SN</button> </a>
            </div>
        </div>

</div>
<script src="../assets/js/snMonitoring.js"></script>
</body>

</html>
