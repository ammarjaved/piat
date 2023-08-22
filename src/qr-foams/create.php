<?php 
include '../partials/header.php';

include '../services/connection.php';

$stmt = $pdo->prepare("SELECT no_sn FROM public.ad_service_qr where status = :stat and tarikh_siap is null");
$stat = 'Inprocess';  // Set the value you want to bind
$piat = "";
$stmt->bindParam(':stat', $stat);
// $stmt->bindParam(':piat', $piat);

$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>
 

<div class="d-flex justify-content-end">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="../index.php">index</a></li>
    <li class="breadcrumb-item active" aria-current="page">add qr</li>
  </ol>
</nav>
</div>
<div class="container shadow p-5  my-5 bg-white foam-1"> 
<?php
       


      if (isset($_SESSION['message'])) {
          echo '<div class="alert ' . $_SESSION['alert'] . ' text-center" role="alert">';
          echo $_SESSION['message'];
          echo '<button type="button" class="close btn" onclick="this.parentNode.style.display = \'none\'">';
          echo '<span aria-hidden="true">&times;</span>';
          echo '</button>';
          echo '</div>';

          unset($_SESSION['message']);
          unset($_SESSION['alert']); 
      }
    ?>
<h3 class="text-center">AD Service QR 2023</h3> 
 <form action="../services/submit-foam-1.php" method="post" onsubmit="return submitFoam()">
    <div class="table-responsive table-bordered" style="overflow-y:auto ; ">                      <!-- TABLE # 1 -->
      <table class="table caption-top">  
      <caption class="text-sm font-medium text-gray-500 mb-2 text-left">Section A </caption>
            <thead>
                <th colspan="3" class="text-center">Section A</th>
            </thead>
            <tbody>
            <tr>
              <th class="col-md-6">BA *<br> <span class="text-danger"></span></th>
              <td  colspan="2">
                
                <select name="ba" class="form-select required" id="ba" style="border:1px solid black">
                    <option value="" hidden> Select BA</option>
                    <option value="KLB - 6121">KLB - 6121</option>
                    <option value="KLT - 6122">KLT - 6122</option>
                    <option value="KLP - 6123">KLP - 6123</option>
                    <option value="KLS - 6124">KLS - 6124</option>
                </select>
             </td>
            </tr>
            <tr>
                <th>Jenis SN *<br> <span class="text-danger"></span></th>
                <td ><input type="radio" name="jenis_sn" id="jenis_sn_lkkk" value="LKKK"> <label for="jenis_sn_lkkk">LKKK</label></td>
                <td ><input type="radio" name="jenis_sn" id="jenis_sn_express" value="Express"> <label for="jenis_sn_express">Express</label></td>
            </tr>
            <tr>
                <th>Jenis Sambungan *<br> <span class="text-danger"></span></th>
                <td > <input type="radio" name="jenis_sambungan" id="jenis_sambungan_oh" value="OH" onclick="checkPiat(this)"> <label for="jenis_sambungan_oh">OH/Combine Service</label></td>
                <td ><input type="radio" name="jenis_sambungan" id="jenis_sambungan_ug" value="UG" onclick="checkPiat(this)"> <label for="jenis_sambungan_ug">UG</label></td>
            </tr>
            <tr>
                <th>No. SN *<br> <span class="text-danger"></span></th>
                <td colspan="2">
                    <input type="hidden" id="id" name="id">

                <span class="text-danger" id="sn_exits"></span>
                <select name="no_sn" id="no_sn" class="form-select" onchange="getSnDetail(this)">
    <option value="" hidden>Select SN</option>
    <?php 
    foreach ($records as $record) {
        echo "<option value='{$record['no_sn']}'>{$record['no_sn']}</option>";
    }
    ?>
