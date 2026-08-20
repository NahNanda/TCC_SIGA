<?php
/**
 * CONFIGURAÇÕES DO SISTEMA DE ALERTA DE FALTAS
 */

return [
    // --------------------------------------------------
    // LIMITE DE FALTAS
    // --------------------------------------------------
    'limite_faltas' => 3,

    // --------------------------------------------------
    // PERÍODO LETIVO (evita notificar o mesmo caso 2x)
    // --------------------------------------------------
    'ano'      => date('Y'),
    'bimestre' => 1,

    // --------------------------------------------------
    // DESTINATÁRIOS DOS ALERTAS
    // --------------------------------------------------
    'email_pedagogos' => 'welber05@gmail.com',
    'email_direcao'   => 'nayara.fsouza@aluno.edu.es.gov.br',

    // --------------------------------------------------
    // CONFIGURAÇÃO DE ENVIO (API do Brevo, via HTTPS)
    // --------------------------------------------------
    // Veja o passo a passo de como gerar a api_key no
    // topo do arquivo classes/EmailService.php
    'brevo' => [
        'api_key'    => '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',
        'from_email' => 'nayara.fsouza@aluno.edu.es.gov.br', // precisa estar verificado no Brevo
        'from_name'  => 'SIGA- Sistema Inteligente de Gestão de Ausências',
    ],
];