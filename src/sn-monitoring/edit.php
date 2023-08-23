<?php
include '../partials/header.php';
include '../services/connection.php';

$stmt = $pdo->prepare('SELECT * FROM public.ad_service_qr WHERE no_sn = :sn');
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
            <li class="breadcrumb-item"><a href="../index.php">index</a></li>
            <li class="breadcrumb-item active" aria-current="page">edit sn monitoring</li>
        </ol>
    </nav>
</div>

<div class="container shadow p-5  my-5 bg-white foam-1 mt-2">


    <h3 class="text-center">PIAT CHECKLIST LV OVERHEAD</h3>
    <form action="../services/submitSnMonitoring.php?id=<?php echo $record['id'] ?>" method="post" onsubmit="return submitFoam()">
        <div class="table-responsive table-bordered" style="overflow-y:auto ; "> <!-- TABLE # 1 -->
            <table class="table">
                <thead>
                    <th colspan="3" class="text-center">SN Monitoring</th>
                </thead>
                <tbody>
                    <tr>
                        <th>BA<br> <span class="text-danger"></span></th>
                        <td colspan="2"><select type="text" name="ba" id="ba" class="form-select required">
                                <option value="<?php echo $record['ba'] != '' ? $record['ba'] : ''; ?>" hidden><?php echo $record['ba'] != '' ? $record['ba'] : 'Select Ba'; ?></option>
                                <option value="KLB - 6121">KLB - 6121</option>
                                <option value="KLT - 6122">KLT - 6122</option>
                                <option value="KLP - 6123">KLP - 6123</option>
                                <option value="KLS - 6124">KLS - 6124</option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th>Alamat<br> <span class="text-danger"></span></th>
                        <td colspan="2"><input type="text" name="description" id="description" class="form-control required"
                                value="<?php echo $record['alamat']; ?>"></td>
                    </tr>
                    <tr>
                        <th>User status<br> <span class="text-danger"></span></th>
                        <td colspan="2"><input type="text" name="user_status" id="user_status" class="form-control required"
                                value="<?php echo $record['user_status']; ?>"></td>
                    </tr>
                 
                    <tr>
                        <th>SN Number<br> <span class="text-danger"></span></th>
                        <td colspan="2"><span class="text-danger" id="sn_exits"></span>
                            <input type="text" name="sn_number" id="sn_number" class="form-control required"
                                onchange="handleKeyPress(event)" value="<?php echo $record['no_sn']; ?>">
                        </td>
                    </tr>
                    <tr>
                <th>Jenis SN *<br> <span class="text-danger"></span></th>
                <td ><input type="radio" name="jenis_sn" id="jenis_sn_lkkk" value="LKKK" <?php echo $record['jenis_sn'] == "LKKK" ? 'checked' : ''; ?>> <label for="jenis_sn_lkkk">LKKK</label></td>
                <td ><input type="radio" name="jenis_sn" id="jenis_sn_express" value="Express" <?php echo $record['jenis_sn'] == "Express" ? 'checked' : ''; ?>> <label for="jenis_sn_express">Express</label></td>
            </tr>
            <tr>
                <th>Jenis Sambungan *<br> <span class="text-danger"></span></th>
                <td > <input type="radio" name="jenis_sambungan" id="jenis_sambungan_oh" value="OH"   <?php echo $record['jenis_sambungan'] == "OH" ? 'checked' : ''; ?>> <label for="jenis_sambungan_oh">OH/Combine Service</label></td>
                <td ><input type="radio" name="jenis_sambungan" id="jenis_sambungan_ug" value="UG"  <?php echo $record['jenis_sambungan'] == "UG" ? 'checked' : ''; ?>> <label for="jenis_sambungan_ug">UG</label></td>
            </tr>
                    <tr>
                        <th>CSP Paid Date<br> <span class="text-danger"></span></th>
                        <td colspan="2"><input type="date" name="csp_paid_date" id="csp_paid_date" class="form-control required" onchange="getAging(this)"
                                value="<?php echo $record['csp_paid_date']; ?>"></td>
                    </tr>
                    <tr>
                        <th>Aging (days)<br> <span class="text-danger"></span></th>
                        <td colspan="2"><input type="number" name="aging_days" id="aging_days" class="form-control required"
                                value="<?php echo $record['aging_days']; ?>"></td>
                    </tr>
                
                    <tr>
                        <th>PIC/Vendor<br> <span class="text-danger"></span></th>
                        <td colspan="2"><input type="text" name="pic_vendor" id="pic_vendor" class="form-control required"
                                value="<?php echo $record['pic_vendor']; ?>"></td>
                    </tr>
                  
                    <tr>
                        <th>Remark<br> <span class="text-danger"></span></th>
                        <td colspan="2"><input type="text" name="remark" id="remark" class="form-control required"
                                value="<?php echo $record['remark']; ?>"></td>
                    </tr>
                     

                </tbody>
            </table>
        </div>

        <div class="text-center mt-b">

            <button type="submit" class="btn btn-sm btn-success m-3">Submit</button>
        </div>
    </form>
</div>
<script src="../../assets/js/sn-monitoring.js"></script>
</body>

</html>
