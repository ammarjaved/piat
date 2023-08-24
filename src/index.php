<?php
session_start();
if(!isset($_SESSION['user_name']) && !isset($_SESSION['user_id'])){
    header("location:./auth/login.php");
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

    <title>Index</title>

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
            <a class="navbar-brand" href="/foams/index.php">Piat Check List</a>
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

        <h3 class="text-center">PIAT CHECKLIST LV OVERHEAD</h3>
        <div class="text-end mb-3 d-flex justify-content-end">
        <div class="m-2">

<a href="./sn-monitoring/create.php" class="btn btn-success btn-sm ">ADD SN</a>
</div>
            <div class="m-2"><a href="./qr-foams/create.php" class="btn btn-success btn-sm ">ADD QR AND PIAT</a></div>
           
            <div class="m-2">
                <form action="./services/generateExcel.php" method="POST">
                    <input type="hidden" name="exc_ba" id="exc_ba" value="<?php echo isset($_POST['searchBA']) ? $_POST['searchBA'] : ''; ?>">
                    <input type="hidden" name="exc_from" id="exc_from" value="<?php echo isset($_POST['from_date']) ? $_POST['from_date'] : ''; ?>">
                    <input type="hidden" name="exc_to" id="exc_to" value="<?php echo isset($_POST['to_date']) ? $_POST['to_date'] : ''; ?>">
                    <button href="./services/generateExcel.php" class="btn btn-success btn-sm" type="submit">Download
                        Excel</button>
                </form>
            </div>
        </div>
        <form action="" method="post">
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


<?php if($_SESSION['user_name'] == 'admin'){ include "./admin/dashboard-count.php";} ?>
        
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                    type="button" role="tab" aria-controls="home" aria-selected="true">QR</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                    type="button" role="tab" aria-controls="profile" aria-selected="false">SN
                    Monitoring</button>
            </li>

        </ul>


        <div class="tab-content" id="myTabContent">



            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="table-responsive table-bordered py-3" style="overflow-y:auto ; ">
                    <table id="myTable" class="table table-striped table-responsive table-bordered">
                        <thead>
                            <tr>
                            <th>BA</th>
                                <th>SN NO</th>
                                <th>JENIS SN</th>
                                <th>Aging (days)</th>
                                <th>STATUS</th>
                                <th>QR</th>
                                <th>Piat</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                           
                            
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['submitButton'] == 'filter') {

                             
                                    $ba = isset($_POST['searchBA']) ? $_POST['searchBA'] : '';
                                    $from = isset($_POST['from_date']) ? $_POST['from_date'] : '';
                                    $to = isset($_POST['to_date']) ? $_POST['to_date'] : '';
                                    $record = '';

                                    if ($from == '' || $to == '') {       // if dates are null and only ba is selected then first get min and max date
                                        $stmt = $pdo->prepare('SELECT MAX(tarikh_siap) , MIN(tarikh_siap) FROM public.ad_service_qr ');
                                        $stmt->execute();
                                        $record = $stmt->fetch(PDO::FETCH_ASSOC);
                                    }

                                    $from = $from == '' ? $record['min'] : $from;
                                    $to = $to == '' ? $record['max'] : $to;
                            
                                                                        // filter query
                                    $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr WHERE ba LIKE :ba AND tarikh_siap::date >= :from AND tarikh_siap::date <= :to  ");
                                    $stmt->execute([':ba' => "%$ba%", ':from' => $from, ':to' => $to]);

                              }else{
                                    // without filter
                                    if($_SESSION['user_name'] == "admin"){
                                        $stmt = $pdo->prepare('SELECT * FROM public.ad_service_qr    ORDER BY id DESC');
                                  
                                
                                    }else{
                                         $stmt = $pdo->prepare('SELECT * FROM public.ad_service_qr WHERE ba LIKE :ba ORDER BY id DESC');
                                    // $stmt->bindValue(':created', '%' . $_SESSION['user_id'] . '%', PDO::PARAM_STR);
                                    $stmt->bindValue(':ba', '%' . $_SESSION['user_ba'] . '%', PDO::PARAM_STR);
                                    } 
                                    $stmt->execute();
                              }
       
                            
                                
                                
                                
                        
                            
                            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach ($records as $record) {
                                echo '<tr>';
                                echo "<td>{$record['ba']}</td>";
                                echo "<td>{$record['no_sn']}</td>";
                                
                                echo "<td>{$record['jenis_sn']}</td>";
                                echo "<td>{$record['aging_days']}</td> <td>{$record['status']}</td><td class='algin-middle text-center'>";
                                if ( $record['tarikh_siap'] != '') {
                                    echo ' <span class="check" style="font-weight: 600; color: green;">&#x2713;</span>';
                                } else {
                                    echo '<span class="check" style="font-weight: 600; color: red;">&#x2715;</span>';
                                }
                            
                                echo '</td><td class="algin-middle text-center">';
                            
                                if ($record['status'] == "Complete" && $record['piat'] == 'yes' ) {
                                    echo '<span class="check " style="font-weight: 600; color: green;">&#x2713;</span>';
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
                                  if($record['piat'] == 'yes'){
                                        echo "<li><a class='dropdown-item' href='./qr-foams/edit.php?no_sn={$record['no_sn']}'>";
                                        echo  $record['tarikh_siap'] != '' ? 'Edit Foam' : 'Add Qr';                                   
                                        echo '</a></li>';
                                        if ( $record['status'] == "Complete") {
                                            echo "  <li><a class='dropdown-item' href='./generate-pdf/previewPDF.php?no_sn={$record['no_sn']}' target='_blank'>Preview PDF</a></li>";
                                        } elseif ( $record['status'] == "Inprocess" && $record['tarikh_siap'] != '') {
                                            echo "  <li><a class='dropdown-item' href='./services/foamRedirect.php?sn={$record['no_sn']}'>Fill Checklist</a></li>";
                                        }
                                  echo "  <li><a class='dropdown-item' href='./piat-foam/detail.php?no_sn={$record['no_sn']}'  >Detail</a></li>";
                                  
                                    }
                            
                                echo "  <li><a class='dropdown-item' href='./sn-monitoring/detail.php?no_sn={$record['no_sn']}' >Sn Detail</a></li>";
                                echo "<li><button type='button' class='dropdown-item' data-bs-toggle='modal' data-sn='{$record['no_sn']}' data-bs-target='#exampleModal'> Remove </button'></li>";
                                echo "</ul></div></td>";
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>

                </div>

            </div>

            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="table-responsive table-bordered py-3" style="overflow-y:auto ; ">
                    <table id="snTable" class="table table-striped table-responsive table-bordered ">
                        <thead>
                            <tr>
                                <th>BA</th>
                                <th>SN NO</th>
                                <th>JENIS SN</th>
                                <th>Aging (days)</th>
                                <th>STATUS</th>
                                <th>QR</th>
                                <th>Piat</th>
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
                                if($record['status'] == "Inprocess" && $record['tarikh_siap'] == ""){
                                echo '<tr>';
                                echo "<td>{$record['ba']}</td>";
                                echo "<td>{$record['no_sn']}</td>";
                                echo "<td>{$record['jenis_sn']}</td>";
                                echo "<td>{$record['aging_days']}</td>";
                                echo "<td>{$record['status']}</td>";
                                
                                echo "<td class='algin-middle text-center'>";
                                if ( $record['tarikh_siap'] != '') {
                                  echo ' <span class="check" style="font-weight: 600; color: green;">&#x2713;</span>';
                              } else {
                                  echo '<span class="check" style="font-weight: 600; color: red;">&#x2715;</span>';
                              }
                          
                              echo '</td><td class="algin-middle text-center">';
                          
                       
                                  echo '<span class="check" style="font-weight: 600; color: red;">&#x2715;</span>';
                              
                              echo '</td>';
                                echo "<td class='text-center'><div class='dropdown'>
                              <button class='btn   ' type='button' id='dropdownMenuButton1' data-bs-toggle='dropdown' aria-expanded='false'>
                              <img src='../images/three-dots-vertical.svg'  >
                              </button>
                              <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton1'>";
                              echo "<li><a class='dropdown-item' href='./qr-foams/edit.php?no_sn={$record['no_sn']}'>Add Qr</a></li>";
                                echo "<li><a class='dropdown-item' href='./sn-monitoring/edit.php?no_sn={$record['no_sn']}'>Edit Foam</a></li>";
                            
                                echo "  <li><a class='dropdown-item' href='./sn-monitoring/detail.php?no_sn={$record['no_sn']}'  >Detail</a></li>";
                               
                            
                                echo "</ul>
                                                                      </div></td>";
                                echo '</tr>';
                            }
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
            $('#myTable').DataTable();
            $("#snTable").DataTable()

            $(document).ready(function() {
                $('#exampleModal').on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget);
                    var id = button.data('sn');
                    var modal = $(this);
                    $('#modal-sn').val(id)
                });
            });

            reset();
        });

        function reset() {
            var sub = "<?php echo isset($_POST['submitButton']) ? $_POST['submitButton'] : ''; ?>";
            if (sub == 'reset') {
                $('#searchBA').find('option').first().remove();
                $('#searchBA').prepend('<option value="" selected hidden>Select ba</option')
                $('#to_date').val('')
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
    </script>
</body>

</html>
