<?php
/**
 * CONFIGURAÇÕES DO SISTEMA DE ALERTA DE FALTAS
 * 
 * Este arquivo é um array associativo que guarda todas as
 * configurações em um só lugar. Quando outro arquivo precisa
 * de alguma config, ele faz: $config = require 'config.php';
 */

return [
    // --------------------------------------------------
    // LIMITE DE FALTAS
    // --------------------------------------------------
    // Se o aluno tiver MAIS faltas que esse número,
    // o alerta será disparado.
    'limite_faltas' => 10,

    // --------------------------------------------------
    // PERÍODO LETIVO (evita notificar o mesmo caso 2x)
    // --------------------------------------------------
    'ano'      => date('Y'),   // Pega o ano atual automaticamente
    'bimestre' => 1,           // 1, 2, 3 ou 4

    // --------------------------------------------------
    // DESTINATÁRIOS DOS ALERTAS
    // --------------------------------------------------
    // Você pode colocar vários emails separados por vírgula
    'email_pedagogos' => 'nayara.fsouza@aluno.edu.es.gov.br',
    'email_direcao'   => 'nayara.fsouza@aluno.edu.es.gov.br',

    // --------------------------------------------------
    // CONFIGURAÇÃO SMTP (Envio de E-mail)
    // --------------------------------------------------
    // Se deixar 'host' vazio, o sistema usa a função
    // mail() nativa do PHP (funciona em alguns servidores).
    //
    // Para Gmail:
    //   host => 'smtp.gmail.com'
    //   port => 587
    //   encryption => 'tls'
    //   username => 'seuemail@gmail.com'
    //   password => 'senha_de_app_do_gmail'
    'smtp' => [
        'host'       => '',
        'username'   => '',
        'password'   => '',
        'port'       => 587,
        'encryption' => 'tls',
        'from_email' => 'sistema@escola.edu.br',
        'from_name'  => 'Sistema de Faltas Escolar'
    ]
];
?>