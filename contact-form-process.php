<?php
session_start();

if (isset($_POST['Email'])) {
    // Your existing form processing code...
    // EDIT THE EMAIL TO YOUR PREFERRED EMAIL ADDRESS
    $email_to = "info@llmedia.biz";
    $email_subject = "New form submission";

    // Function to handle errors
    function problem($error)
    {
        echo "We're sorry, but there were errors found with the form you submitted. ";
        echo "Please see below for details:<br><br>";
        echo $error . "<br><br>";
        echo "Please go back and correct these errors.<br><br>";
        die();
    }

    // Validation expected data exists
    if (
        !isset($_POST['Name']) ||
        !isset($_POST['Email']) ||
        !isset($_POST['Phone']) ||
        !isset($_POST['Message'])
    ) {
        problem('We are sorry, but there appears to be a problem with the form you submitted.');
    }

    // Sanitize and validate inputs
    $name = filter_var($_POST['Name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['Email'], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($_POST['Phone'], FILTER_SANITIZE_STRING);
    $message = filter_var($_POST['Message'], FILTER_SANITIZE_STRING);

    // Validate email format
    $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
    if (!preg_match($email_exp, $email)) {
        problem('The Email address you entered does not appear to be valid. Please enter a valid email address.');
    }

    // Validate name and message format
    $string_exp = "/^[A-Za-z .',-]+$/";
    if (!preg_match($string_exp, $name)) {
        problem('The Name you entered does not appear to be valid. Please keep it simple, and use only letters, commas, or periods.');
    }

    if (strlen($message) < 2) {
        problem('The Message you entered does not appear to be valid.');
    }

    // Validate phone format
    $phone_exp = "/^\+?[0-9]{10,14}$/"; // Accepts phone numbers with or without country code, 10-14 digits
    if (!preg_match($phone_exp, $phone)) {
        problem('The Phone Number you entered does not appear to be valid. Please enter a valid phone number.');
    }

    // Construct email message
    $email_message = "Form details below.\n\n";
    $email_message .= "Name: " . $name . "\n";
    $email_message .= "Email: " . $email . "\n";
    $email_message .= "Phone: " . $phone . "\n";
    $email_message .= "Message: " . $message . "\n";

    // Create email headers
    $headers = 'From: ' . $email . "\r\n" .
        'Reply-To: ' . $email . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    // Send email
    @mail($email_to, $email_subject, $email_message, $headers);

    // Reset session
    session_regenerate_id(true);
    unset($_SESSION['form_submitted']);
    $_SESSION['form_submitted'] = true;

    // Redirect after successful submission
    header('Location: index.php?success=true');
    exit();
}
?>
<!--
    // reCAPTCHA Secret Key obtained from the reCAPTCHA admin console
    $recaptcha_secret_key = '6LeCA9spAAAAAN8p3YX6BfSZWFFkmLqtzvwiKpU7';

    // Verify reCAPTCHA response
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_data = array(
        'secret' => $recaptcha_secret_key,
        'response' => $recaptcha_response
    );

    $recaptcha_options = array(
        'http' => array(
            'method' => 'POST',
            'content' => http_build_query($recaptcha_data)
        )
    );

    $recaptcha_context = stream_context_create($recaptcha_options);
    $recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);
    $recaptcha_success = json_decode($recaptcha_result)->success;

    if (!$recaptcha_success) {
        problem('reCAPTCHA verification failed. Please complete the reCAPTCHA challenge.');
    }
-->