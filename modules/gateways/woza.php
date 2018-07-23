<?php

# Woza

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related capabilities and
 * settings.
 *
 * @see https://developers.whmcs.com/payment-gateways/meta-data-params/
 *
 * @return array
 */
function woza_MetaData()
{
    return array(
        'DisplayName' => 'Woza',
        'APIVersion' => '1.1', // Use API Version 1.1
        'DisableLocalCreditCardInput' => true,
        'TokenisedStorage' => false,
    );
}

function woza_config() {

    $configarray = array(
     "FriendlyName" => array(
        "Type" => "System",
        "Value" => "Woza"
        ),
     'shortcodetype' => array(
            'FriendlyName' => 'Short Code Type',
            'Type' => 'dropdown',
            'Options' => array(
                'paybill' => 'Pay Bill',
                'tillnumber' => 'Till Number',
            ),
            'Description' => 'Select Your Short Code Type',
        ),
     'shortcode' => array(
            'FriendlyName' => 'Short Code',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your short code here',
        ),
     'clientkey' => array(
            'FriendlyName' => 'Client Key',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your key here',
     ),
     'clientsecret' => array(
            'FriendlyName' => 'Client Secret',
            'Type' => 'text',
            'Size' => '30',
            'Default' => '',
            'Description' => 'Enter your secret here',
     )
    );

    return $configarray;

}

function woza_link($params) {
    $message = "";
    $paymentmade = false;

    if (isset($_POST)){
        if ( $_POST['invoice_id'] != '' && $_POST['send_url'] != "") {
            if ($_POST['send_url'] == 'paybill') {
                $send_url = 'https://my.jisort.com/paymentsApi/validate/?business_no='.$_POST['shortcode'].'&trans_id='.$_POST['trans_id'];
            } else {
                $send_url = 'https://my.jisort.com:9382/general_ledger/transactions_ledger/?business_no='.$_POST['shortcode'].'&trans_id='.$_POST['trans_id'];
            }

            $url = $send_url;
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch); 
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
            curl_close($ch);  

            if ($httpcode == '500') {
                $message = "<strong style='color:red;'>An error has occurred. Kindly try again later.</strong>";
            } elseif ($httpCode != '200') {
                $message = "<strong style='color:red;'>".str_replace('["', '', str_replace('"]', '', $result))."</strong>";
            } else {   

                $json = json_decode($result, true);     

                addInvoicePayment(
                    $_POST['invoice_id'],
                    $json['transaction_reference'],
                    $json['debit'],
                    '0',
                    $_POST['moduleName']
                );
                
                header("Refresh:0");
            }
            $res = $message;
       }
    }

    global $_LANG;

    // Gateway Configuration Parameters
    $accountId = $params['accountID'];
    $secretKey = $params['secretKey'];
    $testMode = $params['testMode'];
    $dropdownField = $params['dropdownField'];
    $radioField = $params['radioField'];
    $textareaField = $params['textareaField'];

    // Invoice Parameters
    $invoiceId = $params['invoiceid'];
    $description = $params["description"];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];

    // Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $returnUrl = $params['returnurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    $url = "viewinvoice.php?id=".$params['invoiceid'];
    $send_url = '';

    if (strpos($_SERVER['PHP_SELF'], 'viewinvoice') !== false) {
        $send_url = $params['shortcodetype'];
        $url = $_SERVER['PHP_SELF'].'?id='.$params['invoiceid'];
        if ($params['shortcodetype'] == 'paybill') {
            $instructions = $message. "
            <br><img src='http://www.truehost.co.ke/cloud/templates/ryanada/index.png' alt='mpesa' style='width:200px;'><br>
            <strong>Payment Instructions (".$params['shortcode'].")</strong>
                1. Go to M-Pesa menu 
                2. Click on Lipa na M-Pesa 
                3. Click on Paybill
                4. Enter business no <strong>".$params['invoiceid']."</strong>
                4. Enter paybill no <strong>".$params['shortcode']."</strong>
                5. Enter amount <strong>".$amount.' '.$currencyCode."</strong>
                6. Wait for the M-Pesa message
                7. Click Pay Now.";

        } else {
            $instructions =  $message."
            <strong>Payment Instructions (".$params['shortcode'].")</strong>
                1. Go to M-Pesa menu 
                2. Click on Lipa na M-Pesa 
                3. Click on Buy Goods and Services 
                4. Enter till no <strong>".$params['shortcode']."</strong>
                5. Enter amount <strong>".$amount.' '.$currencyCode."</strong>
                6. Wait for the M-Pesa message
                7. Click Pay Now.";
        }
    }

    $postfields = array();
    $postfields['username'] = $username;
    $postfields['invoice_id'] = $invoiceId;
    $postfields['shortcode'] = $params['shortcode'];
    $postfields['description'] = $description;
    $postfields['amount'] = $amount;
    $postfields['currency'] = $currencyCode;
    $postfields['first_name'] = $firstname;
    $postfields['last_name'] = $lastname;
    $postfields['email'] = $email;
    $postfields['address1'] = $address1;
    $postfields['address2'] = $address2;
    $postfields['city'] = $city;
    $postfields['state'] = $state;
    $postfields['postcode'] = $postcode;
    $postfields['country'] = $country;
    $postfields['phone'] = $phone;
    $postfields['send_url'] = $send_url;
    $postfields['moduleName'] = $moduleName;
    $postfields['trans_id'] = $params['trans_id'];
    $postfields['clientsecret'] = $params['clientsecret'];
    $postfields['clientkey'] = $params['clientkey'];

    $code = '<p>'.nl2br($instructions).'<br />'.$_LANG['invoicerefnum'].': '.$params['invoiceid'].
        '<br /></p>';

    $code .= '<form method="post" action="' . $url . '">';
    foreach ($postfields as $k => $v) {
        $code .= '<input type="hidden" name="' . $k . '" value="' . urlencode($v) . '" />';
    }
    $code .= '<input type="text" name="trans_id" placeholder="Mpesa Transaction Id" style="border-radius:10px;-moz-border-radius: 10px;padding:5px;"/>';
    $code .= '<input style="color:green; border-radius:5px;-moz-border-radius: 5px;padding:5px; margin-left:10px; width: 200px;" type="submit" value="' . $langPayNow . '" />';
    $code .= '</form>';

    return $code;

}

?> 