</select>

               
            </tr>
            <tr>
                <th>Alamat *<br> <span class="text-danger"></span></th>
                <td colspan="2"><input type="text" name="alamat" id="alamat" class="form-control required"></td>
            </tr>
            <tr>
                <th>Tarikh Siap *<br> <span class="text-danger"></span></th>
                <td colspan="2"><input type="date" name="tarikh_siap" id="tarikh_siap" class="form-control required"></td>
            </tr>
            <tr>
                <th>PIAT *<br> <span class="text-danger"></span></th>
                <td > <input type="radio" name="piat" id="piat_yes" value="yes"> <label for="piat_yes">Yes</label></td>
                <td ><input type="radio" name="piat" id="piat_no" value="no"> <label for="piat_no">No</label></td>
            </tr>
            <tr>
                <th>Nama Pencawang / Nama Feeder Pillar <br> <span  style="font-family: Harlow Solid Italic !important; font-size:13px">Jika LKKK Sahaja </span> <span class="text-danger"></span> <br> <span class="text-danger"></span></th>
                    <td colspan="2"> <input type="text" name="nama_feeder_pillar" id="nama_feeder_pillar" class="form-control "></td>
            </tr>
            <tr>
                <th>Nama Feeder / Nama Jalan <br> <span  style="font-family: Harlow Solid Italic !important; font-size:13px">Jika LKKK Sahaja <br></span> <span class="text-danger"></span></th>
                <td colspan="2" class="align-middle"><input type="text" name="nama_jalan" id="nama_jalan" class="form-control "></td>
            </tr>
             
            </tbody>

        </table>
    </div>            

    
                                                         <!-- TABLE # 2 -->

    <div class="table-responsive table-bordered" style="overflow-y:auto ; ">                     
        <table class="table caption-top">
            <caption class="text-sm font-medium text-gray-500 mb-2 text-left">Section B</caption>
            <thead>
                <th colspan="3" class="text-center">Seksyen</th>
            </thead>
            <tr>
                <th class="col-md-6">Dari (No Tiang / Nama Feeder)</th>
                <td colspan="2"><input type="text" name="seksyen_dari" id="seksyen_dari" class="form-control"></td>
            </tr>

            <tr>
                <th>Ke (No Tiang/ Nama Feeder/ No Rumah/ No Kedai)</th>
                <td colspan="2"><input type="text" name="seksyen_ke" id="seksyen_ke" class="form-control"></td>
            </tr>
        </table>
    </div>




                                                             <!-- TABLE # 3 -->


    <div class="table-responsive table-bordered" style="overflow-y:auto ; ">                     
        <table class="table caption-top">
            <caption class="text-sm font-medium text-gray-500 mb-2 text-left">Section C</caption>
            <thead>
                <th colspan="3" class="text-center">Bil Saiz Tiang</th>
            </thead>

            <tr>
                <th class="col-md-6">Bil Saiz Tiang 7.5</th>
                <td ><input type="number" name="bil_saiz_tiang_a" id="bil_saiz_tiang_a" class="form-control"></td>
            </tr>


            <tr>
                <th>Bil Saiz Tiang 9</th>
                <td ><input type="number" name="bil_saiz_tiang_b" id="bil_saiz_tiang_b" class="form-control"></td>
            </tr>
            <tr>
                <th>Bil Saiz Tiang 10</th>
                <td ><input type="number" name="bil_saiz_tiang_c" id="bil_saiz_tiang_c" class="form-control"></td>
            </tr>
        </table>
    </div>




                                                            <!-- TABLE # 4 -->


    <div class="table-responsive table-bordered" style="overflow-y:auto ; ">                     
        <table class="table caption-top">
            <caption class="text-sm font-medium text-gray-500 mb-2 text-left"> Section D</caption>
            <thead>
                <th colspan="3" class="text-center">Bil Jenis Tiang</th>
            </thead>

            <tr>
                <th>Span</th>
                <td ><input name="bil_jenis_spun" id="bil_jenis_spun" class="form-control" type="number"></td>
            </tr>
            <tr>
                <th>Konkrit</th>
                <td ><input name="bil_jenis_konkrit" id="bil_jenis_konkrit" class="form-control" type="number"></td>
            </tr>
            <tr>
                <th>Besi</th>
                <td ><input name="bil_jenis_besi" id="bil_jenis_besi" class="form-control" type="number"></td>
            </tr>
            <tr>
                <th>Kayu</th>
                <td ><input name="bil_jenis_kayu" id="bil_jenis_kayu" class="form-control" type="number"></td>
            </tr>
            

        </table>
    </div>

                                                                <!-- TABLE # 5 -->


    <div class="table-responsive table-bordered" style="overflow-y:auto ; ">                     
        <table class="table caption-top">
            <caption class="text-sm font-medium text-gray-500 mb-2 text-left">Section E</caption>
            <thead>
                <th colspan="3" class="text-center">ABC (Span)</th>
            </thead>

            <tr>
                <th>3 X 185</th>
                <td ><input name="abc_span_a" id="abc_span_a" class="form-control" type="number"></td>
            </tr>

            <tr>
                <th>3 X 95</th>
                <td ><input name="abc_span_b" id="abc_span_b" class="form-control" type="number"></td>
            </tr>

            <tr>
                <th>3 X 16</th>
                <td ><input name="abc_span_c" id="abc_span_c" class="form-control" type="number"></td>
            </tr>

            <tr>
                <th>1 X 16</th>
                <td ><input name="abc_span_d" id="abc_span_d" class="form-control" type="number"></td>
            </tr>

        </table>
    </div>

                                                                <!-- TABLE # 6 -->


    <div class="table-responsive table-bordered" style="overflow-y:auto ; ">                     
        <table class="table caption-top">
            <caption class="text-sm font-medium text-gray-500 mb-2 text-left">Section F</caption>
            <thead>
                <th colspan="3" class="text-center">PVC (Span)</th>
            </thead>
            <tr>
                <th>19/064</th>
                <td ><input name="pvc_span_a" id="pvc_span_a" class="form-control" type="number"></td>
            </tr>
            <tr>
                <th>7/083 </th>
                <td ><input name="pvc_span_b" id="pvc_span_b" class="form-control" type="number"></td>
            </tr>
            <tr>
                <th>7/044</th>
                <td ><input name="pvc_span_c" id="pvc_span_c" class="form-control" type="number"></td>
            </tr>
        </table>
    </div>

                                                                <!-- TABLE # 7 -->


    <div class="table-responsive table-bordered" style="overflow-y:auto ; ">                     
        <table class="table caption-top">
            <caption class="text-sm font-medium text-gray-700 mb-2 text-left">Section G</caption>
            <thead>
                <th colspan="3" class="text-center">BARE (Span)</th>
            </thead>
            <tr>
                <th>7/173</th>
                <td ><input name="bare_span_a" id="bare_span_a" class="form-control" type="number"></td>
            </tr>
            <tr>
                <th>7/122</th>
                <td ><input name="bare_span_b" id="bare_span_b" class="form-control" type="number"></td>
            </tr>
            <tr>
                <th>3/132</th>
                <td ><input name="bare_span_c" id="bare_span_c" class="form-control" type="number"></td>
            </tr>
        </table>
    </div>

    <div class="table-responsive table-bordered" style="overflow-y:auto ; ">                     
        <table class="table caption-top">
            <caption class="text-sm font-medium text-gray-700 mb-2 text-left">Section H</caption>
            <thead>
                <th colspan="3" class="text-center">Section H</th>
            </thead>

            <tr>
                <th>Jumlah Span<br> <span class="text-danger"></span></th>
                <td colspan="2"><input type="number" name="jumlah_span" id="jumlah_span" class="form-control"></td>
            </tr>

            <tr>
                <th>Talian Utama (M) / Serbis (S)<br> <span class="text-danger"></span></th>
                <td><input type="radio" name="talian_utama" id="talian_utama_m" value="M" checked> <label for="talian_utama_m"> M</label></td>
                <td><input type="radio" name="talian_utama" id="talian_utama_s" value="S"> <label for="talian_utama_s"> S</label></td>
            </tr>
            <tr>
                <th>Bil Umbang <br> <span class="text-danger"></span></th>
                <td colspan="2"><input type="number" name="bil_umbang" id="bil_umbang" class="form-control"></td>
            </tr>
            <tr>
                <th>Bil Black Box<br> <span class="text-danger"></span></th>
                <td colspan="2"><input type="number" name="bil_black_box" id="bil_black_box" class="form-control"></td>
            </tr>
           <tr>
                <th>Bil LVPT Cap Bank<br> <span class="text-danger"></span></th>
                <td colspan="2"><input type="number" name="bil_lvpt" id="bil_lvpt" class="form-control"></td>
            </tr>
            <tr>
                <th>Bilangan Serbis Melibatkan 1 pengguna sahaja<br> <span class="text-danger"></span></th>
                <td colspan="2"><input type="number" name="bilangan_serbis" id="bilangan_serbis" class="form-control"></td>
            </tr>
            <tr>
                <th>Catatan<br> <span class="text-danger"></span></th>
                <td colspan="2"><input type="text" name="catatan" id="catatan" class="form-control"></td>
            </tr> 

        </table>
    </div>

    <div class="text-center d-flex justify-content-center">
      <button type="submit" class="btn btn-sm btn-success my-3 mx-3" name="submit_button" value="submit">Submit</button>
      <button type="submit" class="btn btn-sm btn-success mx-3 my-3 " style="display: none;"  id="next_foam" name="submit_button" value="next">Next</button>
     </div>
    


    </form>


</div>
<script src="../../assets/js/foam-1.js"></script>

 
</body>
</html>