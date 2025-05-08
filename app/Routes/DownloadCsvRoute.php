<?php 

use App\Models\User;
use Slim\Psr7\Response;
use Slim\Psr7\Stream;

// CSV download route
$app->post('/download-csv', function ($request, $response, $args) {
    // Get the selected exam_code from the form
    $data = $request->getParsedBody();
    $examCode = $data['exam_code'];

    // Fetch user data filtered by exam_code
    $users = User::where('exam_code', $examCode)->get();

    // Create a temporary file stream
    $tempFile = tmpfile();
    $tempStream = new Stream($tempFile);

    // CSV headers
    $headers = ['NO', 'NAMA', 'USERNAME', 'PASSWORD'];
    fputcsv($tempFile, $headers);

    // Write user data to CSV
    foreach ($users as $index => $user) {
        $nama = strtoupper($user->name) . ' ' . strtoupper($user->middle_name) . ' ' . strtoupper($user->last_name);
        fputcsv($tempFile, [
            $index + 1,         // NO
            $nama,              // NAMA
            $user->username,    //
            $user->password     // USERNAME
        ]);
    }

    // Rewind the stream for reading
    rewind($tempFile);

    // Set headers for CSV download
    return $response->withHeader('Content-Type', 'text/csv')
                    ->withHeader('Content-Disposition', 'attachment; filename="peserta.csv"')
                    ->withBody($tempStream); // Use the temporary stream
});
