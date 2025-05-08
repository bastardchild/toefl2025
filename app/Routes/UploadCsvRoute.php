<?php
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use App\Models\User;

// Route to upload CSV file
$app->post('/upload-csv', function (Request $request, Response $response, $args) {
    $uploadedFiles = $request->getUploadedFiles();
    $file = $uploadedFiles['csv'];

    if ($file->getError() === UPLOAD_ERR_OK) {
        // Save the file to the 'uploads' directory
        $filename = moveUploadedFile('uploads', $file);
        
        // Process the CSV file
        if (($handle = fopen("uploads/" . $filename, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                // Create user with name, middle_name, last_name, created_at, and exam_code
                $userData = [
                    'name' => $data[0] ?? '',
                    'middle_name' => $data[1] ?? '',
                    'last_name' => $data[2] ?? '',
                    'role_id' => 2, // Assign default role_id
                    'created_at' => date('Y-m-d H:i:s'), // Add created_at column
                    'exam_code' => $data[3] ?? '', // Add exam_code and default to an empty string if not set
                ];

                $user = User::create($userData);
                
                // Generate username based on name, user_id, and 2 random characters
                $randomString = substr(str_shuffle('1234567890'), 0, 2);
                $username = strtolower($data[0]) . $user->id . $randomString;
                
                // Set the username as the password as well
                $user->username = $username;
                $user->password = $username; // Use the username directly as the password
                $user->save();
            }
            fclose($handle);
            
            // Set success message in session
            $_SESSION['message_notification'] = "CSV file uploaded and processed successfully.";
            return $response->withHeader('Location', '/dashboard')->withStatus(302);
        } else {
            // Set error message in session
            $_SESSION['message_notification'] = "Error processing CSV file.";
            return $response->withHeader('Location', '/dashboard')->withStatus(302);
        }
    } else {
        // Set error message in session
        $_SESSION['message_notification'] = "Error uploading file.";
        return $response->withHeader('Location', '/dashboard')->withStatus(302);
    }
});

// Helper function to move the uploaded file
function moveUploadedFile($directory, $uploadedFile) {
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(8)); // Random name for the file
    $filename = sprintf('%s.%0.8s', $basename, $extension);
    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
    return $filename;
}