<?php

/*
<DOC>
@file engine/functions.php
@desc helper functions
@version v0.1
</DOC>
*/

/*
<DOC>
@func getConfigItem
@desc Return a constant, decrypt if required
@param name : the constant's name
@param decrypt : boolean - True=decrypt False=Do not decrypt
@return string
</DOC>
*/
function getConfigItem($name,$decrypt=false)
{
    if(!defined($name))
    {
        throw new RuntimeException($name,10);
    }

    $value = ($decrypt===false) ? constant($name) : decrypt(constant($name));

    return $value;
}

/*
<DOC>
@func decrypt
@desc decrypts an encrypted string
@param value : the encrypted string
@return string
</DOC>
*/
function decrypt($value)
{
    //TO DO - Add decryption code
    //$exec = CONFIG_PATH."go_crypto dec $enc_value";

    //echo $exec;

    //exec($exec,$output);

    //print_r($output);

    //$value = $output[0];

    return $value;
}

/*
<DOC>
@func checkParametersAndDatabaseConnection
@desc check the post parameters coming in from QSIS and connection to QSIS database
@param post : the $_POST array
</DOC>
*/
function checkParametersAndDatabaseConnection($post)
{
    global $DATABASE;
    global $TEST_MODE;

    if(
        empty($post["instId"]) ||
        empty($post["cartId"]) ||
        empty($post["currency"]) ||
        empty($post["desc"]) ||
        empty($post["authMode"]) ||
        !isset($post["testMode"]) ||
        empty($post["email"]) ||
        empty($post["country"]) ||
        empty($post["name"]) ||
        empty($post["amount"]) ||
        empty($post["MC_URL"])
    )
    {
        throw new RuntimeException(serialize($post),100);
    }

    $DATABASE = strtolower($post["MC_URL"]);
    $TEST_MODE = $post["testMode"];

    //Check database connection
    $env = getQSISEnvironment($DATABASE);
    $conn = db_getConnection($env);

    $use_addresses = getConfigItem("WP_QUB_USE_ADDRESSES");

    if(
        $use_addresses===true && 
        (
            empty($post["address1"]) ||
            empty($post["postcode"]) ||
            empty($post["town"]) ||
            empty($post["region"])
        )
    )
    {
        throw new RuntimeException(serialize($post),101);
    }

    db_log($post["cartId"],"N/A","TS1 - Connection received from '$DATABASE'. POST parameters OK",$DATABASE);
}

/*
<DOC>
@func generateAddressXML
@desc Formats the address variables into XML format
@param post : the $_POST array
@return string (xml)
</DOC>
*/
function generateAddressXML($post)
{
    $use_addresses = getConfigItem("WP_QUB_USE_ADDRESSES");
    
    if($use_addresses===false){ return ""; }

    if
    (
        !isset($post['address1']) ||
        !isset($post['postcode']) ||
        !isset($post['town']) ||
        !isset($post['region']) ||
        !isset($post['country'])
    )
    {
        throw new RuntimeException(print_r($post,true),105);
    }

    $temp = explode(" ",$post["name"]);

    $FIRSTNAME = $temp[0];
    $LASTNAME = (isset($temp[1])) ? $temp[1] : "";

    $ADDRESS1 = $post["address1"];
    $POSTCODE = $post["postcode"];
    $TOWN = $post["town"];
    $REGION = $post["region"];
    $COUNTRY = $post["country"];

    $xml = <<<XML
         <shippingAddress>
            <address>
               <address1>$ADDRESS1</address1>
               <address2></address2>
               <address3></address3>
               <postalCode>$POSTCODE</postalCode>
               <city>$TOWN</city>
               <state>$REGION</state>
               <countryCode>$COUNTRY</countryCode>
            </address>
         </shippingAddress>
         <billingAddress>
            <address>
                <firstName>$FIRSTNAME</firstName>
                <lastName>$LASTNAME</lastName>
                <address1>$ADDRESS1</address1>
               <address2></address2>
               <address3></address3>
               <postalCode>$POSTCODE</postalCode>
               <city>$TOWN</city>
               <state>$REGION</state>
               <countryCode>$COUNTRY</countryCode>
            </address>
         </billingAddress>
XML;

    return $xml;
}

