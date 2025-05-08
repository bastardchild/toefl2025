<?php
// exam_routes.php

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use App\Models\Exam; // Adjust the namespace according to your folder structure
use App\Models\ExamResult;

// Start Exam route
$app->post('/start-exam', function (Request $request, Response $response, array $args) {
    if (!isset($_SESSION['user_id'])) {
        return $response
            ->withHeader('Location', '/')
            ->withStatus(302);
    }

    $userId = $_SESSION['user_id'];
    
    // Check if an exam record already exists for the user
    $existingExam = Exam::where('user_id', $userId)->first();

    if ($existingExam) {
        // If an existing exam is found, redirect to a page showing an error message
        $_SESSION['error_message'] = 'You have already started an exam.';
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    // Create a new exam record with status 'In Progress'
    $exam = new Exam();
    $exam->user_id = $userId;
    $exam->status_id = 1; // In Progress
    $exam->save();

    $_SESSION['exam_id'] = $exam->id; // Set exam_id in session
    $_SESSION['current_section'] = 'listening';

    return $response
        ->withHeader('Location', '/listening')
        ->withStatus(302);
});

// Listening section route (GET)
$app->get('/listening', function (Request $request, Response $response, array $args) {
    if (!isset($_SESSION['user_id']) || $_SESSION['current_section'] !== 'listening') {
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    ob_start();
    require __DIR__ . '/views/listening.php';
    $output = ob_get_clean();
    
    $response->getBody()->write($output);
    return $response;
});

// Handle Listening section submission (POST)
$app->post('/listening', function (Request $request, Response $response, array $args) {
    if (!isset($_SESSION['user_id']) || $_SESSION['current_section'] !== 'listening') {
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    $examId = $_SESSION['exam_id']; // This should now be set

    if (!$examId) {
        // Handle the case where exam_id is not set
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    // Define the correct answers
    $correctAnswers = [
        'question1' => 'A',        
    ];

    // Retrieve user's submitted answers from POST request
    $userAnswers = $request->getParsedBody();

    // Calculate the total correct answers
    $totalCorrect = 0;
    foreach ($correctAnswers as $question => $correctAnswer) {
        if (isset($userAnswers[$question]) && $userAnswers[$question] === $correctAnswer) {
            $totalCorrect++;
        }
    }

    // Save the total correct answers to the exam_results table
    $userId = $_SESSION['user_id'];

    // Insert or update the exam result in the exam_results table
    $db = new ExamResult();
    $examResult = $db->where('user_id', $userId)->where('exam_id', $examId)->first();

    if ($examResult) {
        // Update existing result
        $examResult->listening_score = $totalCorrect;
        $examResult->save();
    } else {
        // Create new result
        $db->create([
            'user_id' => $userId,
            'exam_id' => $examId,
            'listening_score' => $totalCorrect,
        ]);
    }

    // Move to Writing section
    $_SESSION['current_section'] = 'writing';

    return $response
        ->withHeader('Location', '/writing')
        ->withStatus(302);
});

// Writing section route (GET)
$app->get('/writing', function (Request $request, Response $response, array $args) {
    if (!isset($_SESSION['user_id']) || $_SESSION['current_section'] !== 'writing') {
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    ob_start();
    require __DIR__ . '/views/writing.php';
    $output = ob_get_clean();
    
    $response->getBody()->write($output);
    return $response;
});

// Handle Writing section submission (POST)
$app->post('/writing', function (Request $request, Response $response, array $args) {
    if (!isset($_SESSION['user_id']) || $_SESSION['current_section'] !== 'writing') {
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    $examId = $_SESSION['exam_id'];

    if (!$examId) {
        // Handle the case where exam_id is not set
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    // Define the correct answers for the Writing section
    $correctAnswers = [
        'question1' => 'B',
        // Add more questions and answers as needed
    ];

    // Retrieve user's submitted answers from POST request
    $userAnswers = $request->getParsedBody();

    // Calculate the total correct answers
    $totalCorrect = 0;
    foreach ($correctAnswers as $question => $correctAnswer) {
        if (isset($userAnswers[$question]) && $userAnswers[$question] === $correctAnswer) {
            $totalCorrect++;
        }
    }

    // Save the total correct answers to the exam_results table
    $userId = $_SESSION['user_id'];

    // Insert or update the exam result in the exam_results table
    $db = new ExamResult();
    $examResult = $db->where('user_id', $userId)->where('exam_id', $examId)->first();

    if ($examResult) {
        // Update existing result
        $examResult->writing_score = $totalCorrect;
        $examResult->save();
    } else {
        // Create new result
        $db->create([
            'user_id' => $userId,
            'exam_id' => $examId,
            'writing_score' => $totalCorrect,
        ]);
    }

    // Move to Reading section
    $_SESSION['current_section'] = 'reading';

    return $response
        ->withHeader('Location', '/reading')
        ->withStatus(302);
});

// Reading section route (GET)
$app->get('/reading', function (Request $request, Response $response, array $args) {
    if (!isset($_SESSION['user_id']) || $_SESSION['current_section'] !== 'reading') {
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    ob_start();
    require __DIR__ . '/views/reading.php';
    $output = ob_get_clean();
    
    $response->getBody()->write($output);
    return $response;
});

// Handle Reading section submission (POST)
$app->post('/reading', function (Request $request, Response $response, array $args) {
    if (!isset($_SESSION['user_id']) || $_SESSION['current_section'] !== 'reading') {
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    $examId = $_SESSION['exam_id'];

    if (!$examId) {
        // Handle the case where exam_id is not set
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    // Define the correct answers for the Reading section
    $correctAnswers = [
        'question1' => 'C',
        // Add more questions and answers as needed
    ];

    // Retrieve user's submitted answers from POST request
    $userAnswers = $request->getParsedBody();

    // Calculate the total correct answers
    $totalCorrect = 0;
    foreach ($correctAnswers as $question => $correctAnswer) {
        if (isset($userAnswers[$question]) && $userAnswers[$question] === $correctAnswer) {
            $totalCorrect++;
        }
    }

    // Save the total correct answers to the exam_results table
    $userId = $_SESSION['user_id'];

    // Insert or update the exam result in the exam_results table
    $db = new ExamResult();
    $examResult = $db->where('user_id', $userId)->where('exam_id', $examId)->first();

    if ($examResult) {
        // Update existing result
        $examResult->reading_score = $totalCorrect;
        $examResult->save();
    } else {
        // Create new result
        $db->create([
            'user_id' => $userId,
            'exam_id' => $examId,
            'reading_score' => $totalCorrect,
        ]);
    }   

    // Mark the exam as completed
    $exam = Exam::where('user_id', $userId)
                ->where('status_id', 1) // Ensure the exam is in Progress
                ->first();

    if ($exam) {
        // Update the exam status to Completed
        $exam->status_id = 2; // Completed
        $exam->save();
    }

    // Clear the current_section from the session
    unset($_SESSION['current_section']);

    // Redirect to a completion page
    return $response
        ->withHeader('Location', '/complete')
        ->withStatus(302);
});