<?php
include 'header.php';

include 'services/connection.php';
$stmt = $pdo->prepare('SELECT * FROM public.sn_monitoring WHERE sn_number = :sn');
$stmt->bindParam(':sn', $_REQUEST['no_sn']);
$stmt->execute();
$record = $stmt->fetch(PDO::FETCH_ASSOC);

$pdo = null;
if (!$record) {
    header('location: ./index.php');
}
?>

<div class="d-flex justify-content-end">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/piat/foams/">index</a></li>
            <li class="breadcrumb-item active" aria-current="page">detail sn monitoring</li>
        </ol>
    </nav>
</div>

<div class="container shadow p-5  my-5 bg-white foam-1 mt-2">


    <h3 class="text-center">PIAT CHECKLIST LV OVERHEAD</h3>
   
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
                        <th>Description<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['description']; ?></td>
                    </tr>
                    <tr>
                        <th>User status<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['user_status']; ?></td>
                    </tr>
                    <tr>
                        <th>Notifictn type<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['notifictn_type']; ?></td>
                    </tr>
                    <tr>
                        <th>SN Number<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['sn_number']; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Priority<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['priority']; ?></td>
                    </tr>
                    <tr>
                        <th>CSP Paid Date<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['csp_paid_date']; ?></td>
                    </tr>
                    <tr>
                        <th>Aging (days)<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['aging_days']; ?></td>
                    </tr>
                    <tr>
                        <th>Jenis Kerja<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['jenis_kerja']; ?></td>
                    </tr>
                    <tr>
                        <th>PIC/Vendor<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['pic_vendor']; ?></td>
                    </tr>
                    <tr>
                        <th>Type<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['type']; ?></td>
                    </tr>
                    <tr>
                        <th>Remark<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['remark']; ?></td>
                    </tr>
                    <tr>
                        <th>Date Complete<br> <span class="text-danger"></span></th>
                        <td><?php echo $record['date_complete']; ?></td>
                    </tr>

                </tbody>
            </table>
        </div>

</div>
<script src="../assets/js/snMonitoring.js"></script>
</body>

</html>
