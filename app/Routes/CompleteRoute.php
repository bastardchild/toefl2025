<?php
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use App\Models\ExamResult;
use App\Models\User;
use App\Models\Exam;


// complete route
$app->get('/complete', function ($request, $response, $args) {
    if (!isset($_SESSION['user_id'])) {
        return $response
            ->withHeader('Location', '/')
            ->withStatus(302);
    }

    // Retrieve user information from the 'users' table
    $user = User::where('id', $_SESSION['user_id'])->first();

    if (!$user) {
        // Handle case where user is not found (e.g., redirect to login)
        return $response->withHeader('Location', '/login')->withStatus(302);
    }

    $first_name = $user->name;
    $middle_name = $user->middle_name;
    $last_name = $user->last_name;

    $examDate = \App\Models\Exam::where('user_id', $_SESSION['user_id'])->first();
    $createdAtTimestamp = $examDate->created_at;
    $createdAtExam= date('d-m-Y', strtotime($createdAtTimestamp));

    // Retrieve the user's exam results
    $examResult = \App\Models\ExamResult::where('user_id', $_SESSION['user_id'])->first();

    if ($examResult) {
        $listening_score = $examResult->listening_score;
        $writing_score = $examResult->writing_score;
        $reading_score = $examResult->reading_score;
    } else {
        $listening_score = 0;
        $writing_score = 0;
        $reading_score = 0;
    }

    // Score mappings for each section
   $section1_scores  = [
        50 => 68, 49 => 67, 48 => 66, 47 => 65, 46 => 63, 45 => 62, 44 => 61, 43 => 60, 42 => 59, 
        41 => 58, 40 => 57, 39 => 57, 38 => 56, 37 => 55, 36 => 54, 35 => 54, 34 => 53, 33 => 52, 
        32 => 52, 31 => 51, 30 => 51, 29 => 50, 28 => 49, 27 => 49, 26 => 48, 25 => 48, 24 => 47, 
        23 => 47, 22 => 46, 21 => 45, 20 => 45, 19 => 44, 18 => 43, 17 => 42, 16 => 41, 15 => 41, 
        14 => 38, 13 => 37, 12 => 37, 11 => 35, 10 => 33, 9 => 32, 8 => 32, 7 => 31, 6 => 30, 
        5 => 29, 4 => 28, 3 => 27, 2 => 26, 1 => 25, 0 => 24
    ];  


    $section2_scores = [
        40 => 68, 39 => 67, 38 => 65, 37 => 63, 36 => 61, 35 => 60, 34 => 58, 33 => 57, 32 => 56, 
        31 => 55, 30 => 54, 29 => 53, 28 => 52, 27 => 51, 26 => 50, 25 => 49, 24 => 48, 23 => 47, 
        22 => 46, 21 => 45, 20 => 44, 19 => 43, 18 => 42, 17 => 41, 16 => 40, 15 => 40, 14 => 38, 
        13 => 37, 12 => 36, 11 => 35, 10 => 33, 9 => 31, 8 => 29, 7 => 27, 6 => 26, 5 => 25, 4 => 23, 
        3 => 22, 2 => 21, 1 => 20, 0 => 20
    ];
    

    $section3_scores = [
        50 => 67, 49 => 66, 48 => 65, 47 => 63, 46 => 61, 45 => 60, 44 => 59, 43 => 58, 42 => 57, 41 => 56, 
        40 => 55, 39 => 54, 38 => 54, 37 => 53, 36 => 52, 35 => 52, 34 => 51, 33 => 50, 32 => 49, 31 => 48, 
        30 => 48, 29 => 47, 28 => 46, 27 => 46, 26 => 45, 25 => 44, 24 => 43, 23 => 43, 22 => 42, 21 => 41, 
        20 => 40, 19 => 39, 18 => 38, 17 => 37, 16 => 36, 15 => 35, 14 => 34, 13 => 32, 12 => 31, 11 => 30, 
        10 => 29, 9 => 28, 8 => 28, 7 => 27, 6 => 26, 5 => 25, 4 => 24, 3 => 23, 2 => 23, 1 => 22, 0 => 21
    ];

    // Convert raw scores to mapped scores
    $mapped_listening_score = isset($section1_scores[$listening_score]) ? $section1_scores[$listening_score] : 0;
    $mapped_writing_score = isset($section2_scores[$writing_score]) ? $section2_scores[$writing_score] : 0;
    $mapped_reading_score = isset($section3_scores[$reading_score]) ? $section3_scores[$reading_score] : 0;

    // Sum the converted scores
    $total_converted_score = $mapped_listening_score + $mapped_writing_score + $mapped_reading_score;

    // Calculate the average of the converted scores
    $average_converted_score = $total_converted_score / 3;

    // Final TOEFL score (rounded to the nearest integer)
    $toefl_score = round($average_converted_score * 10);

    // save toefl_score to database
    if (!$examResult->toefl_score) {
        $examResult->toefl_score = $toefl_score;
        $examResult->save();
    }

    // Pass the mapped scores and final TOEFL score to the complete.php view
    ob_start();
    require __DIR__ . '/../../views/complete.php';
    $output = ob_get_clean();
    
    $response->getBody()->write($output);
    return $response;
});
