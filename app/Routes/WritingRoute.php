<?php
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use App\Models\ExamResult;

$app->get('/writing', function (Request $request, Response $response, array $args) {
    if (!isset($_SESSION['user_id']) || $_SESSION['current_section'] !== 'writing') {
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    ob_start();
    require __DIR__ . '/../../views/writing.php';
    $output = ob_get_clean();
    
    $response->getBody()->write($output);
    return $response;
});

$app->post('/writing', function (Request $request, Response $response, array $args) {
    if (!isset($_SESSION['user_id']) || $_SESSION['current_section'] !== 'writing') {
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

    $correctAnswers = ['question1' => 'A', 'question2' => 'D', 'question3' => 'C', 'question4' => 'D', 'question5' => 'B', 'question6' => 'B', 'question7' => 'B', 'question8' => 'A', 'question9' => 'D', 'question10' => 'C', 'question11' => 'D', 'question12' => 'C', 'question13' => 'B', 'question14' => 'A', 'question15' => 'A', 'question16' => 'A', 'question17' => 'D', 'question18' => 'A', 'question19' => 'A', 'question20' => 'D', 'question21' => 'B', 'question22' => 'B', 'question23' => 'A', 'question24' => 'C', 'question25' => 'B', 'question26' => 'C', 'question27' => 'D', 'question28' => 'B', 'question29' => 'D', 'question30' => 'D', 'question31' => 'C', 'question32' => 'D', 'question33' => 'A', 'question34' => 'B', 'question35' => 'A', 'question36' => 'C', 'question37' => 'B', 'question38' => 'C', 'question39' => 'A', 'question40' => 'A'];
    $userAnswers = $request->getParsedBody();
    $totalCorrect = 0;
    foreach ($correctAnswers as $question => $correctAnswer) {
        if (isset($userAnswers[$question]) && $userAnswers[$question] === $correctAnswer) {
            $totalCorrect++;
        }
    }

    $userId = $_SESSION['user_id'];
    $db = new ExamResult();
    $examResult = $db->where('user_id', $userId)->where('exam_id', $examId)->first();

    if ($examResult) {
        $examResult->writing_score = $totalCorrect;
        $examResult->save();
    } else {
        $db->create([
            'user_id' => $userId,
            'exam_id' => $examId,
            'writing_score' => $totalCorrect,
        ]);
    }

    $_SESSION['current_section'] = 'reading';

    $response->getBody()->write("
        <html>
        <head>
            <title>Submission Successful</title>
            <script type='text/javascript'>
                setTimeout(function() {
                    window.location.href = '/reading';
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

    // return $response
    //     ->withHeader('Location', '/reading')
    //     ->withStatus(302);
});
