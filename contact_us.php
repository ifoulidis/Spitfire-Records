<?php
session_start();
include("includes/header.php");
include("includes/db.php");
?>

<main>
  <div class="contact__container">
    <div class="contact__form">
      <h2>Contact Us</h2>
      <?php
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        // Get the form data
        $name = $_POST['name'];
        $email = $_POST['email'];
        $message = $_POST['message'];

        // Validate the form data (you can add more validation if needed)
        $errors = [];
        if (empty($name)) {
          $errors[] = "Name is required";
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $errors[] = "Invalid email address";
        }
        if (empty($message)) {
          $errors[] = "Message is required";
        }

        // If there are no errors, send the email
        if (empty($errors)) {
          $to = "isaiahemails@gmail.com"; // Replace with your email address
          $subject = "Contact Form Submission";
          $body = "Name: $name\nEmail: $email\nMessage: $message";
          $headers = "From: $email";

          if (mail($to, $subject, $body, $headers)) {
            echo '<div class="success">Thank you for contacting us!</div>';
          } else {
            echo '<div class="error">Sorry, there was an error sending your message. Please try again later.</div>';
          }
        } else {
          // Display validation errors
          foreach ($errors as $error) {
            echo "<div class='error'>$error</div>";
          }
        }
      }
      ?>
      <form id="contactForm" method="POST">
        <div class="form-group">
          <label for="name">Name:</label>
          <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
          <label for="message">Message:</label>
          <textarea id="message" name="message" rows="5" required></textarea>
        </div>
        <button type="submit" name="submit">Submit</button>
      </form>
    </div>
  </div>
</main>

<?php
include("includes/footer.php");
?>

</body>

</html>