<?php
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use App\Models\Exam;

$app->post('/start-exam', function (Request $request, Response $response, array $args) {
    if (!isset($_SESSION['user_id'])) {
        return $response
            ->withHeader('Location', '/')
            ->withStatus(302);
    }

    $userId = $_SESSION['user_id'];
    
    $existingExam = Exam::where('user_id', $userId)->first();

    if ($existingExam) {
        $_SESSION['error_message'] = 'You have already started an exam. Please contact administrator to fix it.';
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    $exam = new Exam();
    $exam->user_id = $userId;
    $exam->status_id = 1; // In Progress
    $exam->save();

    $_SESSION['exam_id'] = $exam->id;
    $_SESSION['current_section'] = 'listening';

    return $response
        ->withHeader('Location', '/listening')
        ->withStatus(302);
});
