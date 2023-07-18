<?php
      session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.3/datatables.min.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" >
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" ></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous"></script>

    <title>Index</title>

    <style>
        .container.shadow.p-5.my-5.bg-white {
    padding: 20px !important;
}
    </style>
</head>
<body>

<div class="container shadow p-5  my-5 bg-white">   

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
 
   <div class="m-3">
        <a href="./foam-1.php" class="btn btn-success btn-sm " >Add New</a> 
        </div>
        <div class="m-3">
    <a href="./services/generateExcel.php" class="btn btn-success btn-sm">Download Excel</a>
</div>
</div>
<div class="table-responsive table-bordered" style="overflow-y:auto ; ">  
    <table id="myTable" class="table table-striped table-responsive table-bordered " >
        <thead>
            <tr>
                <th>Sn no</th>
                <th>Ba</th>
                <th>Jenis Sambungan</th>
                <th>Tarikh Siap</th>
                <th >Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            include('./services/connection.php');
            $stmt = $pdo->prepare("SELECT * FROM public.ad_service_qr ");
            $stmt->execute();
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $pdo = null;
            
            foreach ($records as $record) {
                echo "<tr>";
                echo "<td>{$record['no_sn']}</td>";
                echo "<td>{$record['ba']}</td>";
                echo "<td>{$record['jenis_sambungan']}</td>";
                echo "<td>{$record['tarikh_siap']}</td>";
                

                echo "<td class='text-center'><div class='dropdown'>
                <button class='btn   ' type='button' id='dropdownMenuButton1' data-bs-toggle='dropdown' aria-expanded='false'>
                <img src='../images/three-dots-vertical.svg'  >
                </button>
                <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton1'>
                  <li><a class='dropdown-item' href='./services/generateExcel.php?id={$record['id']}'>Download Excel</a></li>";
                  echo "<li><a class='dropdown-item' href='./editFoam-1.php?no_sn={$record['no_sn']}'>Edit Foam</a></li>";
                  if($record['piat'] == 'yes'){
                echo "  <li><a class='dropdown-item' href='./previewPDF.php?no_sn={$record['no_sn']}' target='_blank'>Preview PDF</a></li>";
                  }
                  
                  echo "  <li><a class='dropdown-item' href='./detail.php?no_sn={$record['no_sn']}'  >Detail</a></li>";
                  echo "<li><button type='button' class='dropdown-item' data-bs-toggle='modal' data-id='{$record['id']}' data-bs-target='#exampleModal'> Remove </button'></li>";
                  
                echo "</ul>
              </div></td>";
                echo "</tr>";
            } 
            ?>
        </tbody>
    </table>
    
</div>

</div>
 

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class=" modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Remove Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="./services/removeSn.php" method="post" > 
      <div class="modal-body">
            Are You Sure ? 
            <input type="hidden" name="id" id="modal-id">
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

            $(document).ready(function() {
            $('#exampleModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var modal = $(this);
            $('#modal-id').val(id)
        });
    });
        });
    </script>
</body>
</html>
