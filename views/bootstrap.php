<!-- Bootstrap CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap Bundle CDN (includes Popper.js) -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@400;600&display=swap" rel="stylesheet">

<link href="/assets/css/custom.css" rel="stylesheet">

<script>
    function keepSessionAlive() {
        fetch('/keep-alive', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'inactive') {
                window.location.href = '/';
            }
        })
        .catch(error => {
            console.error('Error keeping session alive:', error);
        });
    }
    setInterval(keepSessionAlive, 300000);
</script>

<?php
// Check if the user has role_id = 2 (user)
if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2) {
?>
    <script>
        // Alert on right-click (contextmenu)
        document.addEventListener('contextmenu', function(event) {
            alert("Right-clicking is not allowed during the exam!");
            event.preventDefault(); // This will prevent the default right-click context menu
        });

        // // Alert when the user moves away from the tab or changes the tab
        // document.addEventListener('visibilitychange', function() {
        //     if (document.visibilityState === 'hidden') {
        //         alert("You moved away from the exam tab! Please stay focused.");
        //     }
        // });
    </script>
<?php
}
?>