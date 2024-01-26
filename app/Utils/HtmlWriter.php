<?php


namespace App\Utils;


class HtmlWriter
{

    private $head;
    private $bottom;
    private $name;
    private $url;
    private $backUrl;
    private $admUrl;

    public function __construct($name)
    {
        $this->url = env('FRONT_URL');
        $this->backUrl = env('BACK_URL');
        $this->admUrl = "https://javalindohomologa.whiteclub.tech/";
        $this->name = $name;
        $this->head = "
        <style type=text/css'>
        .dadosuser {
            border-left: 7px #494949 solid;
            padding-left: 5px;
            width: 90%;
            max-width: 526px;
            text-align: left

        }
        #outlook a {
        padding:0;
        }
        .es-button {
        mso-style-priority:100!important;
        text-decoration:none!important;
        }
        a[x-apple-data-detectors] {
        color:inherit!important;
        text-decoration:none!important;
        font-size:inherit!important;
        font-family:inherit!important;
        font-weight:inherit!important;
        line-height:inherit!important;
        }
        .es-desk-hidden {
        display:none;
        float:left;
        overflow:hidden;
        width:0;
        max-height:0;
        line-height:0;
        mso-hide:all;
        }

            </style>
            <table align='center'
        id='m_-8610807613878967890m_-6403683138306923417Tabela_01'
        width='100%' border=0' cellpadding=0' cellspacing='0'
        style='max-width:640px' bgcolor='#ebebeb'>
                <tbody>
                    <tr>
                        <td align='center'>
                            <table align='center'
                            id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='94%'
                            border='0' cellpadding='0' cellspacing='0' style='max-width:607px'
                            bgcolor='#fff'>
                                <tbody>
                                    <tr>
                                        <td>
                                            <p style='border-left:18px #3f4040 solid;padding-left:10px'>
                                                <font style='font-size:1.7em' color='#3f4040' face='arial, sans-serif'>
                                                    <b>{$this->name} .</b><br>
                                                </font>
                                            </p>
                                        </td>
                                    </tr>

        ";

