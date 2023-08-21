<?php
include 'header.php';

?>
<div class="d-flex justify-content-end">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/piat/foams/">index</a></li>
    <li class="breadcrumb-item active" aria-current="page">add sn</li>
  </ol>
</nav>
</div>


<div class="container shadow p-5  my-5 bg-white foam-1 mt-3">


    <h3 class="text-center">SN Monitoring</h3>
    <form action="./services/submitSnMonitoring-1.php" method="post" onsubmit="return submitFoam()">

        <div class="row p-2">
            <div class="col-md-6">
                <label for=""><strong style="font-size: 1rem !important;"> Ba</strong><br> <span class="text-danger"></span></label>
            </div>
            <div class="col-md-6">

                <select type="text" name="ba" id="ba" class="form-select required" style="width: 100% !important; font-size:1rem !important ; padding: 0.375rem 2.25rem 0.375rem 0.75rem !important;">
                    <option value="" hidden> Select BA</option>
                    <option value="KLB - 6121">KLB - 6121</option>
                    <option value="KLT - 6122">KLT - 6122</option>
                    <option value="KLP - 6123">KLP - 6123</option>
                    <option value="KLS - 6124">KLS - 6124</option>
                </select>
            </div>
        </div>
        <div class="row p-2">
            <div class="col-md-6">
                <label for=""><strong style="font-size: 1rem !important;">SN No</strong><br> <span class="text-danger"></span></label>
            </div>
            <div class="col-md-6"><span class="text-danger" id="sn_exits"></span>
                <input type="text" name="sn_no" id="sn_no" class="form-control required"
                    onchange="handleKeyPress(event)" style="width: 100% !important; font-size:1rem !important ; padding: 0.375rem 2.25rem 0.375rem 0.75rem !important;">
            </div>
        </div>
        <div class="row p-2">
            <div class="col-md-6">
                <label for=""><strong style="font-size: 1rem !important;">Jenis Sambungan</strong><br> <span class="text-danger"></span></label>
            </div>
            <div class="col-md-6">

            <select name="jenis_sambungan" id="jenis_sambungan" class="form-select required" style="width: 100% !important; font-size:1rem !important ; padding: 0.375rem 2.25rem 0.375rem 0.75rem !important;">
                <option value="" hidden>Select type</option>
                <option value="OH">OH/Combine Service</option>
                <option value="UG">UG</option>
            </select>

            </div>
        </div>
        <div class="text-center mt-b">

            <button type="submit" class="btn btn-sm btn-success m-3">Submit</button>
        </div>
    </form>
</div>
<script src="../assets/js/snMonitoring.js"></script>
</body>

</html>
