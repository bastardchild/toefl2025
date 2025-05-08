<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="/assets/img/favicon.png">
    <title>Listening Section</title>
    <?php require 'bootstrap.php'; ?>
</head>
<body>
    <div class="container mt-5 main-content mb-5">
        <h1>Listening Section</h1>

        <!-- Audio element -->
        <audio id="listeningAudio" src="/assets/audio/audio-test.mp3"></audio>

        <!-- Start button -->
        <button id="startButton" class="btn btn-primary">Start Listening</button>

        <form id="listeningForm" action="/listening" method="post">
            <!-- Example Question -->
            <?php include 'questions/listening_questions.php'; ?>

            <button type="submit" class="btn btn-success">Submit Answers</button>
        </form>
        <a href="/logout" class="btn btn-danger d-none">Logout</a>
    </div>

    <video id="webcam" class="cam-exam" autoplay></video>
    <script>
        // JavaScript to handle audio ended event
        document.getElementById('listeningAudio').addEventListener('ended', function() {
            // Clear saved playback time and submit the form when the audio ends
            sessionStorage.removeItem('audioPlaybackTime');
            document.getElementById('listeningForm').submit();
        });

        // Save audio playback position before the page unloads
        window.addEventListener('beforeunload', function() {
            const audio = document.getElementById('listeningAudio');
            if (audio) {
                sessionStorage.setItem('audioPlaybackTime', audio.currentTime);
                console.log('Saved playback time:', audio.currentTime);
            }
        });

        // Restore audio playback position when the page loads
        window.onload = function() {
            const audio = document.getElementById('listeningAudio');
            const savedTime = sessionStorage.getItem('audioPlaybackTime');
            if (audio && savedTime) {
                audio.currentTime = parseFloat(savedTime);
                console.log('Restored playback time:', savedTime);
            }
            startWebcam();
        };

        // Start audio on button click
        document.getElementById('startButton').addEventListener('click', function() {
            const audio = document.getElementById('listeningAudio');
            if (audio) {
                console.log('Current Time Before Play:', audio.currentTime);
                audio.play().then(() => {
                    // Hide the button once audio starts playing
                    this.style.display = 'none';
                    console.log('Audio is now playing');
                }).catch(error => {
                    console.error('Error playing audio:', error);
                });
            }
        });

        // Webcam access
        async function startWebcam() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                document.getElementById('webcam').srcObject = stream;
            } catch (error) {
                alert('Webcam access is required for this test.');
                console.error('Error accessing webcam:', error);
            }
        }
    </script>
</body>
</html>