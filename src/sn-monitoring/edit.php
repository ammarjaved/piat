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
 
    <form action="../services/submitSnMonitoring.php?id=<?php echo $record['id']; ?>" method="post"
        onsubmit="return submitFoam()">
        <div class="table-responsive table-bordered" style="overflow-y:auto ; "> <!-- TABLE # 1 -->
            <table class="table">
                <thead>
                    <th colspan="3" class="text-center">SN Monitoring</th>
                </thead>
                <tbody>
                    <tr>
                        <th>BA<br> <span class="text-danger"></span></th>
                        <td colspan="2"><select name="ba" id="ba" class="form-select" onchange="getUsersPIC()">
                                <?php if($_SESSION['user_name'] == "admin"){ ?>
                                   <option value="<?php echo  $record['ba'] ?>"><?php echo $record['ba'] ?></option>";
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
                        <th>Alamat<br> <span class="text-danger"></span></th>
                        <td colspan="2"><input type="text" name="alamat" id="description"
                                class="form-control required" value="<?php echo $record['alamat']; ?>" ></td>
                    </tr>
                    <tr>
                        <th>User status<br> <span class="text-danger"></span></th>
                        <td colspan="2"><input type="text" name="user_status" id="user_status"
                                class="form-control required" value="<?php echo $record['user_status']; ?>"></td>
                    </tr>

                    <tr>
                        <th>SN Number<br> <span class="text-danger"></span></th>
                        <td colspan="2"><span class="text-danger" id="sn_exits"></span>
                            <input type="number" name="sn_number" id="sn_number" class="form-control required"
                                onchange="handleKeyPress(event)" value="<?php echo $record['no_sn']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>Jenis SN *<br> <span class="text-danger"></span></th>
                        <td><input type="radio" name="jenis_sn" id="jenis_sn_lkkk" value="LKKK" <?php echo $record['jenis_sn'] == 'LKKK' ? 'checked' : ''; ?>>
                            <label for="jenis_sn_lkkk">LKKK</label>
                        </td>
                        <td><input type="radio" name="jenis_sn" id="jenis_sn_express" value="Express"
                                <?php echo $record['jenis_sn'] == 'Express' ? 'checked' : ''; ?>> <label for="jenis_sn_express">Express</label></td>
                    </tr>
                    <tr>
                        <th>Jenis Sambungan *<br> <span class="text-danger"></span></th>
                        <td> <input type="radio" name="jenis_sambungan" id="jenis_sambungan_oh" value="OH"
                                <?php echo $record['jenis_sambungan'] == 'OH' ? 'checked' : ''; ?>> <label for="jenis_sambungan_oh">OH/Combine Service</label></td>
                        <td><input type="radio" name="jenis_sambungan" id="jenis_sambungan_ug" value="UG"
                                <?php echo $record['jenis_sambungan'] == 'UG' ? 'checked' : ''; ?>> <label for="jenis_sambungan_ug">UG</label></td>
                    </tr>
                    <tr>
                        <th>CSP Paid Date<br> <span class="text-danger"></span></th>
                        <td colspan="2"><input type="date" name="csp_paid_date" id="csp_paid_date"
                                class="form-control required" onchange="getAging(this)" value="<?php echo $record['csp_paid_date']; ?>" max="<?php echo $record['tarikh_siap'] != ''? $record['tarikh_siap'] : date('Y-m-d');  ?>">
                        </td>
                    </tr>
                    <tr>
                    <?php   
                    
                        if ($record['csp_paid_date'] != '') {
                            # code...
                       
                    $agingDateTime = new DateTime( $record['csp_paid_date']);

                    $todayDateTime = $record['tarikh_siap'] != '' ? new DateTime($record['tarikh_siap'])  : new DateTime();


                    $interval = $agingDateTime->diff($todayDateTime);
                    $differenceInDays = $interval->format('%a'); }else{
                        $differenceInDays= 0;
                    } ?>
                        <th>Aging (days)<br> <span class="text-danger"></span></th>
                        <td colspan="2"><input disabled type="number" name="aging_days" id="aging_days"
                                class="form-control required" value="<?php echo $differenceInDays +1 ?>"  ></td>
                    </tr>

                    <tr>
                        <th>PIC *<br> <span class="text-danger"></span></th>
                        <td colspan="2"> 
                                <select name="pic" id="pic" class="form-select required" onchange="getUsersPIC()">
                                    <option value="<?php echo $record['pic']?>"><?php echo $record['pic']?></option>
                                </select>    
                        </td>
                    </tr>

                    <tr>
                        <th>Vendor <br> <span class="text-danger"></span></th>
                        <td colspan="2">
                            <select   name="vendor" id="vendor" class="form-select ">
                                <option value="<?php echo $record['vendor'] !='' ?$record['vendor'] : '' ?>"><?php echo $record['vendor'] !='' ?$record['vendor'] : '' ?></option>

                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th>Remark<br> <span class="text-danger"></span></th>
                        <td colspan="2"><input type="text" name="remark" id="remark"
                                class="form-control" value="<?php echo $record['remark']; ?>"></td>
                    </tr>


                    <tr id="comp_date">
                        <th>Completion Date<br> <span class="text-danger"></span></th>
                        <td colspan="2"><input type="date" name="complete_date" id="complete_date"
                                value="<?php echo $record['tarikh_siap']; ?>" class="form-control" min="<?php echo $record['csp_paid_date']; ?>" >
                        </td>
                    </tr>

                    <tr>
                        <th>Construction Status<br> <span class="text-danger"></span></th>
                        <td colspan="2">
                            <select name="cons_status" id="cons_status" class="form-select">
                                <option value="<?php echo $record['status']; ?>" hidden> <?php echo $record['status']; ?></option>
                                <option value="Inprogress">Inprogress</option>
                                <option value="KIV">KIV</option>
                                <option value="Complete">Complete</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th>ERMS Status</th>
                        <td colspan="2"><input type="checkbox"  id="erms" name="erms" <?php echo $record['erms_status'] == 'done' ?'checked' :'' ?> > <label
                                for="erms"></label></td>
                    </tr>
                    <tr>
                        <th>Work Type</th>
                        <td colspan="2">
                            <input type="radio" name="work_type" id="work_type_less_then_3_poles" value="less then 3 poles" <?php echo $record['work_type'] == 'less then 3 poles' ?'checked' :''?> > 
                            <label for="work_type_less_then_3_poles">less then 3 poles</label>
                            <br>
                            <input type="radio" name="work_type" id="work_type_more_then_3_poles" value="more then 3 poles" <?php echo $record['work_type'] == 'more then 3 poles' ?'checked' :''?>> <label for="work_type_more_then_3_poles">more then 3 poles</label>
                            <br>
                            <input type="radio" name="work_type" id="work_type_ug_without_permit" value="ug without permit" <?php echo $record['work_type'] == 'ug without permit' ?'checked' :''?>> <label for="work_type_ug_without_permit">ug without permit</label>
                            <br>
                            <input type="radio" name="work_type" id="work_type_ug_with_permit" value="ug with permit" <?php echo $record['work_type'] == 'ug with permit' ?'checked' :''?>> <label for="work_type_ug_with_permit">ug with permit</label>
                            <br>
                            <input type="radio" name="work_type" id="work_type_meter_only" value="meter only" <?php echo $record['work_type'] == 'meter only' ?'checked' :''?>> <label for="work_type_meter_only">meter only</label>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="text-center mt-b">
            <a href="../index.php"><button type="button" class="btn btn-sm btn-primary"> GO BACK</button></a>
            <button type="submit" class="btn btn-sm btn-success m-3">Submit</button>
        </div>
    </form>
</div>
<script src="../../assets/js/foam-1.js"></script>
<script >

    $(document).ready(function()
    {

        $("#cons_status").on("change",function(){

            if(this.value === "Complete"){
                 $("#complete_date").hasClass('required') ? '' : $("#complete_date").addClass('required')
            }else{
                $("#complete_date").hasClass('required') ? $("#complete_date").removeClass('required') :''
            }
        })


        $('input[name="jenis_sambungan"').on("change", function() {

            var select = $('#cons_status').find('option[value="Complete"]');

            if (this.value === "UG") 
            {
                $('#comp_date').html(`<th>Completion Date<br> <span class="text-danger"></span></th>
                        <td colspan="2"><input type="date" name="complete_date" id="complete_date" class="form-control" onchange="changestatus()" min="<?php echo date('Y-m-d'); ?>">
                        </td>`);
                
                if (select.length < 1) {
                    $('#cons_status').prepend('<option value="Complete">Complete</option>')
                }
            } else {
                $('#comp_date').html('')
                if (select.length > 0) {
                    select.remove();
                }
            }
        })


        $("#complete_date").on("change",function(){
            $("#cons_status").val("Complete");
        })

        $("#no_sn").on("change",function () {
            getSnDetail(this)
        })


    
        onLoad()

 
        getUsersPIC()
        getVendors()

    })

    function onLoad() {
        var jenis = "<?php echo $record['jenis_sambungan']?>"
        if (jenis == 'OH' && $('#complete_date').val() == '') {
            $('#comp_date').html('')
        }
    }

    // function ermsChecked(event){
    //     alert("ASd");
    //     if (event.checked) {
    //             $("#erms1").val('done')

    //         } else {
    //             $("#erms1").val('pending')
    //         }
    // }
</script>
</body>

</html>
