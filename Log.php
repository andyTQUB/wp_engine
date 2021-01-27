<?php

/*
<DOC>
@class Log
@version 0.1
@desc write error messages and alerts to log files
</DOC>
*/

class Log
{
/*
<DOC>
@const LOG_TYPE_SYSTEM
@type string
@desc system log type
</DOC>
*/
    const LOG_TYPE_SYSTEM = "SYSTEM";

/*
<DOC>
@const LOG_TYPE_PAYMENTS
@type string
@desc payments log type
</DOC>
*/
    const LOG_TYPE_DEBUG = "DEBUG";
    const LOG_TYPE_PAYMENTS = "PAYMENTS";
    const LOG_TYPE_POSTS = "POSTS";
    const LOG_TYPE_NOTIFICATIONS = "NOTIFICATIONS";
    
    /*
    Emergency - System is unusable 	A panic condition.
    Alert - Action must be taken immediately. A condition that should be corrected immediately, such as a corrupted system database.
    Critical - Critical conditions. Hard device errors.
    Error - Error conditions 	
    Warning - Warning conditions 	
    Notice - Normal but significant conditions. Conditions that are not error conditions, but that may require special handling.
    Informational - Informational messages
    Debug
    */
    
    const LOG_LEVEL_EMERGENCY = "EMERGENCY";
    const LOG_LEVEL_ALERT = "ALERT";
    const LOG_LEVEL_CRITICAL = "CRITICAL";
    const LOG_LEVEL_ERROR = "ERROR";
    const LOG_LEVEL_WARNING = "WARNING";
    const LOG_LEVEL_NOTICE = "NOTICE";
    const LOG_LEVEL_INFORMATIONAL = "INFORMATIONAL";
    const LOG_LEVEL_DEBUG = "DEBUG";
    
/*
<DOC>
@method constructor
@param type : log type
@param message : data to be written to file
@param log_level : log level
@param session_id : current session id (default=null)
</DOC>
*/
    public static function write($type,$message,$log_level,$session_id=null)
    {
        $log_levels = array
        (
            Log::LOG_LEVEL_EMERGENCY,Log::LOG_LEVEL_ALERT,Log::LOG_LEVEL_CRITICAL,Log::LOG_LEVEL_ERROR,
            Log::LOG_LEVEL_WARNING,Log::LOG_LEVEL_NOTICE,Log::LOG_LEVEL_INFORMATIONAL,Log::LOG_LEVEL_DEBUG
        );
        
        if(!in_array($log_level,$log_levels))
        {
            $log_level = "WARNING";
        }
        
        switch(strtoupper($type))
        {
            case Log::LOG_TYPE_SYSTEM:

                $filename = date("Y-m-d")."_system.log";
                $filepath = LOG_PATH."system";

            break;

            case Log::LOG_TYPE_DEBUG:

                $filename = date("Y-m-d")."_debug.log";
                $filepath = LOG_PATH."debug";

            break;

            case Log::LOG_TYPE_PAYMENTS:

                $filename = date("Y-m-d")."_payments.log";
                $filepath = LOG_PATH."payments";

            break;

            case Log::LOG_TYPE_POSTS:

                $filename = date("Y-m-d")."_posts.log";
                $filepath = LOG_PATH."posts";

            break;
        
            case Log::LOG_TYPE_NOTIFICATIONS:

                $filename = date("Y-m-d")."_notifications.log";
                $filepath = LOG_PATH."notifications";

            break;

            default:

                $filename = date("Y-m-d")."_system.log";
                $filepath = LOG_PATH."system";

            break;
        }

        $date = date("d/m/y H:i:s");

        $ip = (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : "SYSTEM";
        
        $data = "\n$log_level\t$ip\t$date\t";
        if(!is_null($session_id)){ $data .= "$session_id\t"; }
        $data .= $message;

        $ok = file_put_contents($filepath."/".$filename,$data,FILE_APPEND);

        return $ok; 
    }
}