<?php
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use App\Models\ExamResult;
use App\Models\User;


$app->get('/listening', function (Request $request, Response $response, array $args) {
    if (!isset($_SESSION['user_id']) || $_SESSION['current_section'] !== 'listening') {
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    ob_start();
    require __DIR__ . '/../../views/listening.php';
    $output = ob_get_clean();
    
    $response->getBody()->write($output);
    return $response;
});

$app->post('/listening', function (Request $request, Response $response, array $args) {
    if (!isset($_SESSION['user_id']) || $_SESSION['current_section'] !== 'listening') {
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    // Check if reset_required is 1
    $userId = $_SESSION['user_id'];
    $user = new User(); // Assuming you have a User model
    $userData = $user->find($userId);
    
    if ($userData && $userData->reset_required == 1) {
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    $examId = $_SESSION['exam_id'];

    if (!$examId) {
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    $correctAnswers = ['question1' => 'D', 'question2' => 'D', 'question3' => 'A', 'question4' => 'D', 'question5' => 'C', 'question6' => 'D', 'question7' => 'A', 'question8' => 'C', 'question9' => 'B', 'question10' => 'A', 'question11' => 'A', 'question12' => 'A', 'question13' => 'C', 'question14' => 'D', 'question15' => 'C', 'question16' => 'B', 'question17' => 'A', 'question18' => 'C', 'question19' => 'B', 'question20' => 'B', 'question21' => 'D', 'question22' => 'D', 'question23' => 'A', 'question24' => 'D', 'question25' => 'A', 'question26' => 'C', 'question27' => 'C', 'question28' => 'D', 'question29' => 'D', 'question30' => 'B', 'question31' => 'B', 'question32' => 'C', 'question33' => 'A', 'question34' => 'D', 'question35' => 'A', 'question36' => 'D', 'question37' => 'B', 'question38' => 'C', 'question39' => 'B', 'question40' => 'C', 'question41' => 'D', 'question42' => 'D', 'question43' => 'B', 'question44' => 'B', 'question45' => 'B', 'question46' => 'A', 'question47' => 'D', 'question48' => 'B', 'question49' => 'D', 'question50' => 'B'];
    $userAnswers = $request->getParsedBody();
    $totalCorrect = 0;
    foreach ($correctAnswers as $question => $correctAnswer) {
        if (isset($userAnswers[$question]) && $userAnswers[$question] === $correctAnswer) {
            $totalCorrect++;
        }
    }

    $db = new ExamResult();
    $examResult = $db->where('user_id', $userId)->where('exam_id', $examId)->first();

    if ($examResult) {
        $examResult->listening_score = $totalCorrect;
        $examResult->save();
    } else {
        $db->create([
            'user_id' => $userId,
            'exam_id' => $examId,
            'listening_score' => $totalCorrect,
        ]);
    }

    $_SESSION['current_section'] = 'writing';

    $response->getBody()->write("
        <html>
        <head>
            <title>Submission Successful</title>
            <script type='text/javascript'>
                setTimeout(function() {
                    window.location.href = '/writing';
                }, 3000); // Redirect after 3 seconds
            </script>
            <style type='text/css'>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    text-align: center;
                    padding: 50px;
                }
                .notification {
                    background-color: #4CAF50;
                    color: white;
                    padding: 15px;
                    border-radius: 5px;
                    display: inline-block;
                    font-size: 18px;
                    margin-bottom: 20px;
                    line-height: 1.4;
                }
            </style>
        </head>
        <body>
            <div class='notification'>Jawaban anda sudah tersimpan.<br>Silahkan tunggu untuk sesi selanjutnya...</div>
            <div class='loading-gif'>
                <img src='/assets/img/180-ring.svg' alt='Loading...'>
            </div>
        </body>
        </html>
    ");

    return $response;
});