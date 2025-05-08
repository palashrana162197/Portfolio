<?php
// Display a basic test message
echo "hi<br>";

// Check if the form is submitted
if (isset($_POST['full_name'])) {
    echo "Form received<br>";

    // Database connection details
    $server = "localhost";
    $username = "root";
    $password = ""; // Change if needed
    $database = "portfolio_my"; // Updated DB name

    // Establish DB connection
    $connection = new mysqli($server, $username, $password, $database);

    // Check DB connection
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    echo "Connected to DB<br>";

    // Retrieve and sanitize inputs
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $mobile = htmlspecialchars(trim($_POST['mobile']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Optional validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }
    if (!preg_match('/^\d{10,15}$/', $mobile)) {
        die("Invalid mobile number. Only 10-15 digits allowed.");
    }

    // Prepare insert SQL
    $stmt = $connection->prepare("INSERT INTO `contact_us` (full_name, email, mobile, subject, message) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $connection->error);
    }

    // Bind and execute
    $stmt->bind_param("sssss", $full_name, $email, $mobile, $subject, $message);

    if ($stmt->execute()) {
        echo "Data inserted successfully.<br>";
    } else {
        echo "Insert failed: " . $stmt->error;
    }

    $stmt->close();
    $connection->close();

    // Email setup
    $to = 'palashbhair@gmail.com'; // Your receiving email
    $mail_subject = 'New Contact Form Submission: ' . $subject;
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $email_body = "Name: $full_name\nEmail: $email\nMobile: $mobile\nSubject: $subject\nMessage:\n$message";

    if (mail($to, $mail_subject, $email_body, $headers)) {
        echo "Email sent successfully.";
    } else {
        echo "Email sending failed.";
    }

} else {
    echo "Form not submitted correctly.";
}
?>
