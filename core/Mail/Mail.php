<?php

namespace Core\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    protected PHPMailer $mailer;

    /**
     * Initialize the mail object and configure it based on the environment.
     */
    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    /**
     * Configure the mailer object based on the environment.
     *
     * It will use the following environment variables to configure the mailer:
     *
     * - MAIL_HOST
     * - MAIL_USERNAME
     * - MAIL_PASSWORD
     * - MAIL_ENCRYPTION (default: tls)
     * - MAIL_PORT (default: 587)
     * - MAIL_FROM_ADDRESS
     * - MAIL_FROM_NAME
     *
     * The mailer will be configured to use SMTP with authentication and encryption.
     * The HTML content type will also be set.
     */
    protected function configure(): void
    {
        $this->mailer->isSMTP();
        $this->mailer->Host       = env('MAIL_HOST');
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = env('MAIL_USERNAME');
        $this->mailer->Password   = env('MAIL_PASSWORD');
        $this->mailer->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
        $this->mailer->Port       = env('MAIL_PORT', 587);

        $this->mailer->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        $this->mailer->isHTML(true);
    }

    /**
     * Add a recipient to the email.
     *
     * @param string $email The recipient's email address.
     * @param string $name The recipient's name (optional).
     * @return static
     */
    public function to(string $email, string $name = ''): static
    {
        $this->mailer->addAddress($email, $name);
        return $this;
    }

    /**
     * Set the subject of the email.
     *
     * @param string $subject The email subject.
     * @return static
     */
    public function subject(string $subject): static
    {
        $this->mailer->Subject = $subject;
        return $this;
    }

    /**
     * Set the body of the email.
     *
     * If the $alt parameter is not given, the HTML body will be stripped of its tags
     * and used as the alternative body.
     *
     * @param string $html The HTML body of the email.
     * @param string $alt The alternative body of the email (optional).
     * @return static
     */
    public function body(string $html, string $alt = ''): static
    {
        $this->mailer->Body    = $html;
        $this->mailer->AltBody = $alt ?: strip_tags($html);
        return $this;
    }

    
    /**
     * Attach a file to the email.
     *
     * @param string $path The path to the file to attach.
     * @param string $name The name of the attachment (optional).
     * @return static
     */
    public function attachment(string $path, string $name = ''): static
    {
        $this->mailer->addAttachment($path, $name);
        return $this;
    }

    /**
     * Sends the email.
     *
     * @return bool True if the email was sent successfully, false otherwise.
     * @throws \Exception If the email could not be sent.
     */
    public function send(): bool
    {
        try {
            return $this->mailer->send();
        } catch (Exception $e) {
            throw new \Exception("Mail could not be sent. Error: {$this->mailer->ErrorInfo}");
        }
    }
}