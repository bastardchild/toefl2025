<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Asia/Bangkok');

require 'vendor/autoload.php';
require 'config/database.php';

use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\User;
use App\Models\Exam;
use App\Models\ExamResult;

// Set session lifetime in your SlimPHP application
ini_set('session.gc_maxlifetime', 86400); // 24 hours (86400 seconds)
session_set_cookie_params(86400); // Set the cookie lifetime to 24 hours
session_start();

$app = AppFactory::create(); // Create Slim app

$app->addRoutingMiddleware();
// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Middleware to restrict access to Firefox on PC/Laptop only
$app->add(function ($request, $handler) {
    $userAgent = $request->getHeaderLine('User-Agent');

    // Check if the browser is Firefox
    $isFirefox = strpos($userAgent, 'Firefox') !== false;

    // Detect mobile devices based on common User-Agent substrings
    $isMobile = preg_match('/Mobile|Android|iPhone|iPad|iPod|Windows Phone/i', $userAgent);

    // Block if the browser is not Firefox or if it's a mobile device
    if (!$isFirefox || $isMobile) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write('Access restricted: Only Firefox on PC/Laptop is allowed');
        return $response->withStatus(403); // Forbidden
    }

    // Proceed to the next middleware or route
    return $handler->handle($request);
});

// Include routes after app is created
require 'app/Routes/DashboardRoute.php';
require 'app/Routes/StartExamRoute.php';
require 'app/Routes/ListeningRoute.php';
require 'app/Routes/WritingRoute.php';
require 'app/Routes/ReadingRoute.php';
require 'app/Routes/CompleteRoute.php';
require 'app/Routes/UploadCsvRoute.php';
require 'app/Routes/DownloadCsvRoute.php';
require 'app/Routes/ResetRoute.php';


// Render login view as the homepage
$app->get('/', function ($request, $response, $args) {
    if (isset($_SESSION['user_id'])) {
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    ob_start();
    require __DIR__ . '/views/login.php';
    $output = ob_get_clean();
    
    $response->getBody()->write($output);
    return $response;
});

// Handle login form submission
$app->post('/', function ($request, $response, $args) {
    
    $delay = rand(350000, 750000); // Random microseconds between 350,000 and 750,000
    usleep($delay);

    $data = $request->getParsedBody();
    $username = $data['username'];
    $password = $data['password'];

    $user = User::where('username', $username)->first();

    if ($user && $user->password === $password) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['role_id'] = $user->role_id;
        $_SESSION['name'] = $user->name;

        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    } else {
        $error = 'Invalid username or password';
        ob_start();
        require __DIR__ . '/views/login.php';
        $output = ob_get_clean();
        $response->getBody()->write($output);
    }

    return $response;
});

// Logout route
$app->get('/logout', function ($request, $response, $args) {
    session_destroy();
    return $response
        ->withHeader('Location', '/')
        ->withStatus(302);
});

// Route to handle image upload
$app->post('/upload-image', function ($request, $response, $args) {
    // Get parsed body and files
    $data = $request->getParsedBody();
    
    // Check if image data is set
    if (!isset($data['image'])) {
        $response->getBody()->write('Image data is missing');
        return $response->withStatus(400);
    }
    
    $image = $data['image'];
    
    // Ensure image data is not null
    if ($image === null) {
        $response->getBody()->write('Invalid image data');
        return $response->withStatus(400);
    }
    
    // Decode the base64 image
    $image = str_replace('data:image/png;base64,', '', $image);
    $image = str_replace(' ', '+', $image);
    $imageData = base64_decode($image);

    if (strlen($imageData) === 0) {
        $response->getBody()->write('Decoded image data is empty');
        return $response->withStatus(400);
    }
    
    // Generate a unique filename
    $filename = uniqid() . '.png';

    // Save image to uploads folder
    $uploadDir = __DIR__ . '/uploads/';
    $filePath = $uploadDir . $filename;

    // Ensure the directory exists and is writable
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
        $response->getBody()->write('Failed to create upload directory');
        return $response->withStatus(500);
    }

    // Save the image
    if (file_put_contents($filePath, $imageData) === false) {
        $response->getBody()->write('Failed to save image');
        return $response->withStatus(500);
    }

    // Retrieve user_id from the session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        $response->getBody()->write('User not authenticated');
        return $response->withStatus(403);
    }
    $userId = $_SESSION['user_id'];

    // Save the file path to the database
    $user = User::find($userId);
    $user->cam_image = $filename; // Save to 'cam_image' column
    $user->save();

    $response->getBody()->write('Image uploaded successfully');
    return $response->withStatus(200);
});

// Keep-alive route to refresh the session
$app->get('/keep-alive', function (Request $request, Response $response, $args) {
    if (isset($_SESSION['user_id'])) {
        // Respond with success if session is active
        $response->getBody()->write(json_encode(['status' => 'active']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    } else {
        // Respond with failure if session is not active
        $response->getBody()->write(json_encode(['status' => 'inactive']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }
});

$app->run();