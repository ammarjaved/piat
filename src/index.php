<?php
session_start();
if (!isset($_SESSION['user_name']) && !isset($_SESSION['user_id'])) {
    header('location:./auth/login.php');
}
include './services/connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.3/datatables.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js"
        integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous">
    </script>

    <title>AD KL SN, QR and PIAT Monitoring</title>

    <style>
        .container.shadow.p-5.my-5.bg-white {
            padding: 20px !important;
        }

        @media only screen and (max-width: 445px) {

            .dataTables_filter input {
                font-size: 0.6rem !important;
            }

            .dataTables_filter label,
            #myTable_length label,
            th {
                font-size: 13px !important;
            }

            td {
                font-size: 12px !important;
            }

            h3 {
                font-size: 19px !important;
            }
        }

        body {
            background: #e9e9e9;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="">AD KL SN, QR and PIAT Monitoring</a>
            <a href="./auth/logout.php" class="btn btn-sm btn-secondary">logout</a>
        </div>
    </nav>
    <div class="container shadow p-5 my-5 bg-white ">

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

        <h3 class="text-center"><?php echo $_SESSION['user_name']; ?></h3>
        <div class="text-end mb-3 d-flex justify-content-end">
            <div class="m-2">

                <a href="./sn-monitoring/create.php" class="btn btn-success btn-sm ">ADD SN</a>
            </div>
            <div class="m-2"><a href="./qr-foams/create.php" class="btn btn-success btn-sm ">ADD QR AND PIAT</a>
            </div>

            <div class="m-2">
                <form action="./services/generateExcel.php" method="POST">
                    <input type="hidden" name="exc_ba" id="exc_ba" value="<?php echo isset($_POST['searchBA']) ? $_POST['searchBA'] : ''; ?>">
                    <input type="hidden" name="exc_from" id="exc_from" value="<?php echo isset($_POST['from_date']) ? $_POST['from_date'] : ''; ?>">
                    <input type="hidden" name="exc_date_type" id="exc_date_type" value="<?php echo isset($_POST['date_type']) ? $_POST['date_type'] : ''; ?>">
                    <input type="hidden" name="exc_to" id="exc_to" value="<?php echo isset($_POST['to_date']) ? $_POST['to_date'] : ''; ?>">
                    <button href="./services/generateExcel.php" class="btn btn-success btn-sm" type="submit">Download
                        Excel</button>
                </form>
            </div>
        </div>
        <form action="" method="post" onsubmit="return searchFoam()">
            <div class="text-end mb-3 row">

                <div class="m-2 col-md-2">
                    <label for="">Select BA :</label> <br>
                    <select name="searchBA" id="searchBA" class="form-select">
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

                </div>
                <div class="m-2 col-md-2">
                    <label for="">Date Type * :</label> <br>
                    <span class="text-danger " id="date_type_error"></span>
                    <select name="date_type" id="date_type" class="form-select">
                        <option value="<?php echo isset($_POST['date_type']) && $_POST['date_type'] != ""? $_POST['date_type'] :'CSP'  ?>" hidden><?php echo isset($_POST['date_type']) && $_POST['date_type'] != "" ? $_POST['date_type'] :'CSP'  ?></option>
                        <option value="CSP">CSP Date</option>
                        <option value="Completion">Completion Date</option>
                    </select>
                </div>
                <div class="m-2 col-md-2">
                    <label for="">From Date :</label> <br>
                    <input type="date" name="from_date" id="from_date" class="form-control"
                        value="<?php echo isset($_POST['from_date']) ? $_POST['from_date'] : ''; ?>">
                </div>
                <div class="m-2 col-md-2">
                    <label for="">To Date :</label> <br>
                    <input type="date" name="to_date" id="to_date" class="form-control"
                        value="<?php echo isset($_POST['to_date']) ? $_POST['to_date'] : ''; ?>">
                </div>
                <div class="col-md-2 pt-2 text-start" style="display: inline">

                    <button class="btn btn-secondary mt-4 btn-sm" type="submit" name='submitButton'
                        value="filter">Filter</button>
                    <button class="btn btn-secondary btn-sm mt-4" type="submit" name='submitButton' onclick="reset()"
                        value="reset">Reset</button>
                </div>

            </div>
        </form>


        <?php if ($_SESSION['user_name'] == 'admin') {
            include './admin/dashboard-count.php';
        }else{
            include './user/dashboard-count.php';
        } ?>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button"
                    role="tab" aria-controls="profile" aria-selected="false">SN
                    Monitoring</button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link " id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                    type="button" role="tab" aria-controls="home" aria-selected="true">QR</button>
            </li>

        </ul>


        <div class="tab-content" id="myTabContent">



            <div class="tab-pane fade  " id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="table-responsive table-bordered py-3" style="overflow-y:auto ; ">
                    <table id="myTable" class="table table-striped table-responsive table-bordered" data-table>
                        <thead>
                            <tr>
                                <?php
                            if ($_SESSION['user_name'] == "admin") { ?>
                                        <th>BA</th>
                                 <?php   } ?>
                                <th>SN NO</th>
                                <th>JENIS SN</th>
                                <th>JENIS SAMBUNGAN</th>
                                <th>CSP DATE</th>
                                <th>COMPLETION DATE</th>
                                
                                <th>CONSTRUCTION STATUS</th>

                                <th>QR</th>
                                <th>PIAT</th>
                                                                <th>ERMS</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['submitButton'] == 'filter') {
                                $ba = isset($_POST['searchBA']) ? $_POST['searchBA'] : '';
                
                                $record = '';
                            
                             
                              
                                     $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr WHERE ba LIKE :ba AND ".$col_name." >= :from AND ".$col_name." <= :to  ORDER BY csp_paid_date DESC");

                               
                                $stmt->execute([':ba' => "%$ba%", ':from' => $from, ':to' => $to]);
                            } else {
                                // without filter
                                if ($_SESSION['user_name'] == 'admin') {
                                    $stmt = $pdo->prepare('SELECT * FROM public.ad_service_qr    ORDER BY id DESC');
                                } else {
                                    $status =  isset($_REQUEST['status']) ? $_REQUEST['status'] :'';
                                    
                                    $stmt = $pdo->prepare('SELECT * FROM public.ad_service_qr WHERE ba LIKE :ba ORDER BY csp_paid_date DESC, id DESC');

                                    // $stmt->bindValue(':created', '%' . $_SESSION['user_id'] . '%', PDO::PARAM_STR);
                                    $stmt->bindValue(':ba', '%' . $_SESSION['user_ba'] . '%', PDO::PARAM_STR);

                                }
                                $stmt->execute();
                            }
                            
                            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            // print_r("<pre>");
                            // print_r($records);
                            // print_r("</pre>");
                            // exit;
                            
                            foreach ($records as $record) {
                                if ($record['jenis_sambungan'] != 'UG') {
                                    # code...
                            
                                    echo '<tr>';
                                    if ($_SESSION['user_name'] == "admin") {
                                        echo "<td>{$record['ba']}</td>";
                                    }
                            
                                    echo "<td><a class='text-decoration-none text-dark' href='./qr-foams/edit.php?no_sn={$record['no_sn']}'>";
                                    echo $record['no_sn'];
                                    echo '</a></td>';
                            
                                    echo "<td>{$record['jenis_sn']}</td>";
                                    echo "<td>{$record['jenis_sambungan']}</td>";
                                    echo "<td>{$record['csp_paid_date']}</td>";
                                    echo "<td>{$record['tarikh_siap']}</td>";
                                    
                                    echo "<td>{$record['status']}</td>";
                                    echo "<td class='text-center'>";
                                 
                                    echo '</td>';
                                    echo "<td class='text-center'>";
                                    if ($record['tarikh_siap'] != '' ) {
                                        echo ' <span class="check" style="font-weight: 600; color: green;">&#x2713;</span>';
                                    } else {
                                        echo '<span class="check" style="font-weight: 600; color: red;">&#x2715;</span>';
                                    }
                            
                                    echo '</td><td class="algin-middle text-center">';
                            
                                    if ($record['piat_status'] == 'true') {
                                        echo '<span class="check " style="font-weight: 600; color: green;">&#x2713;</span>';
                                    } else {
                                        echo '<span class="check" style="font-weight: 600; color: red;">&#x2715;</span>';
                                    }

                                    if ($record['erms_status'] != '' ) {
                                        echo ' <span class="check" style="font-weight: 600; color: green;">&#x2713;</span>';
                                    } else {
                                        echo '<span class="check" style="font-weight: 600; color: red;">&#x2715;</span>';
                                    }
                                    echo '</td>';
                            
                                    echo "<td class='text-center'><div class='dropdown'>
                                                            <button class='btn   ' type='button' id='dropdownMenuButton1' data-bs-toggle='dropdown' aria-expanded='false'>
                                                            <img src='../images/three-dots-vertical.svg'  >
                                                            </button>
                                                            <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton1'>
                                                              <li><a class='dropdown-item' href='./services/generateExcel.php?id={$record['id']}'>Download Excel</a></li>";
                                    if ($record['csp_paid_date'] == '') {
                                       
                                    echo "  <li><a class='dropdown-item' href='./sn-monitoring/edit.php?no_sn={$record['no_sn']}' >Ad CSP date</a></li>";
                                    }else{
                                        echo "<li><a class='dropdown-item' href='./qr-foams/edit.php?no_sn={$record['no_sn']}'>";
                                        echo $record['tarikh_siap'] != '' ? 'Edit QR' : 'Add QR';
                                        echo '</a></li>';
                                        if ($record['piat_status'] == 'true') {
                                            echo "  <li><a class='dropdown-item' href='./generate-pdf/previewPDF.php?no_sn={$record['no_sn']}' target='_blank'>Preview PDF</a></li>";
                                        } elseif ( $record['qr'] ==  'true') {
                                            echo "  <li><a class='dropdown-item' href='./services/foamRedirect.php?sn={$record['no_sn']}'>Fill Checklist</a></li>";
                                        }
                                        echo "  <li><a class='dropdown-item' href='./piat-foam/detail.php?no_sn={$record['no_sn']}'  >Detail</a></li>";
                                    
                                    }
                                    echo "  <li><a class='dropdown-item' href='./sn-monitoring/edit.php?no_sn={$record['no_sn']}' >Edit SN</a></li>";
                                    echo "<li><button type='button' class='dropdown-item' data-bs-toggle='modal' data-sn='{$record['no_sn']}' data-bs-target='#exampleModal'> Delete </button'></li>";
                                    echo '</ul></div></td>';
                                    echo '</tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>

                </div>

            </div>

            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="table-responsive table-bordered py-3" style="overflow-y:auto ; ">
                    <table id="snTable" class="table table-striped table-responsive table-bordered ">
                        <thead>
                            <tr>
                                <th>BA</th>
                                <th>SN NO</th>
                                <th>JENIS SN</th>
                                <th>JENIS SAMBUNGAN</th>
                                <th>AGING (days)</th>
                                <th>CSP DATE</th>
                                <th>COMPLETION DATE</th>
                                <th>CONSTRUCTION STATUS</th>
                                <th>REMARKS</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            
                            // $stmt = $pdo->prepare('SELECT sm.*, asq.status , asq.tarikh_siap
                            // FROM public.sn_monitoring sm
                            // LEFT JOIN public.ad_service_qr asq ON sm.id = asq.sn_monitoring_id;
                            // ');
                            // $stmt->execute();
                            
                            // $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $pdo = null;
                            
                            foreach ($records as $record) {
                               
                                    echo '<tr>';
                                    echo "<td>{$record['ba']}</td>";
                                    echo "<td>{$record['no_sn']}</td>";
                                    echo "<td>{$record['jenis_sn']}</td>";
                                    echo "<td>{$record['jenis_sambungan']}</td>";
                                    if ($record['csp_paid_date'] != '') {
                                 

                                        $agingDateTime = new DateTime( $record['csp_paid_date']);

                                        $todayDateTime = new DateTime();

        
                                        $interval = $agingDateTime->diff(new DateTime());
                                        $differenceInDays = $interval->format('%a');
                                        echo "<td> ".$differenceInDays + 1 ."</td>";
                                    }else{

                                         echo "<td>{$record['aging_days']}</td>";
                                    }
                                   
                                    echo "<td>{$record['csp_paid_date']}</td>";
                                    echo "<td>{$record['tarikh_siap']}</td>";
                                    echo "<td>{$record['status']}</td>";
                                    $remark = $record['remark'];
                                    if($remark){
                                    if (strlen($remark) > 15) {
                                        $remark = substr($remark, 0, 15) . "...";
                                    }
                                    }
                                    echo "<td>{$remark}</td>";

                                    echo "<td class='text-center'><div class='dropdown'>
                                                          <button class='btn   ' type='button' id='dropdownMenuButton1' data-bs-toggle='dropdown' aria-expanded='false'>
                                                          <img src='../images/three-dots-vertical.svg'  >
                                                          </button>
                                                          <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton1'>";
                                 
                                    echo "<li><a class='dropdown-item' href='./sn-monitoring/edit.php?no_sn={$record['no_sn']}'>Edit SN</a></li>";
                            
                                    echo "  <li><a class='dropdown-item' href='./sn-monitoring/detail.php?no_sn={$record['no_sn']}'  >Detail</a></li>";
                                    echo "<li><button type='button' class='dropdown-item' data-bs-toggle='modal' data-sn='{$record['no_sn']}' data-bs-target='#exampleModal'> Delete </button'></li>";
                                    echo "</ul>
                                                                                                  </div></td>";
                                    echo '</tr>';
                                
                            }
                            ?>

                        </tbody>
                    </table>
                </div>



            </div>

        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class=" modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Remove Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="./services/removeSn.php" method="post">
                    <div class="modal-body">
                        Are You Sure ?
                        <input type="hidden" name="sn" id="modal-sn">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Remove</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.3/datatables.min.js"></script>

    <script>
        $(document).ready(function() {

            $('#myTable').DataTable({
                aaSorting:[[3, 'desc']]
            });
            $("#snTable").DataTable({
                aaSorting:[[5, 'desc']]
            })

            $('#searchButton').on('click', function () {
        var searchTerm = $('#searchInput').val(); 
        var table = $('#myTable').DataTable();
        table.search(searchTerm).draw(); 
    });

                $('#exampleModal').on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget);
                    var id = button.data('sn');
                    var modal = $(this);
                    $('#modal-sn').val(id)
                });


   

            reset();
        });

        function reset() {
            var sub = "<?php echo isset($_POST['submitButton']) ? $_POST['submitButton'] : ''; ?>";
            if (sub == 'reset') {
                $('#searchBA').find('option').first().remove();
                $('#searchBA').prepend('<option value="<?php echo $_SESSION['user_ba'] ?>"> <?php echo $_SESSION['user_ba'] == ""? "Select ba" : $_SESSION['user_ba'] ?></option>')
                $('#to_date').val('')
                $('#date_type').prepend('<option value="CSP" hidden selected>CSP</option>')
                $('#from_date').val('')
                $("#exc_ba").val('')
                $('#exc_from').val('')
                $("#exc_to").val('')
            }
        }


        function genrateExcel() {
            var ba = $("#searchBA").val()
            var from = $("#from_date").val()
            var to = $("#to_date").val()

            $("#exc_ba").val(ba)
            $('#exc_from').val(from)
            $("#exc_to").val(to)

            return true
        }

        function searchTable(param) {
            var table = $('#myTable').DataTable();
        table.search(param).draw(); 
        var table2 = $('#snTable').DataTable();
        table2.search(param).draw(); 
        }

        function searchFoam() {
            var searchValid = true;
            var getDateType = $('#date_type').val();
            if (getDateType == '') {
                $('#date_type_error').html("Select date type");
                searchValid = false;
            }
            return searchValid;
        }

        function adminSearch(ba , status) {
    var table = $('#myTable').DataTable();
   var table2 = $('#snTable').DataTable();

    table.columns(0).search(ba); // Filter Column 1
    table.columns(6).search(status); // Filter Column 2

    table2.columns(0).search(ba); // Filter Column 1
    table2.columns(7).search(status); //

    table.draw(); 
    table2.draw();
}
    </script>
</body>

</html>
