<?php
namespace App\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Slim\Psr7\Stream;

use App\Models\User;
use App\Models\Exam;
use App\Models\ExamResult;

class AdminMiddleware
{
    public function __invoke(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $role_id = $_SESSION['role_id'] ?? null;

        if ($role_id !== 1) {
            $response = new Response();
            $body = new Stream(fopen('php://temp', 'r+'));
            $body->write('Forbidden');
            $body->rewind();
            return $response->withStatus(403)
                            ->withHeader('Content-Type', 'text/plain')
                            ->withBody($body);
        }

        return $handler->handle($request);
    }
}

// reset exam
$app->get('/reset-exam/{id}', function ($request, $response, $args) {
    $userId = $args['id'];

    // Fetch the exam and exam results based on user_id
    $exam = Exam::where('user_id', $userId)->first();
    $examResults = ExamResult::where('user_id', $userId)->get();
    $user = User::find($userId); // Fetch the user

    if ($exam) {
        // Delete all exam results for the user
        foreach ($examResults as $result) {
            $result->delete();
        }

        // Delete the exam record for the user
        $exam->delete();

        // Mark the user as needing a reset (set reset_required to true)
        if ($user) {
            $user->reset_required = true;
            $user->save();
        }

        // Set a success message
        $_SESSION['message_notification'] = "Exam for user ID {$userId} has been reset.";
    } else {
        // Set an error message if no exam found
        $_SESSION['message_notification'] = "No exam found for user ID {$userId}.";
    }

    // Redirect back to the dashboard with a success or error message
    return $response->withHeader('Location', '/dashboard')->withStatus(302);
});

$app->get('/delete-usr/{id}', function ($request, $response, $args) {
    $userId = $args['id'];
   
    // Fetch the user, exam, and exam results based on user_id
    $user = User::find($userId);
    $exam = Exam::where('user_id', $userId)->first();
    $examResults = ExamResult::where('user_id', $userId)->get();

    if ($user) {
        // Delete all exam results for the user
        foreach ($examResults as $result) {
            $result->delete();
        }

        // Delete the exam record for the user if it exists
        if ($exam) {
            $exam->delete();
        }

        // Delete the user record
        $user->delete();

        // Set a success message
        $_SESSION['message_notification'] = "User ID {$userId} and their associated data have been deleted.";
    } else {
        // Set an error message if no user found
        $_SESSION['message_notification'] = "No user found with ID {$userId}.";
    }

    // Redirect back to the dashboard with a success or error message
    return $response->withHeader('Location', '/dashboard')->withStatus(302);
})->add(new AdminMiddleware());

$app->get('/get-reset-flag', function ($request, $response, $args) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        return $response->withStatus(403); // Unauthorized
    }

    $userId = $_SESSION['user_id'];
    $user = User::find($userId);

    if ($user) {
        // Prepare JSON response
        $data = ['reset_required' => $user->reset_required];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    return $response->withStatus(404); // Not Found
});

$app->post('/clear-reset-flag', function ($request, $response, $args) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        return $response->withStatus(403); // Unauthorized
    }

    $userId = $_SESSION['user_id'];
    $user = User::find($userId);
    if ($user) {
        $user->reset_required = false; // Reset the flag
        $user->save();

        // Prepare JSON response
        $data = ['message' => 'Reset flag cleared'];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    return $response->withStatus(404); // Not Found
});