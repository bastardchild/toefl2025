<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="image/x-icon" href="/assets/img/favicon.png">
  <title>Complete</title>
  <?php require 'bootstrap.php'; ?>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-sm"><div class="branding"><img src="/assets/img/logopb.jpeg" alt=""></div></div>
            <div class="col-sm"><a href="/logout" class="btn btn-danger float-end mt-25px">Logout</a></div>
        </div>        
    </div>

    <div class="container mt-3 main-content mb-3">
        <h3>Selamat, <em><?= htmlspecialchars($_SESSION['name']) ?></em>!</h3>
        <p>Anda Telah menyelesaikan tes, Nilai TOEFL Anda adalah:</p>

        <div class="row mb-4 mt-4">
            <div class="col-md-4">            
                <div class="ttl-score">
                    <i class="bi bi-trophy" style="font-size: 35px;"></i><br>
                    Total Score<br>
                    <span><?php echo htmlspecialchars($toefl_score); ?></span>
                </div>  
            </div>

            <div class="col-md-8" style="font-size: 20px;">
                Nama: <strong><?= $first_name ?> <?= $middle_name ?> <?= $last_name ?></strong><br>
                Tanggal Ujian: <strong><?= $createdAtExam ?></strong>
                <div class="row mt-4">
                    <div class="col-sm">
                        <div class="small-score lc">
                            <h4><?php echo htmlspecialchars($listening_score); ?><span class="ttl-q">/50</span></h4>
                            Listening Comprehension
                        </div>                        
                    </div>
                    <div class="col-sm">
                        <div class="small-score we">
                        <h4><?php echo htmlspecialchars($writing_score); ?><span class="ttl-q">/40</span></h4>
                        Written and Structure Expression
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="small-score rc">
                        <h4><?php echo htmlspecialchars($reading_score); ?><span class="ttl-q">/50</span></h4>
                        Reading Comprehension
                        </div>
                    </div>
                </div>

            </div>            
        </div>      
    </div>
    <footer>
        <div class="container copyr mb-3">Copyright 2024 © Universitas Merdeka Malang</div>
    </footer>
</body>
</html>
