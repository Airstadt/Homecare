<?php
// submit.php
// Update these to match your hosting environment
$dbHost = 'localhost';
$dbName = 'ezyro_39442871_caretaker';
$dbUser = 'YOUR_DB_USERNAME';
$dbPass = 'YOUR_DB_PASSWORD';
$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

try {
    // Create PDO with exceptions
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // In production, log this and show a generic error message
    http_response_code(500);
    echo 'Database connection failed.';
    exit;
}

// Helper to get and sanitize POST values
function get_post($key, $default = '') {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

// Collect and sanitize inputs
$firstName = substr(filter_var(get_post('firstName'), FILTER_SANITIZE_STRING), 0, 50);
$lastName  = substr(filter_var(get_post('lastName'), FILTER_SANITIZE_STRING), 0, 50);
$email     = substr(filter_var(get_post('email'), FILTER_SANITIZE_EMAIL), 0, 50);
$phone     = substr(filter_var(get_post('phone'), FILTER_SANITIZE_STRING), 0, 50);
$details   = substr(filter_var(get_post('details'), FILTER_SANITIZE_STRING), 0, 500);

// Checkboxes: set 1 if present, 0 otherwise
$urgent    = isset($_POST['urgent']) ? 1 : 0;
$nonurgent = isset($_POST['nonurgent']) ? 1 : 0;

// Basic server-side validation for required fields
$errors = [];
if ($firstName === '') $errors[] = 'First name is required.';
if ($lastName === '')  $errors[] = 'Last name is required.';

if (!empty($errors)) {
    // Show simple error list; you can redirect back with messages instead
    http_response_code(400);
    echo '<h2>Submission error</h2><ul>';
    foreach ($errors as $err) {
        echo '<li>' . htmlspecialchars($err) . '</li>';
    }
    echo '</ul>';
    exit;
}

// Insert into the table schedule_free_consultation
try {
    $sql = "INSERT INTO `schedule_free_consultation`
      (`firstName`,`lastName`,`email`,`phone`,`details`,`urgent`,`nonurgent`,`cust_1`,`cust_2`)
      VALUES
      (:firstName,:lastName,:email,:phone,:details,:urgent,:nonurgent,0,0)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':firstName', $firstName, PDO::PARAM_STR);
    $stmt->bindValue(':lastName',  $lastName,  PDO::PARAM_STR);
    $stmt->bindValue(':email',     $email,     PDO::PARAM_STR);
    $stmt->bindValue(':phone',     $phone,     PDO::PARAM_STR);
    $stmt->bindValue(':details',   $details,   PDO::PARAM_STR);
    $stmt->bindValue(':urgent',    $urgent,    PDO::PARAM_INT);
    $stmt->bindValue(':nonurgent', $nonurgent, PDO::PARAM_INT);

    $stmt->execute();

    // On success, redirect to thank you page
    header('Location: thankyou.html');
    exit;
} catch (PDOException $e) {
    // In production log $e->getMessage() and show generic error
    http_response_code(500);
    echo 'There was an error saving your submission.';
    exit;
}
