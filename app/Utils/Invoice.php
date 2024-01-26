<?php


namespace App\Utils;

use App\Mail\InvoiceMail;
use App\Mail\RegistrationRequestMail;
use App\Mail\SuporteSolicitacaoClienteMail;
use App\Mail\TermosDeUsoMail;
use App\Models\Adm;
use App\Models\RegistrationRequest;
use App\Models\UserAccount;
use App\Models\User;
use App\Utils\DiscountSheet;
use App\Utils\FileHandler;
use App\Utils\GenericsMedsSheet;
use App\Utils\HtmlWriter;
use App\Utils\MailGunFactory;
use App\Utils\Sheet;
use App\Utils\JwtValidation;
use App\Utils\Message;
use App\Utils\SOAP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Invoice
{

    public static function generateMany($limit, $save = false, $send = false)
    {
        $result = DB::select("
                SELECT PO.ID AS INVOICE_ID,
                         UA.NICKNAME,
                         PO.DT_PAYMENT,
                         US.EMAIL,
                         COALESCE(US.NAME,US.SOCIAL_REASON) AS NAME,
                         US.ZIP_CODE,
                         US.DOCUMENT,
                         US.ADDRESS,
                         US.NUMBER,
                         US.COMPLEMENT,
                         US.NEIGHBORHOOD,
                         US.STATE,
                         US.CITY,
                         CO.NAME AS COUNTRY,
                         PR.NAME AS PRODUCT_NAME,
                         PP.PRICE AS PRODUCT_PRICE,
                         OI.DISCOUNT,
                         OI.GLOSS_PRICE,
                         OI.FEE,
                         OI.BILLET_FEE,
                         PO.AMOUNT_RECEIVED,
                         VOU.voucher AS VOUCHER,
                         (SELECT GROUP_CONCAT(COU.title)
                           FROM vg_school.courses COU
                          WHERE COU.category_id <= IF(PR.ID = 12, 1.5, PR.ID)

                         ) AS COURSES
                      FROM PAYMENT_ORDER PO
                      JOIN ORDER_ITEM OI
                        ON PO.ORDER_ITEM_ID = OI.ID
                      JOIN PRODUCT PR
                        ON OI.PRODUCT_ID = PR.ID
                      JOIN PRODUCT_PRICE PP
                        ON PP.PRODUCT_ID = OI.PRODUCT_PRICE_ID
                      JOIN USER_ACCOUNT UA
                        ON UA.ID = PO.USER_ACCOUNT_ID
                      JOIN USER US
                        ON UA.USER_ID = US.ID
                     LEFT JOIN vg_school.users URS
                        ON US.EMAIL = URS.email
                     LEFT JOIN vg_school.voucher VOU
                        ON URS.id = VOU.owner_id
                      LEFT JOIN COUNTRY CO
                        ON US.COUNTRY_ID = CO.ID
                     WHERE NOT PO.SEND_INVOICE
                  	LIMIT {$limit}
        ");
        //dd($result);
        if (!empty($result)){
            foreach ($result as $key => $value){
                $courses = $value->COURSES;
                $courses = explode(',', $courses);
                $price = $value->PRODUCT_PRICE;
                $combo = $value->PRODUCT_NAME;
                $voucher = $value->VOUCHER;
                $document = $value->DOCUMENT;
                $name = $value->NAME;
                $id = $value->INVOICE_ID;
                $payment = $value->DT_PAYMENT;
                $discount = $value->DISCOUNT;
                $amount = $value->AMOUNT_RECEIVED;
                $fee = $value->FEE;
                $billet_fee = $value->BILLET_FEE / 5;
                $discount = $discount - $fee;
                $address = [
                    'street' => $value->ADDRESS,
                    'number' => $value->NUMBER,
                    'zip_code' => $value->ZIP_CODE,
                    'city' => $value->CITY,
                    'state' => $value->STATE,
                    'country' => $value->COUNTRY

                ];
                $pdf = PDF::loadView('invoices.default', compact('voucher', 'amount', 'billet_fee', 'discount','id', 'payment','courses', 'price', 'combo', 'address', 'document', 'name'));
                if ($save){
                    Storage::put('invoice/'.$id.'.pdf', $pdf->output());
                }
                if ($send){
                    $date = date('d/m/Y', strtotime($payment));
                    $html = (new HtmlWriter($value->NAME))->invoice($id, $date, $price);
                    $mg = new MailGunFactory();
                    $email = explode('@', $value->EMAIL);
                    $mail = $mg->send($value->EMAIL, 'INVOICE - nº'.$id, $html, ['filePath' => 'storage/invoice/'.$id.'.pdf', 'filename' => $id.'.pdf']);
                    /*if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                        $mail = Mail::to($value->EMAIL)->send(new InvoiceMail($html, $id));
                        $mail = true;
                    }else{
                        $mail = $mg->send($value->EMAIL, 'INVOICE - nº'.$id, $html, ['filePath' => 'storage/invoice/'.$id.'.pdf', 'filename' => $id.'.pdf']);
                    }*/
                    Invoice::update($id);
                    Storage::delete('invoice/'.$id.'.pdf');
                }
            }
        }
    }

    public static function generateOne($orderID, $save = false, $send = false)
    {
        $result = DB::select("
                SELECT PO.ID AS INVOICE_ID,
                         UA.NICKNAME,
                         PO.DT_PAYMENT,
                         US.EMAIL,
                         COALESCE(US.NAME,US.SOCIAL_REASON) AS NAME,
                         US.ZIP_CODE,
                         US.DOCUMENT,
                         US.ADDRESS,
                         US.NUMBER,
                         US.COMPLEMENT,
                         US.NEIGHBORHOOD,
                         US.STATE,
                         US.CITY,
                         CO.NAME AS COUNTRY,
                         PR.NAME AS PRODUCT_NAME,
                         PP.PRICE AS PRODUCT_PRICE,
                         OI.DISCOUNT,
                         OI.GLOSS_PRICE,
                         OI.FEE,
                         OI.BILLET_FEE,
                         PO.AMOUNT_RECEIVED,
                         VOU.voucher AS VOUCHER,
                         (SELECT GROUP_CONCAT(COU.title)
                           FROM vg_school.courses COU
                          WHERE COU.category_id <= IF(PR.ID = 12, 1.5, PR.ID)

                         ) AS COURSES
                      FROM PAYMENT_ORDER PO
                      JOIN ORDER_ITEM OI
                        ON PO.ORDER_ITEM_ID = OI.ID
                      JOIN PRODUCT PR
                        ON OI.PRODUCT_ID = PR.ID
                      JOIN PRODUCT_PRICE PP
                        ON PP.PRODUCT_ID = OI.PRODUCT_PRICE_ID
                      JOIN USER_ACCOUNT UA
                        ON UA.ID = PO.USER_ACCOUNT_ID
                      JOIN USER US
                        ON UA.USER_ID = US.ID
                     LEFT JOIN vg_school.users URS
                        ON US.EMAIL = URS.email
                      LEFT JOIN vg_school.voucher VOU
                       ON URS.id = VOU.owner_id
                      LEFT JOIN COUNTRY CO
                        ON US.COUNTRY_ID = CO.ID
                     WHERE NOT PO.SEND_INVOICE
                     AND PO.ORDER_ITEM_ID = {$orderID}
        ");
        //dd($result);
        if (!empty($result)){
            $courses = $result[0]->COURSES;
            $courses = explode(',', $courses);
            $price = $result[0]->PRODUCT_PRICE;
            $voucher = $result[0]->VOUCHER;
            $combo = $result[0]->PRODUCT_NAME;
            $document = $result[0]->DOCUMENT;
            $name = $result[0]->NAME;
            $id = $result[0]->INVOICE_ID;
            $payment = $result[0]->DT_PAYMENT;
            $discount = $result[0]->DISCOUNT;
            $amount = $result[0]->AMOUNT_RECEIVED;
            $fee = $result[0]->FEE;
            $billet_fee = $result[0]->BILLET_FEE / 5;
            $discount = $discount - $fee;
            $address = [
                'street' => $result[0]->ADDRESS,
                'number' => $result[0]->NUMBER,
                'zip_code' => $result[0]->ZIP_CODE,
                'city' => $result[0]->CITY,
                'state' => $result[0]->STATE,
                'country' => $result[0]->COUNTRY

            ];
            $pdf = PDF::loadView('invoices.default', compact('billet_fee','voucher', 'discount', 'amount', 'fee', 'id', 'payment','courses', 'price', 'combo', 'address', 'document', 'name'));
            if ($save){
                Storage::put('invoice/'.$id.'.pdf', $pdf->output());
            }
            if ($send){
                $date = date('d/m/Y', strtotime($payment));
                $html = (new HtmlWriter($result[0]->NAME))->invoice($id, $date, $price);
                $mg = new MailGunFactory();
                $email = explode('@', $result[0]->EMAIL);
                $mail = $mg->send($result[0]->EMAIL, 'INVOICE - nº'.$id, $html, ['filePath' => 'storage/invoice/'.$id.'.pdf', 'filename' => $id.'.pdf']);
                /*if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                    $mail = Mail::to($result[0]->EMAIL)->send(new InvoiceMail($html, $id));
                    $mail = true;
                }else{
                    $mail = $mg->send($result[0]->EMAIL, 'INVOICE - nº'.$id, $html, ['filePath' => 'storage/invoice/'.$id.'.pdf', 'filename' => $id.'.pdf']);
                }*/
                Invoice::update($id);
                Storage::delete('invoice/'.$id.'.pdf');
            }
            return ['pdf' => $pdf, 'invoice_id' => $id];
        }
    }

    public static function update($invoiceId)
    {
        try {
            DB::select("
                UPDATE PAYMENT_ORDER
                    SET SEND_INVOICE = 1,
                        DT_SEND_INVOICE = NOW()
                WHERE ID = {$invoiceId}
            ");
            return true;
        }catch (\Exception $e){
            return false;
        }
    }
}
