<form action="" method="post" onsubmit="return searchFoam()">
    <div class="text-end mb-3 row">
        <!-- BA Selection -->
        <div class="m-1 col-md-2">
            <label for="">Select BA :</label> <br>
            <select name="searchBA" id="searchBA" class="form-select">
                <?php if($_SESSION['user_name'] == "admin"){ ?>
                <option value="<?php echo isset($_POST['searchBA']) ? $_POST['searchBA'] : ''; ?>" hidden>
                    <?php echo isset($_POST['searchBA']) && $_POST['searchBA'] != '' ? $_POST['searchBA'] : 'All Ba'; ?>
                </option>
                <option value="KLB - 6121">KLB - 6121</option>
                <option value="KLT - 6122">KLT - 6122</option>
                <option value="KLP - 6123">KLP - 6123</option>
                <option value="KLS - 6124">KLS - 6124</option>
                <option value="">All Ba</option>
                <?php } else {
                    echo "<option value='{$_SESSION['user_ba']}'>{$_SESSION['user_ba']}</option>";
                }?>
            </select>
        </div>

        <!-- Date Type -->
        <div class="m-2 col-md-2">
            <label for="">Date Type :</label> <br>
            <span class="text-danger" id="date_type_error"></span>
            <select name="date_type" id="date_type" class="form-select">
                <option value="<?php echo isset($_POST['date_type']) && $_POST['date_type'] != '' ? $_POST['date_type'] : ''; ?>" hidden>
                    <?php echo isset($_POST['date_type']) && $_POST['date_type'] != '' ? $_POST['date_type'] : 'Select dateType'; ?>
                </option>
                <option value="Both">Both</option>
                <option value="CSP">CSP Date</option>
                <option value="Completion">Completion Date</option>
            </select>
        </div>

        <!-- From Date -->
        <div class="m-2 col-md-2">
            <label for="">From Date :</label> <br>
            <input type="date" name="from_date" id="from_date" class="form-control"
                value="<?php echo isset($_POST['from_date']) ? $_POST['from_date'] : ''; ?>">
        </div>

        <!-- To Date -->
        <div class="m-2 col-md-2">
            <label for="">To Date :</label> <br>
            <input type="date" name="to_date" id="to_date" class="form-control"
                value="<?php echo isset($_POST['to_date']) ? $_POST['to_date'] : ''; ?>">
        </div>

        <!-- Aging -->
        <div class="m-2 col-md-2">
            <label for="">Aging greater than :</label> <br>
            <select name="aging" id="aging" class="form-select">
                <option value="<?php echo isset($_POST['aging']) ? $_POST['aging'] : ''; ?>" hidden>
                    <?php echo isset($_POST['aging']) && $_POST['aging'] != '' ? $_POST['aging'] : 'Select aging'; ?>
                </option>
                <option value="1,7">1-7 days</option>
                <option value="8,14">8-14 days</option>
                <option value="14,30">14-30 days</option>
                <option value="30,60">30-60 days</option>
                <option value=">60">>60 days</option>
            </select>
        </div>

        <!-- Permit Type -->
        <div class="m-2 col-md-2">
            <label for="">Permit Type :</label> <br>
            <select name="permit_type" id="permit_type" class="form-select">
                <option value="<?php echo isset($_POST['permit_type']) ? $_POST['permit_type'] : ''; ?>" hidden>
                    <?php echo isset($_POST['permit_type']) && $_POST['permit_type'] != '' ? $_POST['permit_type'] : 'Select Permit'; ?>
                </option>
                <option value="">Both</option>
                <option value="PBT">PBT</option>
                <option value="DBKL">DBKL</option>
            </select>
        </div>

        <!-- Filter Button -->
        <div class="col-md-1 pt-2 text-start" style="display: inline">
            <button class="btn btn-secondary mt-4 btn-sm" type="submit" id="mysubmit" 
                name='submitButton' value="filter">
                Filter
            </button>
        </div>
    </div>
</form>