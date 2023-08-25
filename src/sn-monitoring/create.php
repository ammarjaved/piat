<?php
include '../partials/header.php';

?>


<div class="d-flex justify-content-end p-3 pb-0">
    <div class="col-6"></div>
    <div class="col-6 d-flex justify-content-end">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php">index</a></li>
            <li class="breadcrumb-item active" aria-current="page">add sn</li>
        </ol>
    </nav></div>
</div>
<div class="container shadow p-5  my-5 bg-white foam-1">


    <h3 class="text-center">PIAT CHECKLIST LV OVERHEAD</h3>
    <form action="../services/submitSnMonitoring.php" method="post" onsubmit="return submitFoam()">
        <div class="table-responsive table-bordered" style="overflow-y:auto ; "> <!-- TABLE # 1 -->
            <table class="table">
                <thead>
                    <th colspan="3" class="text-center">SN Monitoring</th>
                </thead>
                <tbody>
                    <tr>
                        <th>BA *<br> <span class="text-danger"></span></th>
                        <td colspan="2"><select name="searchBA" id="searchBA" class="form-select">
    <?php if($_SESSION['user_name'] == "admin"){ ?>
        <option value="<?php echo isset($_POST['searchBA']) ? $_POST['searchBA'] : ''; ?>" hidden><?php echo isset($_POST['searchBA']) ? $_POST['searchBA'] : 'Select BA'; ?></option>
        <option value="KLB - 6121">KLB - 6121</option>
        <option value="KLT - 6122">KLT - 6122</option>
        <option value="KLP - 6123">KLP - 6123</option>
        <option value="KLS - 6124">KLS - 6124</option>
    <?php } else {
        echo "<option value='{$_SESSION['user_ba']}'>{$_SESSION['user_ba']}</option>";
    }?>
</select>

                    </td>
                    </tr>
                    <tr>
                        <th >Alamat *<br> <span class="text-danger"></span></th>
                        <td colspan="2"><input type="text" name="alamat" id="alamat" class="form-control required"></td>
                    </tr>
                    <tr>
                        <th>User status *<br> <span class="text-danger"></span></th>
                        <td colspan="2"><input type="text" name="user_status" id="user_status" class="form-control required"></td>
                    </tr>
                    <tr>
                        <th>SN Number *<br> <span class="text-danger"></span></th>
                        <td colspan="2"><span class="text-danger" id="sn_exits"></span>
                <input type="text" name="sn_number" id="sn_number" class="form-control required" onchange="handleKeyPress(event)"></td>
                    </tr>
                    <tr>
                <th>Jenis SN *<br> <span class="text-danger"></span></th>
                <td ><input type="radio" name="jenis_sn" id="jenis_sn_lkkk" value="LKKK"> <label for="jenis_sn_lkkk">LKKK</label></td>
                <td ><input type="radio" name="jenis_sn" id="jenis_sn_express" value="Express"> <label for="jenis_sn_express">Express</label></td>
            </tr>
            <tr>
                <th>Jenis Sambungan *<br> <span class="text-danger"></span></th>
                <td > <input type="radio" name="jenis_sambungan" id="jenis_sambungan_oh" value="OH" > <label for="jenis_sambungan_oh">OH/Combine Service</label></td>
                <td ><input type="radio" name="jenis_sambungan" id="jenis_sambungan_ug" value="UG" > <label for="jenis_sambungan_ug">UG</label></td>
            </tr>
                    <tr>
                        <th>CSP Paid Date *<br> <span class="text-danger"></span></th>
                        <td colspan="2"><input type="date" name="csp_paid_date" id="csp_paid_date" class="form-control required" onchange="getAging(this)"></td>
                    </tr>
                    <tr>
                        <th>Aging (days) *<br> <span class="text-danger"></span></th>
                        <td colspan="2"><input type="number" name="aging_days" id="aging_days" class="form-control required"></td>
                    </tr>
                
                    <tr>
                        <th>PIC/Vendor *<br> <span class="text-danger"></span></th>
                        <td colspan="2"><input type="text" name="pic_vendor" id="pic_vendor" class="form-control required"></td>
                    </tr>
                 
                    <tr>
                        <th>Remark *<br> <span class="text-danger"></span></th>
                        <td colspan="2"><input type="text" name="remark" id="remark" class="form-control required"></td>
                    </tr>
                  

                </tbody>
            </table>
        </div>

        <div class="text-center mt-b">
        <a href="../index.php" ><button type="button" class="btn btn-sm btn-primary"> GO BACK</button></a>
            <button type="submit" class="btn btn-sm btn-success m-3">Submit</button>
        </div>
    </form>
</div>
<script src="../../assets/js/foam-1.js"></script>
</body>

</html>