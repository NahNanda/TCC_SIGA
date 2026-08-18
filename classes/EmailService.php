<?php
/**
 * EmailService - Envia emails de alerta
 * 
 * Usa PHPMailer se estiver instalado, senao usa mail() nativo do PHP.
 */

class EmailService
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function enviarAlerta(array $alunos, string $destinatarios, int $bimestre, int $ano): bool
    {
        $templatePath = __DIR__ . '/../templates/email_alerta.html';

        if (file_exists($templatePath)) {
            $html = file_get_contents($templatePath);
            $html = $this->preencherTemplate($html, $alunos, $bimestre, $ano);
        } else {
            $html = $this->montarTextoSimples($alunos, $bimestre, $ano);
        }

        // Verifica se PHPMailer esta instalado
        $phpmailerAutoload = __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';

        if (file_exists($phpmailerAutoload)) {
            return $this->enviarComPHPMailer($html, $destinatarios, $bimestre, $ano);
        }

        return $this->enviarComMailNativo($html, $destinatarios, $bimestre, $ano);
    }

    private function enviarComPHPMailer(string $html, string $destinatarios, int $bimestre, int $ano): bool
    {
        require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
        require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
        require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $smtp = $this->config['smtp'];

            if (!empty($smtp['host'])) {
                $mail->isSMTP();
                $mail->Host       = $smtp['host'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $smtp['username'];
                $mail->Password   = $smtp['password'];
                $mail->SMTPSecure = $smtp['encryption'] === 'tls'
                    ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS
                    : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = $smtp['port'];
            } else {
                $mail->isMail();
            }

            $mail->setFrom($smtp['from_email'], $smtp['from_name']);

            $emails = array_map('trim', explode(',', $destinatarios));
            foreach ($emails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $mail->addAddress($email);
                }
            }

            $mail->isHTML(true);
            $mail->Subject = "Alerta de Faltas - {$bimestre}º Bimestre / {$ano}";
            $mail->Body    = $html;
            $mail->AltBody = strip_tags($html);

            $mail->send();
            return true;
        } catch (\Exception $e) {
            error_log("Erro PHPMailer: " . $e->getMessage());
            return false;
        }
    }

    private function enviarComMailNativo(string $html, string $destinatarios, int $bimestre, int $ano): bool
    {
        $smtp = $this->config['smtp'];
        $assunto = "Alerta de Faltas - {$bimestre}º Bimestre / {$ano}";

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: {$smtp['from_name']} <{$smtp['from_email']}>\r\n";

        $emails = array_map('trim', explode(',', $destinatarios));
        $sucesso = true;

        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if (!mail($email, $assunto, $html, $headers)) {
                    $sucesso = false;
                }
            }
        }

        return $sucesso;
    }

    private function preencherTemplate(string $html, array $alunos, int $bimestre, int $ano): string
    {
        $linhas = '';
        foreach ($alunos as $a) {
            $linhas .= "<tr>";
            $linhas .= "<td>" . htmlspecialchars($a['nome']) . "</td>";
            $linhas .= "<td>" . $a['total_geral'] . "</td>";
            $linhas .= "<td>" . $a['total_faltas'] . "</td>";
            $linhas .= "<td>" . $a['total_faltas_just'] . "</td>";
            $linhas .= "</tr>";
        }

        $html = str_replace('{{BIMESTRE}}', $bimestre, $html);
        $html = str_replace('{{ANO}}', $ano, $html);
        $html = str_replace('{{LINHAS_ALUNOS}}', $linhas, $html);
        $html = str_replace('{{TOTAL_ALUNOS}}', count($alunos), $html);

        return $html;
    }

    private function montarTextoSimples(array $alunos, int $bimestre, int $ano): string
    {
        $html = "<h2>Alerta de Faltas - {$bimestre}º Bimestre / {$ano}</h2>";
        $html .= "<table border='1' cellpadding='6'>";
        $html .= "<tr><th>Aluno</th><th>Total</th><th>Faltas</th><th>FJ</th></tr>";

        foreach ($alunos as $a) {
            $html .= "<tr>";
            $html .= "<td>" . htmlspecialchars($a['nome']) . "</td>";
            $html .= "<td>" . $a['total_geral'] . "</td>";
            $html .= "<td>" . $a['total_faltas'] . "</td>";
            $html .= "<td>" . $a['total_faltas_just'] . "</td>";
            $html .= "</tr>";
        }

        $html .= "</table>";
        return $html;
    }
}