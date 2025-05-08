$(document).ready(function() {
    // Load saved countdown duration and form data from local storage
    var countdownDuration = localStorage.getItem('countdownDurationWriting') || 25 * 60; // Default to 10 minutes if not set
    $('#timer').text(formatTime(countdownDuration));

    // Restore form answers from local storage
    $('input[type=radio]').each(function() {
        var questionName = $(this).attr('name');
        var savedAnswer = localStorage.getItem('answer_writing' + questionName);
        if (savedAnswer && $(this).val() === savedAnswer) {
            $(this).prop('checked', true);
        }
    });

    function formatTime(seconds) {
        var minutes = Math.floor(seconds / 60);
        var secs = seconds % 60;
        return minutes + ':' + (secs < 10 ? '0' : '') + secs;
    }

    function updateTimer() {
        countdownDuration--;
        $('#timer').text(formatTime(countdownDuration));
        // Save timer data to local storage
        localStorage.setItem('countdownDurationWriting', countdownDuration);
        // Change background to red and text color to white if time is less than 10 minutes
        if (countdownDuration <= 10 * 60) {
            $('.exam-timer').css({
                'background-color': 'red',
                'color': 'white'
            });
        }
        if (countdownDuration <= 0) {
            $('#exam-form').submit(); // Automatically submit the form when the timer ends
        }
    }

    // Update the timer every second
    setInterval(updateTimer, 1000);

    // Save answers to local storage on change
    $('input[type=radio]').change(function() {
        var questionName = $(this).attr('name');
        var answer = $(this).val();
        localStorage.setItem('answer_writing' + questionName, answer);
    });

    // Handle form submission
    $('#exam-form').on('submit', function() {
        localStorage.removeItem('countdownDurationWriting'); // Clear timer data on form submission
        $('input[type=radio]').each(function() {
            localStorage.removeItem('answer_writing' + $(this).attr('name')); // Clear saved answers
        });
    });
});