/*
<DOC>
@func generateXML
@desc Formats the post variables into XML format
@param post : the $_POST array
@return string (xml)
</DOC>
*/
function generateXML($post)
{
    global $DATABASE;

    if
    (
        !isset($post['cartId']) ||
        !isset($post['desc']) ||
        !isset($post['currency']) ||
        !isset($post['amount']) ||
        !isset($post['email']) ||
        !isset($post['name']) ||
        !isset($post['instId'])
    )
    {
        throw new RuntimeException(print_r($post,true),104);
    }

    if(isset($post["MC_VAR3"]) && !empty($post["MC_VAR3"]) && getConfigItem("ALLOW_EMAIL_OVERRIDE")===true)
    {
        $post["email"] = $post["MC_VAR3"];
    }

    $MERCHANT_CODE = getConfigItem("WP_MERCHANT_CODE",true);
    $CARTID = $post['cartId'];
    $INSTID = $post['instId']; //getConfigItem("WP_INSTALL_ID");
    $DESC = $post['desc'];
    $CURRENCY = $post['currency'];

    $FIXEDAMOUNT = number_format((float)$post["amount"], 2, '.', '');
    $FIXEDAMOUNT = (int) str_replace(".", "", $FIXEDAMOUNT);

    //$FIXEDAMOUNT = (int) str_replace(".", "", $post['amount']);
    //$FIXEDAMOUNT = $FIXEDAMOUNT * 100;
    $EMAIL = $post['email'];
    $CDATA = 'Cart ID: '.$post['cartId'].' Amount: '.$post['amount'].' Currency: '.$post['currency'].' Description: '.$post['desc'].' Name: '.$post['name'].' Email: '.$post['email'];
    $ADDRESSFRAGMENT = generateAddressXML($post);

    $NAME = $post["name"];

    $env = getQSISEnvironment($DATABASE);

    $CARTID = $env["config"]["prefix"].$CARTID;

    $xml = <<<XML
    <?xml version="1.0" encoding="UTF-8"?>
    <!DOCTYPE paymentService PUBLIC "-//Worldpay//DTD Worldpay PaymentService v1//EN" "http://dtd.worldpay.com/paymentService_v1.dtd">
    <paymentService version="1.4" merchantCode="$MERCHANT_CODE">
       <submit>
          <order orderCode="$CARTID" installationId="$INSTID">
             <description>$DESC</description>
             <amount currencyCode="$CURRENCY" exponent="2" value="$FIXEDAMOUNT" /> 
             <orderContent><![CDATA[$CDATA]]></orderContent>
             <paymentMethodMask>
                <include code="ALL" />
             </paymentMethodMask>
             <shopper>
                <shopperEmailAddress>$EMAIL</shopperEmailAddress>
             </shopper>
             $ADDRESSFRAGMENT
          </order>
       </submit>
    </paymentService>
    XML;

    return $xml;
}

