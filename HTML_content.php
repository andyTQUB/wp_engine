<?php

/*
<DOC>
@file engine/HTML_content.php
@desc html fragments for display to user
@version v0.1
</DOC>
*/

/*
<DOC>
@variable HTML
@type Array (string)
</DOC>
*/
$HTML["001"] = <<<HTML
<h5></h5>
<div></div>
HTML;

//Parameters not set (ERROR)
$HTML["100"] = <<<HTML
<h5><i class="fas fa-frown"></i> Something went wrong</h5>
<p>Unfortunately your payment can't be processed at the moment. Please try again later.</p>
HTML;

//Address not set (ERROR)
$HTML["101"] = $HTML["100"];

//Worldpay not authenticated (ERROR)
$HTML["102"] = $HTML["100"];

$HTML["900"] = <<<HTML
<h5>Payments</h5>
<p>Unfortunately payments aren't available at the moment.<br>Please try again later.</p>
HTML;

$HTML["901"] = $HTML["900"];

$HTML["999"] = <<<HTML
THIS IS A TEST {{TESTVAR}}
HTML;

$HTML["2000"] = <<<HTML
<style>
    body { background: #eee; width: 100%; height: 100%; }
    div.container { background: #fff; border: 1px solid #ccc; padding: 10px; border-radius: 8px; box-shadow: 0 2px 6px 1px #ccc; }
</style>
<div class='container'>
    <h4>{{TITLE}}</h4>
    <div class='d-flex'>
        <div class='p-1'>{{INFO_1_ICON}}</div>
        <div class='p-1'>{{INFO_1_MESSAGE}}</div>
    </div>
    <div class='d-flex'>
        <div class='p-1'>{{INFO_2_ICON}}</div>
        <div class='p-1'>{{INFO_2_MESSAGE}}</div>
    </div>
    <div class='d-flex'>
        <div class='p-1'>{{INFO_3_ICON}}</div>
        <div class='p-1'>{{INFO_3_MESSAGE}}</div>
    </div>
    <div class='d-flex'>
        <div class='p-1'>{{INFO_4_ICON}}</div>
        <div class='p-1'>{{INFO_4_MESSAGE}}</div>
    </div>
</div>
HTML;

$HTML["10000"] = <<<HTML
<h3>Transaction</h3>
<hr>
<div class='row'>
    <div class='col-md-2 qsisLabel'>Cart ID</div>
    <div class='col-md-4'>{{QUB_WP_CART_ID}}</div>
    <div class='col-md-2 qsisLabel'>Status</div>
    <div class='col-md-4'>{{DISPLAY_QUB_TRANS_STAGE}} ({{QUB_TRANS_STAGE}}{{QUB_WP_STATUS_FLAG}})</div>
</div>

<div class='qsisVertSpacer-20'></div>

<div class='row'>
    <div class='col-md-2 qsisLabel'>Transaction ID</div>
    <div class='col-md-4'>{{QUB_WP_TRANS_ID}}</div>
    <div class='col-md-2 qsisLabel'>Dates</div>
    <div class='col-md-4'>Created: {{DISPLAY_CREATED_DTTM}}<br> Completed: {{DISPLAY_QUB_WP_TRANS_DTTM}}</div>
</div>

<div class='qsisVertSpacer-20'></div>

<div class='row'>
    <div class='col-md-2 qsisLabel'>Amount</div>
    <div class='col-md-4'>{{AMOUNT}}</div>
    <div class='col-md-2 qsisLabel'>User</div>
    <div class='col-md-4'>EmplID: {{EMPLID}}<br> OprID: {{CREATEOPRID}}</div>
</div>

<div class='qsisVertSpacer-20'></div>

<div class='row'>
    <div class='col-md-2 qsisLabel'>Functional ID</div>
    <div class='col-md-4'>{{QUB_WP_FUNCT_ID}}</div>
    <div class='col-md-2 qsisLabel'>Transaction Type</div>
    <div class='col-md-4'>{{QUB_WP_TRAN_TYPE}}</div>
</div>

<div class='qsisVertSpacer-20'></div>

<div class='row'>
    <div class='col-md-2 qsisLabel'>Payment Type</div>
    <div class='col-md-4'>{{QUB_PAYMENT_TYPE}}</div>
    <div class='col-md-2 qsisLabel'>Transaction Category</div>
    <div class='col-md-4'>{{QUB_WP_TRAN_CAT}}</div>
</div>

<div class='qsisVertSpacer-20'></div>

<div class='row'>
    <div class='col-md-2 qsisLabel'>Term</div>
    <div class='col-md-4'>{{STRM}}</div>
    <div class='col-md-2 qsisLabel'>Business Unit</div>
    <div class='col-md-4'>{{BUSINESS_UNIT}}</div>
</div>

<div class='qsisVertSpacer-20'></div>

<div class='row'>
    <div class='col-md-2 qsisLabel'>Original Transaction ID</div>
    <div class='col-md-4'>{{QUB_ORIG_TRAN_ID}}</div>
    <div class='col-md-2 qsisLabel'>IP Address</div>
    <div class='col-md-4'>{{QUB_IP_ADDR}}</div>
</div>

<div class='qsisVertSpacer-20'></div>

<div class='row'>
    <div class='col-md-2 qsisLabel'>Finance Group ID</div>
    <div class='col-md-4'>{{GROUP_ID_SF}}</div>
    <div class='col-md-2 qsisLabel'>DAP Applcation Number</div>
    <div class='col-md-4'>{{ADM_APPL_NBR}}</div>
</div>

<div class='qsisVertSpacer-20'></div>

<div class='row'>
    <div class='col-md-2 qsisLabel'>User Defined Variables</div>
    <div class='col-md-4'>{{QUB_WP_VAR_1}}<br>{{QUB_WP_VAR_2}}<br>{{QUB_WP_VAR_3}}</div>
</div>

<div class='qsisVertSpacer-20'></div>
<hr>

<h3>Log Data</h3>
<hr>
{{LOG_DATA}}
HTML;