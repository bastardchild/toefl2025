<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="image/x-icon" href="/assets/img/favicon.png">
  <title>Listening Comprehension</title>
  <?php require 'bootstrap.php'; ?>
</head>
<body class="exam-area">
  <div class="container">
        <div class="row">
            <div class="col-sm"><div class="branding"><img src="/assets/img/logopb.jpeg" alt=""></div></div>            
        </div>        
    </div>
  <div class="container mt-3 main-content mb-5">   
    <audio id="listeningAudio" src="https://is3.cloudhost.id/afjnstorage/audio-exam.mp3"></audio>
   
    <button id="startButton" class="btn btn-primary mb-3"><i class="bi bi-music-note-beamed"></i> Play Audio</button>

    <form id="listeningForm" action="/listening" method="post">
      <?php include 'questions/listening_questions.php'; ?>

      <button type="submit" class="btn btn-success">Submit Answers <i class="bi bi-arrow-up-right-square"></i></button>
    </form>
    <a href="/logout" class="btn btn-danger d-none">Logout</a>
  </div>

  <video id="webcam" class="cam-exam" autoplay></video>

  <script>
    $(document).ready(function() {
       // Restore form answers from local storage
    $('input[type=radio]').each(function() {
        var questionName = $(this).attr('name');
        var savedAnswer = localStorage.getItem('answer_listening' + questionName);
        if (savedAnswer && $(this).val() === savedAnswer) {
            $(this).prop('checked', true);
        }
    });

    // Save answers to local storage on change
    $('input[type=radio]').change(function() {
        var questionName = $(this).attr('name');
        var answer = $(this).val();
        localStorage.setItem('answer_listening' + questionName, answer);
    });

    // Handle form submission
    $('#listeningForm').on('submit', function() {
        $('input[type=radio]').each(function() {
            localStorage.removeItem('answer_listening' + $(this).attr('name')); // Clear saved answers
        });
    });

      const audio = $('#listeningAudio');
      const lastPositionKey = 'audioLastPosition';

      // Load the last played position from local storage
      const lastPosition = localStorage.getItem(lastPositionKey);
      if (lastPosition) {
        audio.prop('currentTime', parseFloat(lastPosition));
      }

      // Update local storage with the current position whenever the audio is played
      audio.on('timeupdate', function() {
        localStorage.setItem(lastPositionKey, audio.prop('currentTime'));
      });

      // Save audio playback position before the page unloads
      $(window).on('beforeunload', function() {
        if (audio.length > 0) { // Check if audio exists before saving
          localStorage.setItem(lastPositionKey, audio.prop('currentTime'));
          console.log('Saved playback time:', audio.prop('currentTime'));
        }
      });

      // Start audio on button click
     $('#startButton').on('click', function() {
      const audioElement = $('#listeningAudio').get(0); // Get the native audio element
      if (audioElement) {
        console.log('Current Time Before Play:', audioElement.currentTime);
        audioElement.play().then(function() {
          // Hide the button once audio starts playing
          $('#startButton').hide();
          console.log('Audio is now playing');
        }).catch(function(error) {
          console.error('Error playing audio:', error);
        });
      }
    });

      // Handle audio ended event
      audio.on('ended', function() {
        // Clear saved playback time and submit the form when the audio ends
        localStorage.removeItem(lastPositionKey);
        $('#listeningForm').submit();
      });

      // Remove localStorage item on form submit
      $('#listeningForm').on('submit', function(event) {
        localStorage.removeItem('audioLastPosition');
        console.log('Removed localStorage item on form submit: audioLastPosition');
      });

      // Webcam access
      async function startWebcam() {
        try {
          const stream = await navigator.mediaDevices.getUserMedia({ video: true });
          $('#webcam').prop('srcObject', stream);
        } catch (error) {
          alert('Webcam access is required for this test.');
          console.error('Error accessing webcam:', error);
        }
      }

      startWebcam();
    });
  </script>
</body>
</html>