/*
<DOC>
@func wp_requestPaymentURL
@desc Authenticate transaction with Worldpay server
@param cartID : The cart id
@param xml : xml formatted data to be sent to Worldpay
@return string (Payment URL)
</DOC>
*/
function wp_requestPaymentURL($cartID,$xml)
{
    global $DATABASE;
    global $TEST_MODE;
    global $CONTINUE_URL;
    global $CARTID;
    global $TRANSID;

    $wp_corporate_url = ($TEST_MODE=="0") ? getConfigItem("WP_URL_LIVE") : getConfigItem("WP_URL_TEST");

    $wp_xml_username = ($TEST_MODE=="0") ? getConfigItem("WP_XML_USERNAME_LIVE") : getConfigItem("WP_XML_USERNAME_TEST");
    $wp_xml_password = ($TEST_MODE=="0") ? getConfigItem("WP_XML_PASSWORD_LIVE") : getConfigItem("WP_XML_PASSWORD_TEST");

    $env = getQSISEnvironment($DATABASE);

    $conn = db_getConnection($env);
    $transaction = db_getTransaction($conn,$cartID);

    $CARTID = $cartID;
    $CONTINUE_URL = $transaction["QUB_CONTINUE_URL"];

    $curl = curl_init($wp_corporate_url);
    curl_setopt ($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
    curl_setopt($curl, CURLOPT_POST, true);
    //set basic auth credentials
    curl_setopt($curl, CURLOPT_USERPWD, $wp_xml_username . ":" . $wp_xml_password);
    //Attach the XML string to the body of our request.
    curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
    //return results as a string
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //Execute the POST request and send worldpay our XML
    $result = curl_exec($curl);
    $xmlResult = simplexml_load_string($result);

    if(curl_errno($curl))
    {    
        throw new RuntimeException(" *error=".curl_error($curl),102);
    }

    curl_close($curl);

    $errorCode = false;

    if(stristr($result,"401: Requires Authentication"))
    {
        throw new RuntimeException(print_r($result,true),106);
    }

    if(isset($xmlResult->reply->error))
    {
        $errorCode =  (string) $xmlResult->reply->error->attributes()->code[0];
        $errorMessage = (string) $xmlResult->reply->error[0];
    }

    if($errorCode)
    {
        throw new RuntimeException("error=".$errorCode." - ".$errorMessage,103);
    }

    if(!isset($xmlResult->reply->orderStatus))
    {
        throw new RuntimeException(print_r($xmlResult,true),107);
    }

    //build URL with result URLs appended
    $redirectUrl = (string) $xmlResult->reply->orderStatus->reference[0];
    $transID = (string) $xmlResult->reply->orderStatus->reference->attributes()->id[0];

    $TRANSID = $transID;

    db_writeTransaction($conn,$cartID,$transID);
    db_log($cartID,$transID,"TS2 - XML Generated. Worldpay authentication OK",$DATABASE);

    //Add success, cancel, failure and error url to payment url
    $redirectUrl .= "&successURL=".getConfigItem("RETURN_URL")."/".$DATABASE."/index.php";
    $redirectUrl .= "&cancelURL=".getConfigItem("RETURN_URL")."/".$DATABASE."/index.php";
    $redirectUrl .= "&failureURL=".getConfigItem("RETURN_URL")."/".$DATABASE."/index.php";
    $redirectUrl .= "&errorURL=".getConfigItem("RETURN_URL")."/".$DATABASE."/index.php";

    return $redirectUrl;
}

/*
<DOC>
@func redirectUser
@desc redirect user
@param url : The url to redirect to (string)
@param use_load : whether to use the load.php for redirection (boolean)
</DOC>
*/
function redirectUser($url,$use_load=true)
{
    $encoded = base64_encode($url);

    if($use_load===true)
    {
        header("Location: load.php?id=$encoded");
    }
    else
    {
        header("Location: $url");
    }

    exit;
}

function redirectUserOnError($url)
{
    $encoded = base64_encode($url);
    header("Location: load2.php?id=$encoded");
    exit;
}

/*
<DOC>
@func displayHTML
@desc Display html to the user
@param code : code of the html snippet to use (integer)
@param parameters : parameters to be passed to html snippet (array)
</DOC>
*/
function displayHTML($code,$parameters=null,$headers=true,$title="")
{
    global $HTML;

    $html = (!isset($HTML["$code"])) ? $HTML["001"] : $HTML["$code"];

    if(is_array($parameters) && !empty($parameters))
    {
        foreach($parameters as $key=>$value)
        {
            $html = str_replace("{{".strtoupper($key)."}}",$value,$html);
        }
    }

    $title = (empty($title)) ? "QUB WP Gateway Server" : $title;

    if($headers===true)
    {
        $wrapper = <<<HTML
    <!doctype html>
    <html lang="en">
        <head>
            <meta charset="utf-8">
            <link href='/styles/bootstrap/css/bootstrap.min.css' type='text/css' rel='stylesheet' >
            <link href='/styles/styles.css' type='text/css' rel='stylesheet' >
            <script src="https://kit.fontawesome.com/3327e774d1.js" crossorigin="anonymous"></script>
            <title>$title</title>
        </head>
        <body>
            <div class='qsisContainer'>
                <div class='qsisMessage'>
                    $html
                    <div class='qsisSub'>DH/$code</div>
                </div>
            </div>
        </body>
    </html>
HTML;
    }
    else
    {
        $wrapper = $html;
    }

    return $wrapper;
}

/*
<DOC>
@func getQSISEnvironment
@desc get config details for QSIS environment and database either through a database parameter or from the current working directory (if called from wp_resp process)
@param database : the QSIS database (string) - default=null
@return array (Environment config)
</DOC>
*/
function getQSISEnvironment($database=null)
{
    global $ENVIRONMENTS;

    if(is_null($database))
    {
        $dir = getcwd();
        $processed = str_replace(APP_PATH."wp_resp/","",$dir);
    }
    else
    {
        $processed = strtolower(trim($database));
    }

    if(!isset($ENVIRONMENTS[$processed]))
    {
        throw new RuntimeException($processed,203);
    }

    $env = $ENVIRONMENTS[$processed];

    //NEW 27/01
    $env["dbpswd"] = getPassword($processed);
    $temp["environment"] = $processed;
    $temp["config"] = $env;

    return $temp;
}

/*
<DOC>
@func appendContinueURLParameters
@desc append parameters to the continue url
@param url : the continue url (string)
@param result : success (or not) of the payment process (string)
@param transID : transaction id (srting)
@param cartID : cart id (string)
@param error_code : error code (string) default=null
@return string (url)
</DOC>
*/
function appendContinueURLParameters($url,$result,$transID,$cartID,$error_code=null)
{
    $error_param = (is_null($error_code)) ? "" : "&err=".$error_code;
    $url .= "&result=".$result."&transId=".$transID."&cartId=".$cartID.$error_param;
    return $url;
}

/*
<DOC>
@func resp_generateMAC
@desc generate the MAC code to compare with MAC sent from Worldpay
@param get : the $_GET (array)
@return string (mac)
</DOC>
*/
function resp_generateMAC($get)
{
    //orderKey:paymentAmount:paymentCurrency:paymentStatus
    $secret = getConfigItem("WP_REDIRECT_SECRET");
    if
    (
        isset($get["paymentStatus"]) && 
        isset($get["paymentAmount"]) &&
        isset($get["paymentCurrency"]) &&
        isset($get["paymentStatus"])
    )
    {
        $string = $get["orderKey"].":".$get["paymentAmount"].":".$get["paymentCurrency"].":".$get["paymentStatus"];
    }
    elseif
    (
        isset($get["orderKey"]) &&
        isset($get["orderAmount"]) &&
        isset($get["orderCurrency"])
    ) //paymentStatus isn't set when the user has cancelled the transaction
    {
        //orderKey:orderAmount:orderCurrency
        $string = $get["orderKey"].":".$get["orderAmount"].":".$get["orderCurrency"];
    }
    else
    {
        throw new RuntimeException(print_r($get,true),205);
    }

    $mac = hash_hmac('sha256', $string, $secret);
    return $mac;
}

/*
<DOC>
@func resp_processResponse
@desc process Worldpay's payment response
@param env : QSIS environment configuration details (array)
@return array (resp)
</DOC>
*/
function resp_processResponse($cartID,$env)
{
    global $WP_RESULTS;

    $environment = $env["environment"];
    $conn = db_getConnection($env);

    $transaction = db_getTransaction($conn,$cartID);
    
    $wp_errors = "";

    if(isset($_GET["errorRefNumber"]) || isset($_GET["errors"]))
    {
        $paymentStatus = "ERROR";
        $wp_errors = (isset($_GET["errorRefNumber"])) ? " WP errorRefNumber=".$_GET["errorRefNumber"]." WP errors=".$_GET["errors"] : "";
    }
    else
    {
        $mac = resp_generateMAC($_GET);

        //confirm the hash
        if($mac!==$_GET["mac2"])
        {
            throw new RuntimeException($_GET["mac2"],200);
        }

        db_log($cartID,$transaction["QUB_WP_TRANS_ID"],"TS2 - Payment response received from Worldpay",$environment);

        $paymentStatus = isset($_GET["paymentStatus"]) ? trim($_GET["paymentStatus"]) : "CANCELLED";
    }

    if(!isset($WP_RESULTS["$paymentStatus"]))
    {
        throw new RuntimeException("[$paymentStatus] -".serialize($WP_RESULTS),201);
    }

    db_updateTransactionStatus($conn,$transaction,$WP_RESULTS[$paymentStatus]["DATABASE_FORMAT"]);
    db_log($cartID,$transaction["QUB_WP_TRANS_ID"],"TS".$WP_RESULTS[$paymentStatus]["DATABASE_FORMAT"]." - Payment ".$WP_RESULTS[$paymentStatus]["LABEL"].$wp_errors,$environment); 

    $result = $WP_RESULTS[$paymentStatus]["RETURN_PAGE_FORMAT"];

    $resp["cartID"] = $cartID;
    $resp["transID"] = $transaction["QUB_WP_TRANS_ID"];
    $resp["result"] = $result;
    $resp["QUB_CONTINUE_URL"] = $transaction["QUB_CONTINUE_URL"];

    //if the url doesn't contain the environment then the transaction was
    //initiated in a different environment so is invalid
    if(!stristr($transaction["QUB_CONTINUE_URL"],$environment) && !stristr($transaction["QUB_CONTINUE_URL"],str_replace("dap","",$environment)))
    {
        throw new RuntimeException("[".$transaction["QUB_CONTINUE_URL"]."] [$environment]"."[".str_replace("dap","",$environment)."]",202);
    }

    return $resp;
}

/*
<DOC>
@func db_getConnection
@desc connect to the Oracle database
@param env : QSIS environment configuration details (array)
@return oci connection object
</DOC>
*/

function getPassword($name)
{
    $PASSWORDS = include(CONFIG_PATH."cred.php");

    if(!isset($PASSWORDS[$name]))
    {
        $PASSWORDS = null;
        throw new RuntimeException("",300);
    }

    $password = $PASSWORDS[$name];
    $PASSWORDS = null;
    return $password;
}

function db_getConnection($env)
{
    if
    (
        !isset($env["config"]["conn_string"]) ||
        !isset($env["config"]["dbuser"]) ||
        !isset($env["config"]["dbpswd"])        
    )
    {
        throw new RuntimeException("",300);
    }

    $conn_string = $env["config"]["conn_string"];

    $dbuser = $env["config"]["dbuser"];
    $dbpswd = $env["config"]["dbpswd"];

    $conn = oci_connect($dbuser, $dbpswd, $conn_string);
    
    if (!$conn) {
        $e = oci_error();
        throw new RuntimeException(htmlentities($e['message'], ENT_QUOTES),301);
    }
    
    return $conn;
}

/*
<DOC>
@func db_getTransaction
@desc retrieve the transaction from QSIS database
@param conn : connection object (OCI)
@param cartID : cart id
@return array (row)
</DOC>
*/
function db_getTransaction($conn,$cartID)
{
    $sql = db_getSQL("GET_TRANSACTION");

    $stid = oci_parse($conn,$sql);
    oci_bind_by_name($stid,":cartID",$cartID);

    $result = oci_execute($stid);

    $row = oci_fetch_array($stid, OCI_ASSOC);

    if(!$row)
    {
        throw new RuntimeException($cartID,302);
    }

    return $row;
}

/*
<DOC>
@func db_writeTransaction
@desc update the transaction to QSIS database
@param conn : connection object (OCI)
@param cartID : cart id (string)
@param transID : trans id (string)
</DOC>
*/
function db_writeTransaction($conn,$cartID,$transID)
{
    $ip_criteria = null;

    //IP Address not required - as specified by Tony McCrory (email 22/10/20)
    /*
    if(isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && !empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
    {
        $ip_criteria = ", qub_ip_addr = :qub_ip_addr";
    }

    $stid = oci_parse($conn, "update sysadm.ps_qub_wpay_trans set qub_wp_trans_id = :transID, qub_trans_stage = '2'$ip_criteria where qub_wp_cart_id = :cartID");
    */

    $stid = oci_parse($conn, db_getSQL("WRITE_TRANSACTION"));
    oci_bind_by_name($stid, ':cartID',$cartID);
    oci_bind_by_name($stid, ':transID',$transID);

    //IP Address not required - as specified by Tony McCrory (email 22/10/20)
    /*
    if(!is_null($ip_criteria))
    {
        oci_bind_by_name($stid, ':qub_ip_addr',$_SERVER["HTTP_X_FORWARDED_FOR"]);
    }
    */
    
    $result = oci_execute($stid);

    if(!$result)
    {
        throw new RuntimeException("error=".oci_error($stid),303);
    }
}

/*
<DOC>
@func db_updateTransactionStatus
@desc update transaction with payment status, timestamp
@param conn : connection object (OCI)
@param transaction : transaction details (array)
@param database_result : payment status (string)
</DOC>
*/
function db_updateTransactionStatus($conn,$transaction,$database_result,$paymentMethod=" ")
{
    if(!isset($transaction["QUB_WP_CART_ID"]))
    {
        throw new RuntimeException(print_r($transaction,true),306);
    }

    $cartID = $transaction["QUB_WP_CART_ID"];

    $stid = oci_parse($conn,db_getSQL("UPDATE_TRANS_STATUS"));

    oci_bind_by_name($stid, ':cartID',$cartID);
    oci_bind_by_name($stid, ':transStage',$database_result);
    oci_bind_by_name($stid, ':paymentMethod',$paymentMethod);
    
    $result = oci_execute($stid);

    if(!$result)
    {
        throw new RuntimeException("error=".oci_error($stid),304);
    }
}

function db_getSQL($id,$parameters=null)
{
    global $SQL;

    if(!isset($SQL[strtoupper($id)]))
    {
        throw new RuntimeException("SQL: $id",307);
    }
    $sql = $SQL[$id];

    if(is_array($parameters))
    {
        foreach($parameters as $id=>$value)
        {
            $sql = str_replace("{{".strtoupper($id)."}}",$value,$sql);
        }
    }

    return $sql;
}

/*
<DOC>
@func debug
@desc wrapper function to write debug logs
@param message : the data to be written (string)
</DOC>
*/
function debug($message,$cartID=null)
{
    if(defined("DEBUG") && DEBUG===true)
    {
        $prefix = (!is_null($cartID)) ? "{$cartID} " : "";
        
        Log::write(Log::LOG_TYPE_DEBUG,$prefix.$message,Log::LOG_LEVEL_INFORMATIONAL);
    }
}

/*
<DOC>
@func db_log
@desc Write data to QSIS translog table
@param cartID : The cart id
@param transID : The transaction id
@param message : Mesage to be written
@param database : name of QSIS database (default=null) 
</DOC>
*/
function db_log($cartID,$transID,$message,$database=null)
{
    if(empty($cartID) || empty($transID) || empty($message))
    {
        Log::write(Log::LOG_TYPE_PAYMENTS,"cartID=$cartID transID=$transID message=$message",Log::LOG_LEVEL_ERROR);
        return;
    }

    $env = getQSISEnvironment($database);
    $conn = db_getConnection($env);

    $stid = oci_parse($conn,db_getSQL("LOG_INSERT"));

    oci_bind_by_name($stid, ':cartID',$cartID);
    oci_bind_by_name($stid, ':transID',$transID);
    oci_bind_by_name($stid, ':message',$message);
    
    $result = oci_execute($stid);

    if(!$result)
    {
        throw new RuntimeException("error=".print_r(oci_error($stid),true),305);
    }    
}

/*
<DOC>
@func getInfoFromPrefix
@desc extract the QSIS environment from the cart id's 3 letter prefix
@param cartID : The cart id
@return array (database)
</DOC>
*/
function getInfoFromPrefix($cartID)
{
    global $ENVIRONMENTS;

    $prefix = substr($cartID,0,3);
    $db_to_use = "";

    $temp = array();

    foreach($ENVIRONMENTS as $database=>$row)
    {
        if(isset($row["prefix"]) && $row["prefix"]==$prefix)
        {
            $db_to_use = $database;
            $temp["cartID"] = str_replace($prefix,"",$cartID);
            break;
        }
    }

    if(empty($db_to_use))
    {
        throw new RuntimeException("[$prefix]",403);
    }

    $temp["database"] = $db_to_use;

    return $temp;
}

//======================================================================================
// Order Notification Functions
//======================================================================================

/*
<DOC>
@func db_getDatabaseFromNotificationDetails
@desc Use details passed in the notification to get the database to be used for updating transaction
@param details : Details sent from Worldpay
@param transID : The transaction id
@return string (database name)
</DOC>
*/
function db_getDatabaseFromNotificationDetails($details)
{
    $temp = getInfoFromPrefix($details["cartID"]);

    $details["cartID"] = $temp["cartID"];
    $details["database"] = $temp["database"];

    return $details;
}

/*
<DOC>
@func getDetailsFromNotification
@desc Parse the xml sent from Worldpay push notification
@param post : The $_POST array
@return array (details)
</DOC>
*/
function getDetailsFromNotification($request_body)
{
    $xmlResult = simplexml_load_string(trim($request_body));

    if(!$xmlResult)
    {
        throw new RuntimeException("[".$request_body."]",402);
    }

    if(!isset($xmlResult->notify->orderStatusEvent))
    {
        throw new RuntimeException(print_r($xmlResult,true),400);
    }

    $cartID =  (string) $xmlResult->notify->orderStatusEvent->attributes()->orderCode[0];

    $paymentMethod = (string) $xmlResult->notify->orderStatusEvent->payment->paymentMethod;
    $paymentAmount = (string) $xmlResult->notify->orderStatusEvent->payment->amount->attributes()->value[0];
    $paymentStatus = (string) $xmlResult->notify->orderStatusEvent->journal->attributes()->journalType[0];

    $details["cartID"] = $cartID;
    $details["paymentMethod"] = $paymentMethod;
    $details["paymentAmount"] = $paymentAmount;
    $details["paymentStatus"] = $paymentStatus;

    return $details;
}

/*
<DOC>
@func writeOrderNotification
@desc Write new transaction status (3) to QSIS database. Write log update to translog.
@param database : The name of the QSIS database
@param details : Details from the Worldpay notification
</DOC>
*/
function writeOrderNotification($details)
{
    global $WP_RESULTS;

    if(!isset($details["database"]) || empty($details["database"]))
    {
        debug("writeOrderNotification - Database is empty".print_r($details,true));
    }

    $env = getQSISEnvironment($details["database"]);
    $conn = db_getConnection($env);

    $transaction = db_getTransaction($conn,$details["cartID"]);

    if(!isset($WP_RESULTS[$details["paymentStatus"]]) || $details["paymentStatus"]!=="CAPTURED")
    {
        throw new RuntimeException($details["paymentStatus"],401);
    }

    db_updateTransactionStatus($conn,$transaction,$WP_RESULTS[$details["paymentStatus"]]["DATABASE_FORMAT"],$details["paymentMethod"]);
    db_log($details["cartID"],$transaction["QUB_WP_TRANS_ID"],"TS3 - Payment captured",$details["database"]);
    Log::write(Log::LOG_TYPE_NOTIFICATIONS,"Transaction captured: ".$details["cartID"],Log::LOG_LEVEL_INFORMATIONAL);
}