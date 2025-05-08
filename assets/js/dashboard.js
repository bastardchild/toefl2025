// Webcam access
async function startWebcam(videoElement) {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        videoElement.srcObject = stream;
    } catch (error) {
        alert('Webcam access is required for this test.');
        console.error('Error accessing webcam:', error);
    }
}

// Initialize webcam for both sections
window.onload = () => {
    const checkbox = document.getElementById('termCondition');
    const button = document.getElementById('startTest');
    // Listen for checkbox state change
    checkbox.addEventListener('change', function() {
        // Enable the button if the checkbox is checked, otherwise disable it
        button.disabled = !checkbox.checked;
    });

    startWebcam(document.getElementById('webcam'));
    startWebcam(document.getElementById('webcamCapture'));

    // Enable submit button only if both checkboxes are checked
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const webcamChecked = document.getElementById('webcamCheck').checked;
            const speakerChecked = document.getElementById('speakerCheck').checked;
            document.getElementById('submitpretest').disabled = !(webcamChecked && speakerChecked);
        });
    });

    // Show webcam capture section after "Selanjutnya"
    document.getElementById('submitpretest').addEventListener('click', function() {
        document.querySelector('.pre-test-hardware').style.display = 'none';
        document.querySelector('.webcam-capture').style.display = 'block';
        
        // Stop audio playback
        const audio = document.getElementById('audioTest');
        if (!audio.paused) {
            audio.pause();
        }
    });

    // Webcam capture and screenshot functionality
    document.getElementById('takeScreenshot').addEventListener('click', function() {
        const canvas = document.getElementById('canvasCapture');
        const video = document.getElementById('webcamCapture');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const screenshot = document.getElementById('screenshot');
        screenshot.src = canvas.toDataURL('image/png');
        screenshot.style.display = 'block';
        document.getElementById('submitWebcamCapture').disabled = false;
        document.getElementById('retakeScreenshot').style.display = 'block';
    });

    // Retake screenshot functionality
    document.getElementById('retakeScreenshot').addEventListener('click', function() {
        document.getElementById('takeScreenshot').click();
    });

    // Show ready-test and hide webcam-capture on submit
    document.getElementById('submitWebcamCapture').addEventListener('click', function() {
        const canvas = document.getElementById('canvasCapture');
        const image = canvas.toDataURL('image/png');

        // Prepare the data to send
        const formData = new FormData();
        formData.append('image', image);

        // Create and send the request
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/upload-image', true);

        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                alert('Image uploaded successfully!');
                document.querySelector('.webcam-capture').style.display = 'none';
                document.querySelector('.ready-test').style.display = 'block';
                document.querySelector('.pretest-txt').style.display = 'none';
            } else {
                alert('An error occurred while uploading the image.');
            }
        };

        xhr.send(formData);
    });
};


