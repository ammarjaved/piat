<div class="text-end mb-3 d-flex justify-content-end">
    <div class="m-2">
        <a href="./piat_old.php" class="btn btn-success btn-sm">OLD PIAT</a>
    </div>
    
    <div class="m-2">
        <a href="./sn-monitoring/create.php" class="btn btn-success btn-sm">ADD SN</a>
    </div>
    
    <div class="m-2">
        <a href="./qr-foams/create.php" class="btn btn-success btn-sm">ADD QR AND PIAT</a>
    </div>
    
    <div class="m-2">
        <button type="button" class='btn btn-sm btn-success' data-bs-toggle='modal' 
            data-bs-target='#addVendorModal' aria-expanded='false'>
            Add Vendor
        </button>
    </div>

    <div class="m-2">
        <form action="./services/generateExcel.php" method="POST">
            <input type="hidden" name="exc_ba" id="exc_ba" 
                value="<?php echo isset($_POST['searchBA']) ? $_POST['searchBA'] : ''; ?>">
            <input type="hidden" name="exc_from" id="exc_from" 
                value="<?php echo isset($_POST['from_date']) ? $_POST['from_date'] : ''; ?>">
            <input type="hidden" name="exc_date_type" id="exc_date_type" 
                value="<?php echo isset($_POST['date_type']) ? $_POST['date_type'] : ''; ?>">
            <input type="hidden" name="exc_to" id="exc_to" 
                value="<?php echo isset($_POST['to_date']) ? $_POST['to_date'] : ''; ?>">
            
            <button href="./services/generateExcel.php" class="btn btn-success btn-sm" 
                type="submit" value="download-qr" name="submit-button">
                Download QR
            </button>

            <button href="./services/generateExcel.php" class="btn btn-success btn-sm mx-2" 
                value="download-sn" type="submit" name="submit-button">
                Download SN
            </button>
        </form>
    </div>
    
    <div class="m-2">
        <button id="myreset" class="btn btn-secondary" type="button" 
            name='submitButton' value="reset">
            Reset
        </button>
    </div>
</div>