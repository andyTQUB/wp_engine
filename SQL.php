<?php

$SQL["LOG_INSERT"] = <<<SQL
insert into 
    sysadm.ps_qub_wp_translog 
    (
        qub_wp_cart_id,
        qub_wp_trans_id,
        created_dttm,
        qub_wp_message
    ) 
    values 
    (
        :cartID,
        :transID,
        systimestamp,
        :message
    )
SQL;

$SQL["UPDATE_TRANS_STATUS"] = <<<SQL
update 
    sysadm.ps_qub_wpay_trans 
set 
    qub_trans_stage = :transStage, 
    qub_payment_type = :paymentMethod,
    qub_wp_trans_dttm = systimestamp 
where 
    qub_wp_cart_id = :cartID
SQL;

$SQL["WRITE_TRANSACTION"] = <<<SQL
update 
    sysadm.ps_qub_wpay_trans 
set 
    qub_wp_trans_id = :transID, 
    qub_trans_stage = '2' 
where 
    qub_wp_cart_id = :cartID
SQL;

$SQL["GET_TRANSACTION"] = <<<SQL
select 
    a.*,
    to_char(a.created_dttm, 'YYYY-MM-DD HH24:MI:SS') as display_created_dttm,
    to_char(a.qub_wp_trans_dttm, 'YYYY-MM-DD HH24:MI:SS') as display_qub_wp_trans_dttm,
    c.qub_continue_url 
from 
    sysadm.ps_qub_wpay_trans a
inner join
    sysadm.ps_qub_wpay_area b
on
    a.qub_wp_funct_id = b.qub_wp_funct_id
inner join
    sysadm.ps_qub_wp_javacnfg c
on
    b.qub_wp_acc_id = c.qub_wp_acc_id
where
    a.qub_wp_cart_id = :cartID
SQL;