        $this->bottom = "
            </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align='center'>
                            <font style='font-size:0.8em' color='#666666' face='arial, sans-serif'>
                                <br><b>Se você não fez esta solicitação, Por favor ignore esse email.</b><br><br>
                            </font>
                        </td>
                    </tr>
                </tbody>
            </table>
        ";
    }

    public function validateDataEmailExternal($token, $password = null)
    {
        if($password == null){
            $body = "
                <tr>
                    <td align='center'>
                        <table align='center'
                                id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                                border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                            <tbody>
                                <tr>
                                    <td align='left'> <br>
                                        <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                            Para confirmar seu cadastro em nossa loja, por gentileza clique no link abaixo.
                                            <br>
                                            <br>
                                            Essa solicitação é valida por 24 horas.
                                            <br>
                                            <br>
                                        </font>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align='center' style='padding-bottom: 25px'>
                        <p style='border:7px #3f4040 solid;border-radius:26px;background-color:#3f4040;width:90%;max-width:526px;text-align:center;'>
                            <font color='#1d1a44' face='arial, sans-serif' style='font-size:1.0em'>
                                <a href='{$this->backUrl}/registration-requests/validate/{$token}' style='font-size:2.0em;color:#fff;text-decoration:none'>Clique aqui para confirmar seu cadastro<br></a>
                            </font>
                        </p>
                    </td>
                </tr>
        ";
        }else{
            $body = "
                <tr>
                    <td align='center'>
                        <table align='center'
                                id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                                border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                            <tbody>
                                <tr>
                                    <td align='left'> <br>
                                        <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                            Para confirmar seu cadastro em nossa loja, por gentileza clique no link abaixo.
                                            <br>
                                            <br>
                                            <hr>
                                            Sua senha é: <strong>{$password}</strong>
                                            <hr>
                                            Essa solicitação é valida por 24 horas.
                                            <br>
                                            <br>
                                        </font>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align='center' style='padding-bottom: 25px'>
                         <p style='border:7px #3f4040 solid;border-radius:26px;background-color:#3f4040;width:90%;max-width:526px;text-align:center;'>
                            <font color='#1d1a44' face='arial, sans-serif' style='font-size:1.0em'>
                                <a href='{$this->backUrl}/registration-requests/validate/{$token}' style='font-size:2.0em;color:#fff;text-decoration:none'>Clique aqui para confirmar seu cadastro<br></a>
                            </font>
                        </p>
                    </td>
                </tr>
        ";
        }


        return $this->head.$body.$this->bottom;
    }

    public function validateDataEmail($token, $password = null)
    {
        if($password == null){
            $body = "
                <tr>
                    <td align='center'>
                        <table align='center'
                                id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                                border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                            <tbody>
                                <tr>
                                    <td align='left'> <br>
                                        <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                            Para confirmar seu cadastro em nosso sistema, por gentileza clique no link abaixo.
                                            <br>
                                            <br>
                                            Essa solicitação é valida por 24 horas.
                                            <br>
                                            <br>
                                        </font>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align='center' style='padding-bottom: 25px'>
                        <p style='border:7px #3f4040 solid;border-radius:26px;background-color:#3f4040;width:90%;max-width:526px;text-align:center;'>
                            <font color='#1d1a44' face='arial, sans-serif' style='font-size:1.0em'>
                                <a href='{$this->backUrl}/registration-requests/validate/{$token}' style='font-size:2.0em;color:#fff;text-decoration:none'>Clique aqui para confirmar seu cadastro<br></a>
                            </font>
                        </p>
                    </td>
                </tr>
        ";
        }else{
            $body = "
                <tr>
                    <td align='center'>
                        <table align='center'
                                id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                                border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                            <tbody>
                                <tr>
                                    <td align='left'> <br>
                                        <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                            Para confirmar seu cadastro em nosso sistema, por gentileza clique no link abaixo.
                                            <br>
                                            <br>
                                            <hr>
                                            Sua senha é: <strong>{$password}</strong>
                                            <hr>
                                            Essa solicitação é valida por 24 horas.
                                            <br>
                                            <br>
                                        </font>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align='center' style='padding-bottom: 25px'>
                         <p style='border:7px #3f4040 solid;border-radius:26px;background-color:#3f4040;width:90%;max-width:526px;text-align:center;'>
                            <font color='#1d1a44' face='arial, sans-serif' style='font-size:1.0em'>
                                <a href='{$this->backUrl}/registration-requests/validate/{$token}' style='font-size:2.0em;color:#fff;text-decoration:none'>Clique aqui para confirmar seu cadastro<br></a>
                            </font>
                        </p>
                    </td>
                </tr>
        ";
        }


        return $this->head.$body.$this->bottom;
    }

    public function invoice($id, $date, $valor)
    {
        $body = "
        <tr>
            <td align='center'>
                <table align='center'
                        id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                        border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                    <tbody>
                        <tr>
                            <td align='left'> <br>
                                <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                    Se adjunta una copia de su factura a este mensaje. Gracias por adquirir nuestros productos.
                                    <br>
                                    <br>
                                    ID Invoice: <strong>{$id}</strong>
                                    <br>
                                    Fecha de Compra: <strong>{$date}</strong>
                                    <br>
                                    Valor pago: <strong>$</strong><strong>{$valor}</strong>
                                    <br>
                                    <br>
                                    En caso de dudas, contáctenos por correo electrónico suporte@whiteclub.tech
                                    <br>
                                    <br>
                                    Graciosamente,
                                    <br>
                                    Equipo de WHITE CLUB
                                </font>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        ";

        $html = $this->head . $body . $this->bottom;

        return $html;
    }

    public function termosDeUso($id, $nickname)
    {
        $body = "
        <tr>
            <td align='center'>
                <table align='center'
                        id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                        border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                    <tbody>
                        <tr>
                            <td align='left'> <br>
                                <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                    Anexamos a esta mensagem uma cópia do termo, aceito na hora da compra.
                                    <br>
                                    Obrigado por adquirir nossos produtos.
                                    <br>
                                    <br>
                                    NICKNAME: <strong>{$nickname}</strong>
                                    <br>
                                    Combo adquirido: <strong>{$id}</strong>
                                    <br>
                                    <br>
                                    <br>
                                    Atenciosamente,
                                    <br>
                                    WHITE CLUB
                                </font>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        ";

        $html = $this->head.$body.$this->bottom;

        return $html;
    }

    public function recoveryPassword($token)
    {
        $body = "
        <tr>
            <td align='center'>
                <table align='center'
                        id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                        border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                    <tbody>
                        <tr>
                            <td align='left'> <br>
                                <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                    Você efetuou uma solicitação de alteração de senha.
                                    <br>
                                    <br>
                                    Essa solicitação é valida por 24 horas, nesse periodo você poderá alterar sua senha
                                    utilizando o link disponivel nesse email.
                                    <br>
                                    <br>
                                </font>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td align='center' style='padding-bottom:25px'>
                <p style='border:7px #3f4040 solid;border-radius:26px;background-color:#3f4040;width:90%;max-width:526px;text-align:center;'>
                    <font color='#1d1a44' face='arial, sans-serif' style='font-size:1.0em'><a href='{$this->backUrl}/recovery-password/{$token}' style='font-size:2.0em;color:#fff;text-decoration:none' target='_blank'>Clique
                        aqui para alterar sua senha<br></a>
                    </font>
                </p>
            </td>
        </tr>
        ";

        $html = $this->head.$body.$this->bottom;

        return $html;
    }

    public function recoveryFinancialPassword($token)
    {
        $body = "
        <tr>
            <td align='center'>
                <table align='center'
                        id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                        border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                    <tbody>
                        <tr>
                            <td align='left'> <br>
                                <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                    Você efetuou uma solicitação de alteração da sua senha financeira.
                                    <br>
                                    <br>
                                    Essa solicitação é valida por 24 horas, nesse periodo você poderá alterar sua senha
                                    utilizando o link disponivel nesse email.
                                    <br>
                                    <br>
                                </font>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td align='center' style='padding-bottom:25px'>
                <p style='border:7px #3f4040 solid;border-radius:26px;background-color:#3f4040;width:90%;max-width:526px;text-align:center;'>
                    <font color='#1d1a44' face='arial, sans-serif' style='font-size:1.0em'><a href='{$this->backUrl}/recovery-financial-password/{$token}' style='font-size:2.0em;color:#fff;text-decoration:none' target='_blank'>Clique
                        aqui para alterar sua senha financeira<br></a>
                    </font>
                </p>
            </td>
        </tr>
        ";

        $html = $this->head.$body.$this->bottom;

        return $html;
    }

    public function userAccountRequest($sponsor, $nickname, $token)
    {

        $body = "
        <tr>
            <td align='center'>
                <table align='center'
                        id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                        border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                    <tbody>
                        <tr>
                            <td align='left'> <br>
                                <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                    O usuario <strong>{$sponsor}</strong> está solicitando um novo Nickname baseado em sua conta para pertencer a sua rede!
                                    <br>
                                    <br>
                                    <hr>
                                    O novo nickname solicitado é: <strong>{$nickname}</strong>.
                                    <hr>
                                     Caso queria aceitar essa solicitação basta clicar no link abaixo, porém, caso não queira prosseguir, basta ignorar a solicitação e ela será cancelada em 24 horas.
                                    <br>
                                    <br>
                                </font>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td align='center' style='padding-bottom: 25px'>
                <p style='border:7px #3f4040 solid;border-radius:26px;background-color:#3f4040;width:90%;max-width:526px;text-align:center;'>
                    <font color='#1d1a44' face='arial, sans-serif' style='font-size:1.0em'>
                        <a href='{$this->backUrl}/user-account-request/accept/{$token}' __target='blank' style='font-size:2.0em;color:#fff;text-decoration:none'>ACCEPT REQUEST<br></a>
                    </font>
                </p>
            </td>
        </tr>
        ";

        return $this->head.$body.$this->bottom;
    }


    // novo pedido de saque

    public function withDrawalconcluido($satoshi, $data, $hash, $hashPesquisa, $nickname)
    {
        $redeteste = $hashPesquisa;

        if ($redeteste === 2) {
            $redePesquisa = 'https://polygonscan.com/tx/';
        } else {
            $redePesquisa = 'https://www.blockchain.com/pt/explorer/search?search=';
        }
        $body = "
        <table class='es-wrapper' width='100%' cellspacing='0' cellpadding='0' style='mso-table-lspace:0pt;
    mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;
    height:100%;background-repeat:no-repeat;background-position:center 100px;background-color:#1B1B1B'>
    <tr>
        <td valign='top' style='padding:0;Margin:0'>
            <table cellpadding='0' cellspacing='0' class='es-header' align='center' style='mso-table-lspace:0pt;
                mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;
                background-color:transparent;background-repeat:repeat;background-position:center top'>
                <tr>
                    <td align='center' style='padding:0;Margin:0'>
                        <table class='es-header-body' align='center' cellpadding='0' cellspacing='0' style='mso-table-lspace:0pt;
                            mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;
                            background-color:transparent;width:600px'>
                            <tr>
                                <td align='left' style='padding:0;Margin:0;padding-top:20px;padding-left:20px;padding-right:20px'>
                                    <table cellpadding='0' cellspacing='0' class='es-left' align='center' style='mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:center'>
                                        <tr>
                                            <td class='es-m-p0r es-m-p20b' valign='top' align='center' style='padding:0;Margin:0;width:138px'>
                                                <table cellpadding='0' cellspacing='0' width='100%' role='presentation'
                                                    style='mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;
                                                    border-spacing:0px'>
                                                    <tr>
                                                        <td align='center' style='padding:0;Margin:0;font-size:0px'><a target='_blank' href='#' style='-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#1376C8;font-size:14px'><img src='https://javali.whiteclub.tech/static/img/logo.png' alt='Logo' style='display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic' width='140' title='Logo'></a></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <table cellpadding='0' cellspacing='0' align='center'
                                        style='mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px'>
                                        <tr>
                                            <td align='center' style='padding:0;Margin:0;width:402px'>
                                                <table cellpadding='0' cellspacing='0' width='100%' role='presentation'
                                                    style='mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;
                                                    border-spacing:0px'>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <table class='es-content' cellspacing='0' cellpadding='0' align='center'
                style='mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;
                border-spacing:0px;table-layout:fixed !important;width:100%'>
                <tr>
                    <td align='center' style='padding:0;Margin:0'>
                        <table class='es-content-body' style='mso-table-lspace:0pt;mso-table-rspace:0pt;
                            border-collapse:collapse;border-spacing:0px;background-color:transparent;
                            width:600px' cellspacing='0' cellpadding='0' align='center' bgcolor='#080E0E'>
                            <tr>
                                <td align='left' style='padding:0;Margin:0;padding-top:10px'>
                                    <table width='100%' cellspacing='0' cellpadding='0' style='mso-table-lspace:0pt;
                                        mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px'>
                                        <tr>
                                            <td class='es-m-p0r es-m-p20b' valign='top' align='center' style='padding:0;Margin:0;width:600px'>
                                                <table width='100%' cellspacing='0' cellpadding='0' role='presentation'
                                                    style='mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;
                                                    border-spacing:0px'>
                                                    <tr>
                                                        <td align='center' style='padding:0;Margin:0;position:relative'>
                                                            <a target='_blank' href='#' style='-webkit-text-size-adjust:none;
                                                                -ms-text-size-adjust:none;mso-line-height-rule:exactly;
                                                                text-decoration:underline;color:#2CB543;font-size:14px'>
                                                                <img class='adapt-img' src='https://javali.whiteclub.tech/static/img/product_id.png' alt='Womens equality day' title='Womens equality day' width='300'
                                                                    style='display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic'>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align='left' style='Margin:0;padding-top:10px;padding-bottom:20px;padding-left:20px;padding-right:20px'>
                                    <table width='100%' cellspacing='0' cellpadding='0' style='mso-table-lspace:0pt;
                                        mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px'>
                                        <tr>
                                            <td class='es-m-p0r es-m-p20b' valign='top' align='center' style='padding:0;Margin:0;width:560px'>
                                                <table width='100%' cellspacing='0' cellpadding='0' role='presentation'
                                                    style='mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;
                                                    border-spacing:0px'>
                                                    <tr>
                                                        <td align='center' style='padding:20px;Margin:0;font-size:0'>
                                                            <table border='0' width='60%' height='100%' cellpadding='0' cellspacing='0' role='presentation'
                                                                style='mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px'>
                                                                <tr>
                                                                    <td style='padding:0;Margin:0;border-bottom:1px solid #efefef;
                                                                        background:none;height:1px;width:100%;margin:0px'>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center' style='padding:0;Margin:0;padding-top:5px;padding-bottom:5px'>
                                                            <h3 style='Margin:0;line-height:24px;mso-line-height-rule:exactly;
                                                                font-family:arial, helvetica neue, helvetica, sans-serif;font-size:20px;
                                                                font-style:normal;font-weight:bold;color:#ffffff'>
                                                                <span style='background-color:#333333;border-radius:10px'>
                                                                    &nbsp; {$data}&nbsp;&nbsp;
                                                                </span>
                                                            </h3>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center' style='padding:0;Margin:0;padding-top:5px;padding-bottom:5px'>
                                                            <h2 style='Margin:0;line-height:44px;mso-line-height-rule:exactly;
                                                                font-family:arial, helvetica neue, helvetica, sans-serif;font-size:27px;
                                                                font-style:normal;font-weight:bold;color:#dec699'>{$nickname}
                                                            </h2>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center' style='padding:0;Margin:0;padding-top:5px;padding-bottom:5px'>
                                                            <h3 style='Margin:0;line-height:24px;mso-line-height-rule:exactly;
                                                                font-family:arial, helvetica neue, helvetica, sans-serif;font-size:20px;
                                                                font-style:normal;font-weight:bold;color:#ffffff'>SEU SAQUE NO VALOR DE<br>
                                                            </h3>
                                                            <h2 style='Margin:0;line-height:44px;mso-line-height-rule:exactly;
                                                                font-family:arial, helvetica neue, helvetica, sans-serif;font-size:37px;
                                                                font-style:normal;font-weight:bold;color:#dec699'>{$satoshi} USDT<br>
                                                            </h2>
                                                            <h3 style='Margin:0;line-height:24px;mso-line-height-rule:exactly;
                                                                font-family:arial, helvetica neue, helvetica, sans-serif;font-size:20px;
                                                                font-style:normal;font-weight:bold;color:#ffffff'>FOI REALIZADO COM SUCESSO
                                                            </h3>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align='left' style='padding:0;Margin:0;padding-top:20px;padding-left:20px;padding-right:20px'><!--[if mso]><table style='width:560px' cellpadding='0' cellspacing='0'><tr><td style='width:194px' valign='top'><![endif]-->
                                    <table cellpadding='0' cellspacing='0' class='es-left' align='left' style='mso-table-lspace:0pt;
                                        mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left'>
                                        <tr>
                                            <td class='es-m-p0r es-m-p20b' align='center' style='padding:0;Margin:0;width:174px'>
                                                <table cellpadding='0' cellspacing='0' width='100%' style='mso-table-lspace:0pt;
                                                    mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;
                                                    border-left:1px solid #dec699;border-right:1px solid #dec699;
                                                    border-top:1px solid #dec699;border-bottom:1px solid #dec699' role='presentation'>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align='left' style='padding:0;Margin:0;padding-left:20px;padding-right:20px'>
                                    <table cellpadding='0' cellspacing='0' width='100%' style='mso-table-lspace:0pt;
                                        mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px'>
                                        <tr>
                                            <td align='center' valign='top' style='padding:0;Margin:0;width:560px'>
                                                <table cellpadding='0' cellspacing='0' width='100%' role='presentation'
                                                    style='mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;
                                                    border-spacing:0px'>
                                                    <tr>
                                                        <td align='center' style='padding:0;Margin:0;padding-top:20px;
                                                            padding-bottom:30px'>
                                                            <span class='es-button-border'
                                                                style='border-style:solid;border-color:#2CB543;background:#DEC699;
                                                                border-width:0px;display:inline-block;border-radius:0px;width:auto'>
                                                                <a href=$redePesquisa$hash class='es-button es-button-1619186027629' target='_blank'
                                                                    style='mso-style-priority:100 !important;text-decoration:none;
                                                                    -webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;
                                                                    color:#001212;font-size:18px;padding:15px 40px;display:inline-block;
                                                                    background:#DEC699;border-radius:0px;font-family:arial, helvetica neue, helvetica, sans-serif;font-weight:normal;
                                                                    font-style:normal;line-height:22px;width:auto;text-align:center;mso-padding-alt:0;
                                                                    mso-border-alt:10px solid #DEC699'>VER TRANSAÇÃO
                                                                </a>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
        ";
        return $this->head.$body.$this->bottom;
    }


    public function withDrawalRequest($token, $amount, $withdrawalMethod, $nickname)
    {
        if($withdrawalMethod == 2){
            $body = "
        <tr>
            <td align='center'>
                <table align='center'
                        id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                        border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                    <tbody>
                        <tr>
                            <td align='left'> <br>
                                <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                A new withdrawal request was made in our system. To finalize the process, click the button below within 24 hours. After that period, this request will be invalidated.
                                    <br>
                                    <br>
                                    The requested amount is: <strong>USDT {$amount}</strong>.
                                </font>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td align='center' style='padding-bottom: 25px'>
                 <p style='border:7px #3f4040 solid;border-radius:26px;background-color:#3f4040;width:90%;max-width:526px;text-align:center;'>
                    <font color='#1d1a44' face='arial, sans-serif' style='font-size:1.0em'>
                        <a href='{$this->backUrl}/new-withdrawl-request/{$withdrawalMethod}/{$token}' __target='blank' style='font-size:2.0em;color:#fff;text-decoration:none'>ACCEPT REQUEST<br></a>
                    </font>
                </p>
            </td>
        </tr>
        ";
        }else{
            $body = "
        <tr>
            <td align='center'>
                <table align='center'
                        id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                        border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                    <tbody>
                        <tr>
                            <td align='left'> <br>
                                <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                A new withdrawal request was made in our system. To finalize the process, click the button below within 24 hours. After that period, this request will be invalidated.
                                    <br>
                                    <br>
                                    The requested amount is: <strong>USDT {$amount}</strong>.
                                </font>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td align='center' style='padding-bottom: 25px'>
                 <p style='border:7px #3f4040 solid;border-radius:26px;background-color:#3f4040;width:90%;max-width:526px;text-align:center;'>
                    <font color='#1d1a44' face='arial, sans-serif' style='font-size:1.0em'>
                        <a href='{$this->backUrl}/new-withdrawl-request/{$withdrawalMethod}/{$token}' __target='blank' style='font-size:2.0em;color:#fff;text-decoration:none'>ACCEPT REQUEST<br></a>
                    </font>
                </p>
            </td>
        </tr>
        ";
        }

        return $this->head.$body.$this->bottom;
    }

    public function newWalletRequest($token, $wallet)
    {
        $body = "
        <tr>
            <td align='center'>
                <table align='center'
                        id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                        border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                    <tbody>
                        <tr>
                            <td align='left'> <br>
                                <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                Identificamos o cadastro de uma nova carteira em sua conta.
                                    <br>
                                    <br>
                                    Esta é a carteira: <strong>{$wallet}</strong>.
                                </font>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td align='center' style='padding-bottom: 25px'>
                 <p style='border:7px #3f4040 solid;border-radius:26px;background-color:#3f4040;width:90%;max-width:526px;text-align:center;'>
                    <font color='#1d1a44' face='arial, sans-serif' style='font-size:1.0em'>
                        <a href='{$this->backUrl}/user/user-wallet/{$token}' __target='blank' style='font-size:2.0em;color:#fff;text-decoration:none'>CONFIRMAR<br></a>
                    </font>
                </p>
            </td>
        </tr>
        ";

        return $this->head.$body.$this->bottom;
    }


    public function boletoSend($digitableLine, $boleto, $amount)
    {
        $body = "
        <tr>
            <td align='center'>
                <table align='center'
                        id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                        border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                    <tbody>
                        <tr>
                            <td align='left'> <br>
                                <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                     Uma nova compra foi realizada em nosso sistema.
                                    <br>
                                    <br>
                                    O valor é: <strong>R$ {$amount}</strong>.
                                    <br>
                                    <br>
                                    Codigo de Barras do Boleto: <strong>{$digitableLine}</strong>.
                                </font>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td align='center' style='padding-bottom: 25px'>
                 <p style='border:7px #3f4040 solid;border-radius:26px;background-color:#3f4040;width:90%;max-width:526px;text-align:center;'>
                    <font color='#1d1a44' face='arial, sans-serif' style='font-size:1.0em'>
                        <a href='{$boleto}' __target='blank' style='font-size:2.0em;color:#fff;text-decoration:none'>Clique aqui para visualizar o boleto<br></a>
                    </font>
                </p>
            </td>
        </tr>
        ";

        return $this->head.$body.$this->bottom;
    }

    public function boletoCancel($digitableLine, $order, $amount)
    {
        $body = "
        <tr>
            <td align='center'>
                <table align='center'
                        id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                        border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                    <tbody>
                        <tr>
                            <td align='left'> <br>
                                <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                     O seu boleto referente a ordem <strong>{$order}</strong>,
                                     no valor de <strong>R$ {$amount}</strong> com o codígo de barras:
                                     <strong>{$digitableLine}</strong> acaba de ser cancelado.
                                     Você pode solicitar um novo boleto acessando os seus pedidos dentro
                                     da nossa plataforma.
                                     <br>
                                     <br>
                                </font>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        ";

        return $this->head.$body.$this->bottom;
    }

    public function transferNickname($nickname, $destName, $date)
    {
        $body = "
        <tr>
            <td align='center'>
                <table align='center'
                        id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                        border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                    <tbody>
                        <tr>
                            <td align='left'> <br>
                                <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                     O nickname <strong>{$nickname}</strong>,
                                     está sendo transferido para <strong>{$destName}</strong> no dia <strong>{$date}</strong>.
                                     <br>
                                     Caso não esteja ciente dessa solicitação, por gentileza contate o nosso suporte.
                                     <br>
                                     <br>
                                </font>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        ";

        return $this->head.$body.$this->bottom;
    }

    public function supportEmail($nickname, $reason, $content, $date, $name, $email, $cellphone, $document)
    {
        $body = "
        <tr>
            <td align='center'>
                <table align='center'
                        id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                        border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                    <tbody>
                        <tr>
                            <td align='left'> <br>
                                <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                     O nickname <strong>{$nickname}</strong>
                                     efetuou uma solicitação de suporte no dia <strong> {$date} </strong>.
                                     <hr>
                                     <h3>Dados do cliente: </h3>
                                     <strong>Nome: </strong> {$name}.
                                     <br>
                                     <strong>Telefone: </strong> {$cellphone}.
                                     <br>
                                     <strong>Email: </strong> {$email}.
                                     <br>
                                     <strong>Documento: </strong> {$document}.
                                     <br>
                                     <hr>
                                     <strong>Motivo do suporte: </strong> {$reason}.
                                     <br>
                                     <br>
                                      <strong>Descrição: </strong> {$content}.
                                     <br>
                                     <br>
                                </font>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        ";
        return $this->head.$body;
    }

    public function clientSupportRequestEmail($reason, $content, $date, $nickname, $name, $email, $cellphone, $document)
    {
        $body = "
        <tr>
            <td align='center'>
                <table align='center'
                        id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                        border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                    <tbody>
                        <tr>
                            <td align='left'> <br>
                                <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                     Muito obrigado por entrar em contato com nosso suporte, nossa equipe já esta analisando sua solicitação e retornaremos o mais rápido possível.
                                     <hr>
                                     <h3>Meus Dados: </h3>
                                     <strong>Nome: </strong> {$name}.
                                     <br>
                                     <strong>Telefone: </strong> {$cellphone}.
                                     <br>
                                     <strong>Email: </strong> {$email}.
                                     <br>
                                     <strong>Documento: </strong> {$document}.
                                     <br>
                                     <strong>Data da solicitação: </strong> {$date}.
                                     <br>
                                     <strong>Nickname: </strong> {$nickname}.
                                     <br>
                                     <hr>
                                     <strong>Motivo do suporte: </strong> {$reason}.
                                     <br>
                                     <br>
                                     <strong>Descrição: </strong> {$content}.
                                     <br>
                                     <br>
                                </font>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        ";
        return $this->head.$body.$this->bottom;
    }

    public function newAdmPassword($password, $token)
    {
        $body = "
                <tr>
                    <td align='center'>
                        <table align='center'
                                id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                                border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                            <tbody>
                                <tr>
                                    <td align='left'> <br>
                                        <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                            Você solicitou a alteração de sua senha para o painel adminstrativo, veja sua nova senha abaixo:
                                            <br>
                                            <br>
                                            <hr>
                                            Sua nova senha é: <strong>{$password}</strong>
                                            <hr>
                                            Essa é uma senha gerada em nosso sistema, você pode fazer sua alteração dentro do painel adminstrativo.
                                            <br>
                                            <br>
                                            Essa solicitação é valida por 24 horas, caso a ação não se confime dentro desse periodo ela será anulada.
                                        </font>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align='center' style='padding-bottom: 25px'>
                         <p style='border:7px #3f4040 solid;border-radius:26px;background-color:#3f4040;width:90%;max-width:526px;text-align:center;'>
                            <font color='#1d1a44' face='arial, sans-serif' style='font-size:1.0em'>
                                <a href='{$this->backUrl}/verify-adm-token/{$token}' style='font-size:2.0em;color:#fff;text-decoration:none'>Clique aqui para confirmar a troca de senha<br></a>
                            </font>
                        </p>
                    </td>
                </tr>
        ";

        return $this->head.$body.$this->bottom;
    }

    public function newUserEmail($old_email, $new_email, $token)
    {
        $body = "
                <tr>
                    <td align='center'>
                        <table align='center'
                                id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                                border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                            <tbody>
                                <tr>
                                    <td align='left'> <br>
                                        <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                            Uma solicitação de troca de email foi feita em sua conta:
                                            <br>
                                            <br>
                                            <hr>
                                            Seu antigo email é: <strong>{$old_email}</strong> e agora será: <strong>{$new_email}</strong>.
                                            <hr>
                                            Para aceitar a solicitação, basta clicar no botão abaixo.
                                            <br>
                                            <br>
                                        </font>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align='center' style='padding-bottom: 25px'>
                         <p style='border:7px #3f4040 solid;border-radius:26px;background-color:#3f4040;width:90%;max-width:526px;text-align:center;'>
                            <font color='#1d1a44' face='arial, sans-serif' style='font-size:1.0em'>
                                <a href='{$this->backUrl}/verify-new-user-email/{$token}' style='font-size:2.0em;color:#fff;text-decoration:none'>Clique aqui para confirmar a troca de email<br></a>
                            </font>
                        </p>
                    </td>
                </tr>
        ";

        return $this->head.$body.$this->bottom;
    }

    public function productSended($tracking_code, $delivery_number, $order_number, $product_list)
    {
        $body = "
                <tr>
                    <td align='center'>
                        <table align='center'
                                id='m_-8610807613878967890m_-6403683138306923417Tabela_01' width='90%'
                                border='0' cellpadding='0' cellspacing='0' style='max-width:550px'>
                            <tbody>
                                <tr>
                                    <td align='left'> <br>
                                        <font style='font-size:1.0em' color='#666666' face='arial, sans-serif'>
                                            O Status de seu pedido foi atualizado!
                                            <br>
                                            <br>
                                            <hr>
                                            A remessa nº <strong>{$delivery_number}</strong> referente ao pedido nº <strong>{$order_number}</strong> foi enviado.
                                            <br>
                                            Código de rastreio: <strong>{$tracking_code}</strong>.
                                            <hr>
                                            <strong>Produtos: </strong>
                                            <ul>
                                                {$product_list}
                                            </ul>
                                            <hr>
                                            Use o código acima para acompanhar o envio seus produtos ou acesse a nossa plataforma.
                                            <br>
                                            <br>
                                        </font>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
        ";

        return $this->head.$body.$this->bottom;
    }
}
