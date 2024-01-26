<?php

namespace App\Models;

use App\Utils\Message;
use Illuminate\Database\Eloquent\Model;

class SendWhatsapp extends Model
{
    protected $table = 'SEND_WHATSAPP';
    protected $fillable = [
                            'ID', 'WHATSAPP', 'NAME', 'MSG', 'GROUP_WHATSAPP', 'SEND',
                            'SENDING', 'DT_SEND', 'DT_REGISTER', 'USER_ACCOUNT_ID', 'USER_ID',
                            'CODE_LIST_ID'
                            ];
    public $timestamps = false;

    public const CONFIRM_PRODUCT = 1;
    public const TRACKING_CODE = 2;
    public const CONFIRM_DELIVERY = 3;
    public const CONFIRM_COMBO = 4;
    public const NEW_USER = 5;

    public static function validatePhone($phone)
    {
        return str_replace([' ', '+', '-'], ['', '', ''], $phone);
    }

    public static function sendMessage($data, $message_type)
    {
        if (!array_key_exists('WHATSAPP', $data) || !array_key_exists('NAME', $data)) return 'das';

        $data['WHATSAPP'] = self::validatePhone($data['WHATSAPP']);

        switch ($message_type){
            case self::CONFIRM_PRODUCT:
                return self::confirmProduct($data);
            case self::TRACKING_CODE:
                return self::trackingCodeMessage($data);
            case self::CONFIRM_DELIVERY:
                return self::confirmDelivery($data);
            case self::CONFIRM_COMBO:
                return self::confirmCombo($data);
            case self::NEW_USER:
                return self::newUser($data);
            default:
                return false;
        }
    }

    private static function trackingCodeMessage($data)
    {
        $data['MSG'] = "
{$data['NAME']},

Estamos passando pra avisar que os seus produtos já estão com a transportadora responsável pela entrega. 🚚"

/*
Você pode saber onde o seu pedido está sempre que quiser. É só tocar aqui 👇:
https://market.vg.company/trackingcode/{$data['TRACKING_CODE']}
*/

"Agradecemos por comprar com a gente!
Seguiremos acompanhando o seu pedido mas caso precise de alguma ajuda, entre no link abaixo e nos envie sua solicitação de atendimento.
suporte@whiteclub.tech

Att,
Equipe White Club
        ";

        return self::send($data);
    }

    private static function confirmProduct($data)
    {
        $data['MSG'] = "
Olá {$data['NAME']}!

Recebemos o seu pedido e estamos preparando os seus produtos para envio. Agradecemos pela preferência em comprar com a gente! 🤝

Você pode acompanhar o status do seu pedido através do link abaixo:
https://market.vg.company/myorders

Caso você não saiba, todas as compras realizadas na VG Market gera bonificações para clientes afiliados à WHITE CLUB. Essas bonificações se referem ao nosso Programa de Afiliados que, ao participar, você tem direito a ganhar pontos sob compras realizadas além de participar de outras 5 formas de remuneração, tudo a partir do compartilhamento dos nossos produtos. 🤩
Se isso te interessa, saiba mais em https://vg.company/associado/

Em breve, traremos mais informação sobre a sua compra.

Att,
Equipe VG Market
        ";

        return self::send($data);
    }

    private static function confirmDelivery($data)
    {
        $data['MSG'] = "
Oi, {$data['NAME']}.

Vimos que os seus produtos já chegaram!
Esperamos que sua experiência de compra com a VG Market tenha sido incrível!

E que tal compartilhar essa experiência conosco? 🥰
É só postar nos seus stories do Instagram e marcar o @vgcompany pra eu conseguir ver. Ou, você pode vir nos contar, comentando em qualquer publicação das nossas redes sociais. Iremos amar a sua participação!

É só acessar esses links:
🏠 https://www.facebook.com/vgcompany

🏠 https://instagram.com/vg.company

Ah! E se precisar de ajuda sobre os seus produtos, você pode nos contatar em suporte@whiteclub.tech

Até a próxima!
Att,
Equipe VG Market
       ";

        return self::send($data);
    }

    private static function confirmCombo($data)
    {
        $data['MSG'] = "
Olá, {$data['NAME']}.

Agradecemos por aquirir o combo de produtos {$data['COMBO']}. Agora, você terá um mundo de benefícios a sua disposição! 🤩

Todos os afiliados WHITE CLUB ativos ganham participações nos resultados da empresa, obtidos através da venda de seus produtos e serviços. Além disso, você ganha:

✅ Bônus sob indicações de novos afiliados;
✅ Bônus sob renovação e upgrade dos seus indicados;
✅ Bônus binário sob o resultado da sua rede de clientes;
✅ Bônus sob compras realizadas (pagas em até 4 níveis - do comprador aos seus 3 uplines);
✅E ainda ganha prêmios exclusivos ao atingir as metas descritas em nossa Apresentação de Negócio! 🎁🎉

Lembre-se: no seu escritório virtual você pode executar todas as ações que desejar, de cadastramento de novos clientes afiliados à compra de combos de produtos, visualização e saque das suas bonificações e muito mais.

Não deixe de conferir nossas redes sociais periodicamente para se manter sempre atualizado quanto ao nosso negócio. 🤜🤛

🏠 https://instagram.com/vg.company
🏠 https://www.youtube.com/channel/UCINcpUEuTTeZl80Zwz61Qig

Att,
Equipe WHITE CLUB";

        return self::send($data);
    }

    private static function newUser($data)
    {
        $data['MSG'] = "
Olá, {$data['NAME']}.

Seja muito bem-vindo(a) à nossa família de afiliados WHITE CLUB! 🥳

Queremos agradecer pelo seu cadastro e dizer que aqui você encontra uma grande oportunidade de transformação de vida! 🏆

Oferecemos a você cursos de desenvolvimento pessoal, diversos produtos de consumo diário e relacionados a indústria do bem estar e da saúde e ainda oferecemos serviços que prestamos com a maior dedicação e qualidade possível.

A próxima etapa é escolher o seu combo de produtos desejado e iniciar essa jornada de transformação, que se inicia com a sua experiência com os nossos produtos e ainda abre portas para o sucesso profissional que todos nós almejamos tanto - tudo através do simples compartilhamento dos nossos produtos e do nosso projeto.

É um prazer ter você com a gente! 🫂
Att,
Equipe WHITE CLUB";

        return self::send($data);
    }

    private static function send($data)
    {
        return self::create($data);
    }
}
