<?php
session_start();
header('Content-Type: application/json');

$notesFile = __DIR__ . '/notes.txt';

// Initialize notes file if it doesn't exist
if (!file_exists($notesFile)) {
    file_put_contents($notesFile, '');
}

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Save a new note
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $error = [];

    // Validate input
    if (empty($title)) {
        $error[] = 'Title is required';
    }
    if (empty($content)) {
        $error[] = 'Content is required';
    }

    if (!empty($error)) {
        http_response_code(400);
        $_SESSION['error'] = implode(', ', $error);
        echo json_encode(['error' => $_SESSION['error']]);
        exit;
    }

    // Read existing notes to determine new ID
    $notes = file($notesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $newId = count($notes) + 1;

    // Prepare new note
    $newNote = "$newId|$title|$content|" . date('Y-m-d H:i:s');

    // Append note to file
    if (file_put_contents($notesFile, $newNote . PHP_EOL, FILE_APPEND) === false) {
        http_response_code(500);
        $_SESSION['error'] = 'Failed to save note';
        echo json_encode(['error' => $_SESSION['error']]);
        exit;
    }

    $_SESSION['message'] = 'Note saved successfully';
    echo json_encode(['message' => $_SESSION['message']]);
} elseif ($method === 'GET') {
    // Retrieve all notes
    $notes = file($notesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $notesArray = [];

    foreach ($notes as $note) {
        $fields = explode('|', $note);
        if (count($fields) === 4) {
            $notesArray[] = [
                'id' => (int)$fields[0],
                'title' => $fields[1],
                'content' => $fields[2],
                'created_at' => $fields[3]
            ];
        }
    }

    echo json_encode($notesArray);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>