<?php

/*
<DOC>
@file engine/Errors.php
@desc errors
@version v0.3
</DOC>
*/


/*
<DOC>
@variable ERROR #10
@type string
@desc Triggered in getConfigItem function. Config item is missing.
</DOC>
*/
$ERROR_MESSAGES[10] = "CONSTANT doesn't exist";

/*
<DOC>
@variable ERROR #100
@type string
@desc Triggered in checkParametersAndDatabaseConnection function. The POST data from QSIS/DAP is incomplete.
</DOC>
*/
$ERROR_MESSAGES[100] = "POST parameters are incomplete";

/*
<DOC>
@variable ERROR #101
@type string
@desc Triggered in checkParametersAndDatabaseConnection function. The POST address data from QSIS/DAP is incomplete. Address facility not currently in use.
</DOC>
*/
$ERROR_MESSAGES[101] = "Address parameters are incomplete";

/*
<DOC>
@variable ERROR #102
@type string
@desc Triggered in wp_requestPaymentURL function. Could not connect to the Worldpay Authentication Server.
</DOC>
*/
$ERROR_MESSAGES[102] = "Could not connect to Worldpay auth server";

/*
<DOC>
@variable ERROR #103
@type string
@desc Triggered in wp_requestPaymentURL function. The communication with Worldpay Auth server isn't working.
</DOC>
*/
$ERROR_MESSAGES[103] = "Error in Worldpay authentication response";

/*
<DOC>
@variable ERROR #104
@type string
@desc Triggered in generateXML function. The POST data from QSIS/DAP is incomplete.
</DOC>
*/
$ERROR_MESSAGES[104] = "POST parameters are incomplete";

/*
<DOC>
@variable ERROR #105
@type string
@desc Triggered in generateAddressXML function. The POST address data from QSIS/DAP is incomplete. Address facility not currently in use.
</DOC>
*/
$ERROR_MESSAGES[105] = "POST address parameters are incomplete";

/*
<DOC>
@variable ERROR #106
@type string
@desc Triggered in wp_requestPaymentURL function. Worldpay has not authenticated the request.
</DOC>
*/
$ERROR_MESSAGES[106] = "Could not authenticate with Worldpay auth server";

/*
<DOC>
@variable ERROR #107
@type string
@desc Triggered in wp_requestPaymentURL function. Incomplete or invalid XML response from Worldpay.
</DOC>
*/
$ERROR_MESSAGES[107] = "Missing XML nodes returned from Worldpay";

/*
<DOC>
@variable ERROR #200
@type string
@desc Triggered in resp_processResponse function. The MAC sent from Worldpay doesn't match the system's generated MAC.
</DOC>
*/
$ERROR_MESSAGES[200] = "MAC does not match";

/*
<DOC>
@variable ERROR #201
@type string
@desc Triggered in resp_processResponse function. Configuration (WP_RESULTS) doesn't have an entry that matches payment status sent from Worldpay.
</DOC>
*/
$ERROR_MESSAGES[201] = "Payment status not found";

/*
<DOC>
@variable ERROR #202
@type string
@desc Triggered in resp_processResponse function. Environment in continue_url (i.e. https://qsis.../cs92dev/...) does not match environment passed in POST from QSIS/DAP.
</DOC>
*/
$ERROR_MESSAGES[202] = "Transaction was initiated from a different environment"; 

/*
<DOC>
@variable ERROR #203
@type string
@desc Triggered in getQSISEnvironment function. Configuration (ENVIRONMENTS) doesn't have an entry that matches the environment passed in POST from QSIS/DAP.
</DOC>
*/
$ERROR_MESSAGES[203] = "Environment isn't set";

/*
<DOC>
@variable ERROR #204
@type string
@desc Triggered in wp_resp_processor file. The response from Worldpay does not contain an orderKey.
</DOC>
*/
$ERROR_MESSAGES[204] = "Order Key isn't set";

/*
<DOC>
@variable ERROR #205
@type string
@desc Triggered in resp_generateMAC function. Parameters required to generate a MAC haven't been passed in the Worldpay response.
</DOC>
*/
$ERROR_MESSAGES[205] = "Invalid parameters";

/*
<DOC>
@variable ERROR #300
@type string
@desc Triggered in db_getConnection function. Configuration (ENVIRONMENTS) doesn't have database credentials for the named database.
</DOC>
*/
$ERROR_MESSAGES[300] = "Environment connection details not set up";

/*
<DOC>
@variable ERROR #301
@type string
@desc Triggered in db_getConnection function. Could not connect to the QSIS database.
</DOC>
*/
$ERROR_MESSAGES[301] = "Database connection error";

/*
<DOC>
@variable ERROR #302
@type string
@desc Triggered in db_getTransaction function. The transaction could not be found in the ps_qub_wpay_trans table of QSIS database.
</DOC>
*/
$ERROR_MESSAGES[302] = "Couldn't retrieve transaction from database";

/*
<DOC>
@variable ERROR #303
@type string
@desc Triggered in db_writeTransaction function. Could not update the ps_qub_wpay_trans table of QSIS database.
</DOC>
*/

define("DEFAULT_DB_ERROR","Could not write to database");

$ERROR_MESSAGES[303] = DEFAULT_DB_ERROR;

/*
<DOC>
@variable ERROR #304
@type string
@desc Triggered in db_updateTransactionStatus function. Could not update the ps_qub_wpay_trans table of QSIS database.
</DOC>
*/
$ERROR_MESSAGES[304] = DEFAULT_DB_ERROR;

/*
<DOC>
@variable ERROR #305
@type string
@desc Triggered in db_log function. Could not insert to the ps_qub_wp_translog table of QSIS database.
</DOC>
*/
$ERROR_MESSAGES[305] = DEFAULT_DB_ERROR;

/*
<DOC>
@variable ERROR #306
@type string
@desc Triggered in db_updateTransactionStatus function.  The cart ID hasn't been passed in the transaction data.
</DOC>
*/
$ERROR_MESSAGES[306] = "Transaction cart id not set";

/*
<DOC>
@variable ERROR #307
@type string
@desc Triggered in db_getSQL function. The query couldn't be found in SQL config.
</DOC>
*/
$ERROR_MESSAGES[307] = "SQL Query not found";

/*
<DOC>
@variable ERROR #400
@type string
@desc Triggered in getDetailsFromNotification function. The XML passed from the Worldpay notification portal doesn't contain the necessary parameters.
</DOC>
*/
$ERROR_MESSAGES[400] = "Missing XML nodes returned from Worldpay";

/*
<DOC>
@variable ERROR #401
@type string
@desc Triggered in writeOrderNotification function. Configuration (WP_RESULTS) doesn't have an entry that matches payment status sent from Worldpay.
</DOC>
*/
$ERROR_MESSAGES[401] = "Invalid payment status";

/*
<DOC>
@variable ERROR #402
@type string
@desc Triggered in getDetailsFromNotification function. The Request body from Worldpay doesn't contain valid XML
</DOC>
*/
$ERROR_MESSAGES[402] = "Invalid XML in body";

/*
<DOC>
@variable ERROR #403
@type string
@desc Triggered in db_getDatabaseFromNotificationDetails function. The cartID doesn't have the necessary prefix to identify the database.
</DOC>
*/
$ERROR_MESSAGES[403] = "Database couldn't be identified from cartID";