<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Set your email address here
    $to = "sino2901@gmail.com";

    // Email headers
    $headers = "From: $name <$email>" . "\r\n" .
               "Reply-To: $email" . "\r\n" .
               "X-Mailer: PHP/" . phpversion();

    // Check if there are files uploaded
    if(isset($_FILES['attachments'])){
        $attachments = array();
        $uploaded_files = $_FILES['attachments'];

        // Loop through each file
        for($i=0; $i<count($uploaded_files['name']); $i++) {
            $tmp_name = $uploaded_files['tmp_name'][$i];
            $name = $uploaded_files['name'][$i];

            // Check if file is uploaded successfully
            if(move_uploaded_file($tmp_name, "attachments/" . $name)) {
                // Add file to attachments array
                $attachments[] = "attachments/" . $name;
            }
        }

        // Add attachments to email
        foreach ($attachments as $attachment) {
            $file = chunk_split(base64_encode(file_get_contents($attachment)));
            $headers .= "Content-Type: application/octet-stream; name=\"" . basename($attachment) . "\"\r\n";
            $headers .= "Content-Transfer-Encoding: base64\r\n";
            $headers .= "Content-Disposition: attachment; filename=\"" . basename($attachment) . "\"\r\n";
            $headers .= "X-Attachment-Id: " . rand(1000, 99999) . "\r\n";

            // Read attachment file content
            $attachment_content = file_get_contents($attachment);

            // Encode attachment content in base64
            $attachment_base64 = chunk_split(base64_encode($attachment_content));

            // Attach file to email
            $message .= "--PHP-mixed-" . $name . "\r\n" .
                        "Content-Type: application/octet-stream; name=\"" . $name . "\"\r\n" .
                        "Content-Transfer-Encoding: base64\r\n" .
                        "Content-Disposition: attachment\r\n\r\n" .
                        $attachment_base64 . "\r\n";
        }
    }

    // Send email
    if (mail($to, $subject, $message, $headers)) {
        echo "Email sent successfully!";
    } else {
        echo "Failed to send email. Please try again later.";
    }
}
?>
