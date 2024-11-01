<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Vérifie la validité du reCAPTCHA
    $recaptchaSecret = 'YOUR_SECRET_KEY'; // Remplace par ta clé secrète
    $recaptchaResponse = $_POST['g-recaptcha-response'];
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array(
        'secret' => $recaptchaSecret,
        'response' => $recaptchaResponse
    );

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $verify = file_get_contents($url, false, $context);
    $captchaSuccess = json_decode($verify);

    if ($captchaSuccess->success) {
        if (!empty($name) && !empty($email) && !empty($message)) {
            $to = "votre-email@example.com";
            $subject = "Nouveau message de contact de $name";
            $body = "Nom: $name\nEmail: $email\n\nMessage:\n$message\n";
            $headers = "From: $email\r\nReply-To: $email\r\n";

            if (mail($to, $subject, $body, $headers)) {
                echo "<p>Merci pour votre message, $name ! Je vous répondrai bientôt.</p>";
            } else {
                echo "<p>Une erreur est survenue. Veuillez réessayer plus tard.</p>";
            }
        } else {
            echo "<p>Veuillez remplir tous les champs du formulaire.</p>";
        }
    } else {
        echo "<p>Échec de la vérification reCAPTCHA. Veuillez réessayer.</p>";
    }
}
?>
