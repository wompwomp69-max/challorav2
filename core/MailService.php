<?php
/**
 * Layanan pengiriman email (otomatis)
 * Menggunakan PHPMailer via Composer
 */
class MailService {
    private array $config;
    private $mailer = null;

    public function __construct() {
        $configPath = BASE_PATH . '/config/email.php';
        $this->config = file_exists($configPath) ? require $configPath : [];
    }

    public function isEnabled(): bool {
        return !empty($this->config['enabled']);
    }

    /**
     * Kirim email hasil lamaran (diterima/ditolak)
     */
    public function sendApplicationResult(string $toEmail, string $toName, string $jobTitle, string $status): bool {
        if (!$this->isEnabled()) {
            return false;
        }
        $subject = 'Hasil Lamaran: ' . $jobTitle . ' - ' . ($status === 'accepted' ? 'Diterima' : 'Tidak Diterima');
        if ($status === 'accepted') {
            $body = "Yth. {$toName},\n\nSelamat! Anda dinyatakan LULUS seleksi untuk posisi {$jobTitle}.\n\nSilakan hubungi kami untuk langkah selanjutnya.\n\nTerima kasih.";
        } else {
            $body = "Yth. {$toName},\n\nTerima kasih telah melamar untuk posisi {$jobTitle}.\n\nMohon maaf, setelah proses seleksi Anda belum dapat kami terima untuk posisi ini.\n\nTetap semangat dan terima kasih.";
        }
        return $this->send($toEmail, $toName, $subject, $body);
    }

    private function send(string $toEmail, string $toName, string $subject, string $bodyText): bool {
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            return false;
        }
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = $this->config['smtp_host'] ?? '';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['smtp_user'] ?? '';
            $mail->Password = $this->config['smtp_pass'] ?? '';
            $mail->SMTPSecure = $this->config['smtp_secure'] ?? 'tls';
            $mail->Port = (int) ($this->config['smtp_port'] ?? 587);
            $mail->setFrom($this->config['from_email'] ?? 'noreply@localhost', $this->config['from_name'] ?? 'HR');
            $mail->addAddress($toEmail, $toName);
            $mail->Subject = $subject;
            $mail->Body = nl2br($bodyText);
            $mail->AltBody = $bodyText;
            $mail->send();
            return true;
        } catch (\Exception $e) {
            error_log('MailService: ' . $e->getMessage());
            return false;
        }
    }
}
