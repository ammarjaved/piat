// Main JavaScript for SN Monitoring System

$(document).ready(function() {
    // Initialize DataTables
    initDataTables();
    
    // Initialize LocalStorage
    initLocalStorage();
    
    // Bind Events
    bindEvents();
    
    // Check for saved button click state
    handleSavedState();
});

// Initialize DataTables
function initDataTables() {
    $('#myTable, #snTable').DataTable({
        aaSorting: [[3, 'desc']],
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
    });
}

// Initialize LocalStorage for form persistence
function initLocalStorage() {
    var permitSelect = document.getElementById('permit_type');
    var dateTypeSelect = document.getElementById('date_type');
    var fromDateSelect = document.getElementById('from_date');
    var toDateSelect = document.getElementById('to_date');
    var agingSelect = document.getElementById('aging');
    var baSelect = document.getElementById('searchBA');
    
    // Load saved values
    loadSavedValue(permitSelect, 'selectedPermitType');
    loadSavedValue(dateTypeSelect, 'selectedDateType');
    loadSavedValue(fromDateSelect, 'selectedFromDate');
    loadSavedValue(toDateSelect, 'selectedToDate');
    loadSavedValue(agingSelect, 'selectedAgging');
    
    if (username === 'admin') {
        loadSavedValue(baSelect, 'selectedBA');
    }
    
    // Save values on change
    permitSelect.addEventListener('change', function() {
        localStorage.setItem('selectedPermitType', this.value);
    });
    
    dateTypeSelect.addEventListener('change', function() {
        localStorage.setItem('selectedDateType', this.value);
    });
    
    fromDateSelect.addEventListener('change', function() {
        localStorage.setItem('selectedFromDate', this.value);
    });
    
    toDateSelect.addEventListener('change', function() {
        localStorage.setItem('selectedToDate', this.value);
    });
    
    agingSelect.addEventListener('change', function() {
        localStorage.setItem('selectedAgging', this.value);
    });
    
    if (username === 'admin') {
        baSelect.addEventListener('change', function() {
            localStorage.setItem('selectedBA', this.value);
        });
    }
}

// Load saved value helper
function loadSavedValue(element, key) {
    var savedValue = localStorage.getItem(key);
    if (savedValue) {
        element.value = savedValue;
    }
}

// Bind all events
function bindEvents() {
    // Reset button
    $('#myreset').click(function() {
        clearLocalStorage();
        window.location.reload(true);
    });
    
    // Submit button
    $('#mysubmit').on('click', function() {
        var savedButtonClick = localStorage.getItem('buttonClicked');
        if (savedButtonClick !== 'true') {
            localStorage.setItem('buttonClicked', 'true');
            window.location.reload(true);
        }
    });
    
    // Modal events
    $('#exampleModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('sn');
        $('#modal-sn').val(id);
    });
    
    $('#remarkModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var detail = button.data('remark');
        $('#remark-detail').html(detail);
        $('#update-remarks-id').val(button.data('id'));
    });
    
    // Remark date picker
    const dateInput = document.getElementById('remark-date');
    const textarea = document.getElementById('remark-detail');
    
    if (dateInput && textarea) {
        dateInput.addEventListener('change', function() {
            if (this.value) {
                const selectedDate = this.value;
                const formattedDate = new Date(selectedDate).toLocaleDateString('en-GB');
                const currentText = textarea.value;
                const dateText = `[${formattedDate}]`;
                textarea.value = currentText.trim() ? currentText.trimEnd() + '\n' + dateText + ' ' : dateText + ' ';
                textarea.focus();
            }
        });
    }
    
    // Search button
    $('#searchButton').on('click', function() {
        var searchTerm = $('#searchInput').val();
        var table = $('#myTable').DataTable();
        table.search(searchTerm).draw();
    });
    
    // DataTable pagination
    $('#myTable').on('page.dt', function() {
        var currentPage = $('#myTable').DataTable().page.info().page;
        localStorage.setItem('savedPage', currentPage);
    });
}

// Clear all localStorage
function clearLocalStorage() {
    const keysToRemove = [
        'selectedDateType',
        'selectedFromDate',
        'selectedToDate',
        'selectedAgging',
        'selectedStatus',
        'selectedBA',
        'buttonClicked',
        'selectedPermitType'
    ];
    
    keysToRemove.forEach(key => localStorage.removeItem(key));
}

// Handle saved state on page load
function handleSavedState() {
    var savedButtonClick = localStorage.getItem('buttonClicked');
    
    if (savedButtonClick) {
        if (savedButtonClick === 'true') {
            localStorage.setItem('buttonClicked', 'false');
            
            let dropdown = document.getElementById('searchBA');
            let selectedBaValue = dropdown.value;
            let savedStatus = localStorage.getItem('selectedStatus');
            
            if (savedStatus && savedStatus !== "null") {
                setTimeout(function() {
                    adminSearch(selectedBaValue, savedStatus);
                }, 1000);
            }
        } else {
            localStorage.setItem('buttonClicked', 'true');
            
            let button = document.getElementById('mysubmit');
            let dropdown = document.getElementById('searchBA');
            let selectedBaValue = dropdown.value;
            let savedStatus = localStorage.getItem('selectedStatus');
            
            button.click();
            
            if (savedStatus && savedStatus !== "null") {
                setTimeout(function() {
                    adminSearch(selectedBaValue, savedStatus);
                }, 1000);
            }
        }
    }
}

// Admin search function
function adminSearch(ba, status) {
    localStorage.setItem('selectedStatus', status);
    localStorage.setItem('selectedBA', ba);
    
    var table = $('#myTable').DataTable();
    var table2 = $('#snTable').DataTable();
    var userba = '<?php echo $_SESSION['user_ba'] ?? '' ?>';
    
    if (userba === '') {
        table.columns(0).search(ba);
        table.columns(6).search(status);
    } else {
        table.columns(5).search(status);
    }
    
    table2.columns(0).search(ba);
    table2.columns(8).search(status);
    
    table.draw();
    table2.draw();
}

// Excel generation function
function generateExcel() {
    var ba = $("#searchBA").val();
    var from = $("#from_date").val();
    var to = $("#to_date").val();
    
    $("#exc_ba").val(ba);
    $('#exc_from').val(from);
    $("#exc_to").val(to);
    
    return true;
}

// Search form validation
function searchFoam() {
    var searchValid = true;
    var getDateType = $('#date_type').val();
    
    if (getDateType === '') {
        $('#date_type_error').html("Select date type");
        searchValid = false;
    }
    
    return searchValid;
}