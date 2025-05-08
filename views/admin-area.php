<h2>Admin Dashboard</h2>            
<p>Manage users, view reports, and configure settings here.</p>

<?php if (isset($message)): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>
<hr>
<h4 class="mt-3">Tambah Peserta</h4>
<b>Upload CSV File</b><br>
<form action="/upload-csv" method="post" enctype="multipart/form-data">
    <label for="csv">Select CSV file:</label>
    <input type="file" id="csv" name="csv" accept=".csv" required>
    <button type="submit" class="btn btn-success">Upload</button>
</form>

<hr>
<h4 class="mt-3">Download Peserta</h4>
<b>Download CSV File</b><br>
<form action="/download-csv" method="post">
    <label for="exam_code">Select Exam Code:</label>
    <select id="exam_code" name="exam_code" style="margin-right:35px;" required>
        <option value="">-- Select Exam Code --</option>
        <?php foreach ($examCodes as $examCode): ?>
            <option value="<?= htmlspecialchars($examCode->exam_code) ?>">
                <?= htmlspecialchars($examCode->exam_code) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-success">Download</button>
</form>

<!-- DataTable HTML -->
<hr>
<h3 class="mt-3">Data Peserta</h3>
    <div class="row mt-3 mb-3">
        <div class="col-md-4">
            <!-- Dropdown for exam code filter -->
            <label for="examCodeFilter">Filter Kode Ujian:</label>
            <select id="examCodeFilter" class="form-control">
                <option value="">All</option>
                <!-- Options will be populated dynamically -->
            </select>
        </div>
    </div>
    

    <table id="userTable" class="display">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Username & Password</th>
                <th>Status</th>
                <th>Foto</th>
                <th>Kode Ujian</th>
                <th>Delete</th>
                <th>Reset</th>
            </tr>
        </thead>
    </table>   

<!-- Include DataTables CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#userTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/api/users',
            data: function(d) {
                d.exam_code = $('#examCodeFilter').val(); // Add the selected exam code to the request
            }
        },
        columns: [
            { data: 0 }, // No
            { 
                data: 1, // Nama
                render: function(data, type, row) {
                    return data.toUpperCase();
                }
            },
            { data: 2 }, // Username
            { 
                data: 3, // Status
                render: function(data, type, row) {
                    switch(data) {
                        case 1: return 'Ongoing';
                        case 2: return 'Complete';
                        default: return '-';
                    }
                }
            },
            { 
                data: 4, // Foto
                render: function(data, type, row) {
                    return data ? `<img src="/uploads/${data}" alt="Foto" style="width: 100px;">` : 'No photo';
                }
            },
            { data: 5 }, // Kode Ujian
            { 
                data: 6, // Reset (using ID from last column)
                render: function(data, type, row) {
                    return `<a href="/delete-usr/${data}" class="btn btn-danger btn-sm" onclick="return confirmDelete()">Delete</a>`;
                }
            },
            { 
                data: 7, // Reset (using ID from last column)
                render: function(data, type, row) {
                    return `<a href="/reset-exam/${data}" class="btn btn-dark btn-sm">Reset</a>`;
                }
            }
        ]
    });

    // Populate the dropdown with unique exam codes from the server
    $.ajax({
        url: '/api/exam-codes',
        method: 'GET',
        success: function(data) {
            var options = '<option value="">All</option>';
            data.forEach(function(code) {
                options += `<option value="${code}">${code}</option>`;
            });
            $('#examCodeFilter').html(options);
        }
    });

    // Filter table when the dropdown value changes
    $('#examCodeFilter').on('change', function() {
        table.ajax.reload();
    });
    
    });
    // JavaScript function to confirm delete action
    function confirmDelete() {
        return confirm('Are you sure you want to delete this record? This action cannot be undone.');
    }
    </script>