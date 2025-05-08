<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="/assets/img/favicon.png">
    <title>Dashboard</title>
    <?php require 'bootstrap.php'; ?>    
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-sm"><div class="branding"><img src="/assets/img/logopb.jpeg" alt=""></div></div>
            <div class="col-sm">
                <?php if ($_SESSION['role_id'] === 1): ?>
                    <a href="/" class="btn btn-success btn-sm mt-25px" style="background: #222E5E; border-color: #222E5E;">Dashboard</a>
                    <?php endif; ?>
                </div>
            <div class="col-sm"><a href="/logout" class="btn btn-danger float-end mt-25px">Logout</a></div>
        </div>        
    </div>
<div class="container mt-3 main-content mb-3"> 
<h2>Daftar Nilai</h2>            
<p>Manage users, view reports, and configure settings here.</p>

<!-- DataTable HTML -->
<hr>
<h3 class="mt-3">Data Nilai</h3>
    <table id="examResultsTable" class="display">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>                
                <th>Kode Ujian</th>
                <th>TOEFL Score</th>
                <th>Listening</th>
                <th>Writing</th>
                <th>Reading</th>
            </tr>
        </thead>
    </table>   
</div>

<!-- Include DataTables CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#examResultsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/api/exam-r',
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
            { data: 2 }, // Exam Code
            { data: 3 }, // TOEFL Score
            { data: 4 }, // Listening Score
            { data: 5 }, // Writing Score
            { data: 6 }  // Reading Score
        ]
    });

    // Apply filter when filter input changes
    $('#examCodeFilter').on('change', function() {
        table.ajax.reload();
    });
});
</script>

<footer>
    <div class="container copyr mb-3">Copyright 2024 © Universitas Merdeka Malang</div>
</footer>
</body>
</html>