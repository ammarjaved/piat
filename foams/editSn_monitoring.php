<?php
include 'header.php';

?>



<div class="container shadow p-5  my-5 bg-white foam-1">


    <h3 class="text-center">PIAT CHECKLIST LV OVERHEAD</h3>
    <form action="./services/submitSnMonitoring.php" method="post" onsubmit="return submitFoam()">
        <div class="table-responsive table-bordered" style="overflow-y:auto ; "> <!-- TABLE # 1 -->
            <table class="table">
                <thead>
                    <th colspan="2" class="text-center">SN Monitoring</th>
                </thead>
                <tbody>
                    <tr>
                        <th>BA<br> <span class="text-danger"></span></th>
                        <td><select type="text" name="ba" id="ba" class="form-select required">
                        <option value="" hidden> Select BA</option>
                    <option value="KLB - 6121">KLB - 6121</option>
                    <option value="KLT - 6122">KLT - 6122</option>
                    <option value="KLP - 6123">KLP - 6123</option>
                    <option value="KLS - 6124">KLS - 6124</option>
                    </select>
                    </td>
                    </tr>
                    <tr>
                        <th>Description<br> <span class="text-danger"></span></th>
                        <td><input type="text" name="description" id="description" class="form-control required"></td>
                    </tr>
                    <tr>
                        <th>User status<br> <span class="text-danger"></span></th>
                        <td><input type="text" name="user_status" id="user_status" class="form-control required"></td>
                    </tr>
                    <tr>
                        <th>Notifictn type<br> <span class="text-danger"></span></th>
                        <td><input type="text" name="notifictn_type" id="notifictn_type" class="form-control required"></td>
                    </tr>
                    <tr>
                        <th>SN Number<br> <span class="text-danger"></span></th>
                        <td><span class="text-danger" id="sn_exits"></span>
                <input type="text" name="sn_number" id="sn_number" class="form-control required" onchange="handleKeyPress(event)"></td>
                    </tr>
                    <tr>
                        <th>Priority<br> <span class="text-danger"></span></th>
                        <td><input type="text" name="priority" id="priority" class="form-control required"></td>
                    </tr>
                    <tr>
                        <th>CSP Paid Date<br> <span class="text-danger"></span></th>
                        <td><input type="date" name="csp_paid_date" id="csp_paid_date" class="form-control required"></td>
                    </tr>
                    <tr>
                        <th>Aging (days)<br> <span class="text-danger"></span></th>
                        <td><input type="number" name="aging_days" id="aging_days" class="form-control required"></td>
                    </tr>
                    <tr>
                        <th>Jenis Kerja<br> <span class="text-danger"></span></th>
                        <td><input type="text" name="jenis_kerja" id="jenis_kerja" class="form-control required"></td>
                    </tr>
                    <tr>
                        <th>PIC/Vendor<br> <span class="text-danger"></span></th>
                        <td><input type="text" name="pic_vendor" id="pic_vendor" class="form-control required"></td>
                    </tr>
                    <tr>
                        <th>Type<br> <span class="text-danger"></span></th>
                        <td><input type="text" name="type" id="type" class="form-control required"></td>
                    </tr>
                    <tr>
                        <th>Remark<br> <span class="text-danger"></span></th>
                        <td><input type="text" name="remark" id="remark" class="form-control required"></td>
                    </tr>
                    <tr>
                        <th>Date Complete<br> <span class="text-danger"></span></th>
                        <td><input type="date" name="date_complete" id="date_complete" class="form-control required"></td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="text-center mt-b">
           
            <button type="submit" class="btn btn-sm btn-success m-3">Submit</button>
        </div>
    </form>
</div>
<script src="../assets/js/snMonitoring.js"></script>
</body>

</html>
