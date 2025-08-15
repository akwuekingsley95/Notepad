
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notepad</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #e9ecef;
            padding: 20px;
        }
        .notepad-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-size: 24px;
        }
        .datetime {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .title-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .title-input:focus {
            outline: none;
            border-color: #007bff;
        }
        textarea {
            width: 100%;
            height: 150px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: none;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 10px;
        }
        textarea:focus {
            outline: none;
            border-color: #007bff;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .save-btn {
            background-color: #00ff80ff;
            color: white;
        }
        .save-btn:hover {
            background-color: #00ff80ff;
        }
        .clear-btn {
            background-color: #dc3545;
            color: white;
        }
        .clear-btn:hover {
            background-color: #c82333;
        }
        .notes-list {
            margin-top: 20px;
        }
        .note-item {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
        }
        .note-title {
            font-weight: bold;
            color: #333;
            font-size: 18px;
            margin-bottom: 5px;
        }
        .note-content {
            color: #555;
            font-size: 16px;
            margin-bottom: 5px;
        }
        .note-timestamp {
            color: #888;
            font-size: 12px;
        }
        .note-actions {
            margin-top: 10px;
        }
        .success {
            color: #28a745;
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .note-item button {
            margin-right: 5px;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        /* Modal styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal-overlay.show {
            display: flex;
            animation: fadeIn 0.3s ease-in;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .modal-content h3 {
            color: #dc3545;
            margin-bottom: 10px;
        }
        .modal-content p {
            color: #333;
            margin-bottom: 20px;
        }
        .btn-close {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-close:hover {
            background-color: #c82333;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="notepad-container">
        <h1>Notepad</h1>
        <div class="datetime"></div>
        <!-- Display success messages -->
        <div class="success">
            <?php
            if (isset($_SESSION['message'])) {
                echo htmlspecialchars($_SESSION['message']);
                unset($_SESSION['message']);
            }
            ?>
        </div>
        <!-- Form to create new notes -->
        <form action="./Backend/notes.php" method="post">
            <input type="text" class="title-input" name="title" placeholder="Enter note title...">
            <textarea name="content" placeholder="Write your notes here..."></textarea>
            <div class="button-group">
                <button type="submit" class="save-btn">Save</button>
                <button type="button" class="clear-btn">Clear</button>
            </div>
        </form>
        <!-- Display notes list, newest at the bottom -->
        <div class="notes-list">
            <?php
            $notesFile = './Backend/notes.txt';
            if (file_exists($notesFile)) {
                $notes = file($notesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                // Reverse notes to show newest at the bottom
                $notes = array_reverse($notes);
                foreach ($notes as $note) {
                    $fields = preg_split('/(?<!\\\\)\|/', $note);
                    if (count($fields) === 4) {
                        $id = (int)$fields[0];
                        $title = str_replace('\\|', '|', $fields[1]);
                        $content = str_replace('\\|', '|', $fields[2]);
                        $created_at = $fields[3];
                        echo '<div class="note-item">';
                        echo '<div class="note-title">' . htmlspecialchars($title) . '</div>';
                        echo '<div class="note-content">' . htmlspecialchars($content) . '</div>';
                        echo '<div class="note-timestamp">' . htmlspecialchars($created_at) . '</div>';
                        echo '<div class="note-actions">';
                        echo '<form action="./Backend/notes.php" method="post" style="display:inline;">';
                        echo '<input type="hidden" name="id" value="' . $id . '">';
                        echo '<input type="hidden" name="_method" value="DELETE">';
                        echo '<button type="submit" class="delete-btn">Delete</button>';
                        echo '</form>';
                        echo '</div>';
                        echo '</div>';
                    }
                }
            }
            ?>
        </div>
    </div>
    <!-- Error message modal -->
    <div class="modal-overlay" id="errorModal">
        <div class="modal-content">
            <h3>Error</h3>
            <p id="errorMessage"><?php echo isset($_SESSION['error']) ? htmlspecialchars($_SESSION['error']) : ''; ?></p>
            <button class="btn-close" onclick="closeModal()">OK</button>
        </div>
    </div>
    <script>
        // Update date and time every second
        function updateDateTime() {
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                timeZone: 'Africa/Lagos',
                timeZoneName: 'short'
            };
            const now = new Date().toLocaleString('en-US', options);
            document.querySelector('.datetime').textContent = now;
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);

        // Clear form and messages on button click
        document.querySelector('.clear-btn').addEventListener('click', () => {
            document.querySelector('form').reset();
            document.querySelector('.success').textContent = '';
            closeModal();
        });

        // Close error modal
        function closeModal() {
            document.getElementById('errorModal').classList.remove('show');
        }

        // Show error modal if message exists
        <?php
        if (isset($_SESSION['error'])) {
            echo 'document.getElementById("errorModal").classList.add("show");';
            unset($_SESSION['error']);
        }
        ?>
    </script>
</body>
</html>