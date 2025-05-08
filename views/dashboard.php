<?php $isCompleted = isset($exam) && $exam->status_id == 2; ?>
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
                    <a href="/scores" class="btn btn-success btn-sm mt-25px" style="background: #222E5E; border-color: #222E5E;">Cek Nilai</a>
                    <?php endif; ?>
                </div>
            <div class="col-sm"><a href="/logout" class="btn btn-danger float-end mt-25px">Logout</a></div>
        </div>        
    </div>

    <div class="container mt-3 main-content mb-3">         
        <!-- error exam -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?> 

        <?php if ($_SESSION['role_id'] === 1): ?>
            <!-- admin view -->
            <?php require 'admin-area.php'; ?>   

        <?php else: ?>   
            <div class="col-md-12">  
            <?php if (isset($_SESSION['user_id'])): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            fetch('/get-reset-flag')
                                .then(response => response.json())
                                .then(data => {
                                    if (data.reset_required) {
                                        // Show the clear local storage button
                                        document.getElementById('alert-ulang').style.display = 'block';
                                        document.getElementById('clear-local-storage').style.display = 'block';
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                });
                        });
                    </script>
                    <div class="alert alert-danger" id="alert-ulang" style="display:none;">
                        <p><strong>Silahkan Reset Sebelum Mengulang Ujian.</strong></p>
                        <button id="clear-local-storage" class="btn btn-primary" style="display:none;" onclick="clearLocalStorage()">Reset Ujian</button>
                    </div>

                    <script>
                        function clearLocalStorage() {
                            // Clear the local storage
                            localStorage.clear();
                            alert('Local storage cleared.');

                            // Optionally, notify the server that local storage was cleared
                            fetch('/clear-reset-flag', { method: 'POST' })
                                .then(response => response.json())
                                .then(data => {
                                    console.log('Reset flag cleared:', data);
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                });

                            // Hide the button after clearing
                            document.getElementById('clear-local-storage').style.display = 'none';
                            document.getElementById('alert-ulang').style.display = 'none';

                        }
                    </script>
            <?php endif; ?>
            <div class="pretest-txt">
                <h3>Selamat Datang, <em><?= htmlspecialchars($_SESSION['name']) ?></em>!</h3>
                <p>Sebelum memulai ujian, penting untuk menyelesaikan beberapa pemeriksaan awal untuk memastikan semuanya siap. <br>Silakan ikuti langkah-langkah di bawah ini:</p>
                <hr>
            </div> 
            
            <div class="pre-test-hardware" <?php echo $isCompleted ? 'style="display:none;"' : ''; ?>>
                <h2 class="mt-3">Persiapan Ujian: Tes Webcam dan Speaker</h2>
               
                <!-- Webcam Test Section -->
                <div class="mt-3">
                    <h4><span class="number-b">1</span> Tes Kamera Webcam <i class="bi bi-webcam"></i></h4>
                    <p>Kami akan melakukan tes singkat untuk memastikan kamera webcam Anda berfungsi dengan baik. <br>Selama ujian, webcam Anda akan digunakan untuk tujuan keamanan dan pemantauan. <br><strong>Silakan izinkan akses ke webcam Anda.</strong></p>
                    <video id="webcam" autoplay></video>    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="webcamCheck" required>
                        <label class="form-check-label" for="webcamCheck">Saya dapat melihat tampilan webcam dengan jelas.</label>
                    </div>                     
                </div>

                <!-- Speaker Test Section -->
                <div class="mt-5">
                    <h4><span class="number-b">2</span> Tes Speaker <i class="bi bi-volume-up-fill"></i></h4>
                    <p>Untuk memastikan Anda dapat mendengar bagian audio dari ujian, kami akan menjalankan tes speaker. <br>Pastikan speaker atau headphone Anda terhubung dan berfungsi dengan baik.</p>
                    <audio id="audioTest" controls>
                        <source src="https://is3.cloudhost.id/afjnstorage/audio-test.mp3" type="audio/mp3">
                        Your browser does not support the audio element.
                    </audio>                                          
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="speakerCheck" required>
                        <label class="form-check-label" for="speakerCheck">Saya dapat mendengar audio dengan jelas.</label>
                    </div>     
                </div>
                <hr class="mt-3">
                <button id="submitpretest" class="btn btn-success mt-3" disabled>Next Step <i class="bi bi-arrow-up-right-square"></i></button>
            </div>

            <!-- Webcam Capture Section -->
            <div class="webcam-capture mt-3" style="display:none;">
                <h4><span class="number-b">3</span> Pengambilan Screenshot Webcam <i class="bi bi-webcam"></i></h4>
                <p>Untuk keperluan verifikasi identitas, kami akan mengambil screenshot dari webcam Anda. <br>Silakan posisikan diri Anda di depan kamera dan pastikan pencahayaan memadai.</p>
                <div class="row">
                    <div class="col-md-6">
                        <!-- Webcam video and capture controls -->
                        <div>
                            <video id="webcamCapture" autoplay></video>
                            <canvas id="canvasCapture" style="display:none;"></canvas>
                        </div>
                        <div>
                            <button id="takeScreenshot" class="btn btn-primary mt-3">Take Screenshot</button>                            
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Screenshot preview -->
                        <div>
                            <img id="screenshot" alt="Webcam Screenshot" style="max-width:100%;display:none;">
                            <button id="retakeScreenshot" class="btn btn-secondary mt-3" style="display:none;">Retake Screenshot</button>
                        </div>                       
                    </div>
                </div>

                <form id="uploadForm" action="/upload-image" method="post" enctype="multipart/form-data" style="display:none;">
                    <input type="file" id="fileInput" name="image" accept="image/png">
                    <button type="submit" id="uploadButton">Upload Image</button>
                </form>
                <hr class="mt-5">
                <button id="submitWebcamCapture" class="btn btn-success mt-3" disabled>Next Step <i class="bi bi-arrow-up-right-square"></i></button>
            </div>

            <!-- Ready to Start Exam Section -->
            <div class="ready-test" style="display:none;">
                <?php require 'aturan.php'; ?>                                
                <p class="mt-5">Ready to start the TOEFL exam? Click the button below to begin.</p>
                <form action="/start-exam" method="post">
                    <div class="alert alert-secondary">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="termCondition" required>
                        <label class="form-check-label" for="termCondition">
                            <strong>Saya menyatakan telah membaca, memahami, dan menyetujui seluruh ketentuan yang tercantum dalam peraturan pelaksanaan ujian ini. Saya bersedia mengikuti semua aturan yang berlaku dan bertanggung jawab atas segala tindakan yang saya lakukan selama pelaksanaan ujian.</strong>
                        </label>
                    </div>    
                    </div>                
                    <button type="submit" class="btn btn-primary mt-3" id="startTest" disabled>Start TOEFL Exam <i class="bi bi-arrow-up-right-square"></i></button>
                </form>
            </div>
          
            <script src="/assets/js/dashboard.js"></script>
        <?php endif; ?>
        </div>       
    </div>

    <footer>
        <div class="container copyr mb-3">Copyright 2024 © Universitas Merdeka Malang</div>
    </footer>
</body>
</html>
