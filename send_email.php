<?php
// Function to send email to the customer
function sendEmailToCustomer($emailAddress)
{
  $to = $emailAddress;
  $subject = 'Payment Confirmation';
  $message = 'Thank you for your payment.';
  $headers = 'From: admin@spitfirerecords.co.nz' . "\r\n" .
    'Reply-To: admin@spitfirerecords.co.nz' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

  if (mail($to, $subject, $message, $headers)) {
    echo 'Email sent successfully.';
  } else {
    echo 'Email could not be sent.';
  }
}

// Get the email address from the AJAX request
$emailAddress = $_POST['email'];

// Call the sendEmailToCustomer function
sendEmailToCustomer($emailAddress);


// //Version using PHPMailer

// require 'path/to/PHPMailer/PHPMailerAutoload.php';

// // Function to send email to the customer
// function sendEmailToCustomer($emailAddress)
// {
//   // Create a new PHPMailer instance
//   $mail = new PHPMailer;

//   // Configure the email settings
//   $mail->isSMTP();
//   $mail->Host = 'your_smtp_host';
//   $mail->SMTPAuth = true;
//   $mail->Username = 'your_email_username';
//   $mail->Password = 'your_email_password';
//   $mail->Port = 587; // Update with your SMTP port

//   $mail->setFrom('your_email@example.com', 'Your Name');
//   $mail->addAddress($emailAddress);

//   // Set the email content
//   $mail->Subject = 'Payment Confirmation';
//   $mail->Body = 'Thank you for your payment.';

//   // Send the email
//   if (!$mail->send()) {
//     echo 'Email could not be sent.';
//     echo 'Mailer Error: ' . $mail->ErrorInfo;
//   } else {
//     echo 'Email sent successfully.';
//   }
// }

// // Get the email address from the AJAX request
// $emailAddress = $_POST['email'];

// // Call the sendEmailToCustomer function
// sendEmailToCustomer($emailAddress);

?>