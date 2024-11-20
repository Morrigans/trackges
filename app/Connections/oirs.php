<?php

$MM_oirs_HOSTNAME  = '186.64.116.150';
$MM_oirs_DATABASE  = 'mysql:redgescl_cli_santiago';
$MM_oirs_DBTYPE    = preg_replace('/:.*$/', '', $MM_oirs_DATABASE);
$MM_oirs_DATABASE  = preg_replace('/^[^:]*?:/', '', $MM_oirs_DATABASE);
$MM_oirs_USERNAME  = 'redgescl_icrsredgescl';
$MM_oirs_PASSWORD  = '_z]*[nJOBGN-';
$MM_oirs_LOCALE    = 'En';
$MM_oirs_MSGLOCALE = 'En';
$MM_oirs_CTYPE     = 'P';
$KT_locale         = $MM_oirs_MSGLOCALE;
$KT_dlocale        = $MM_oirs_LOCALE;
$KT_serverFormat   = '%Y-%m-%d %H:%M:%S';
$QUB_Caching       = 'false';

$KT_localFormat = $KT_serverFormat;

if (!defined('CONN_DIR')) {
    define('CONN_DIR', dirname(__FILE__));
}

require_once CONN_DIR . '/../adodb/adodb.inc.php';
// esta linea se actualiza para php 7.4
$oirs = ADOnewConnection($MM_oirs_DBTYPE);

if ($MM_oirs_DBTYPE == 'access' || $MM_oirs_DBTYPE == 'odbc') {
    if ($MM_oirs_CTYPE == 'P') {
        $oirs->PConnect($MM_oirs_DATABASE, $MM_oirs_USERNAME, $MM_oirs_PASSWORD);
    } else {
        $oirs->Connect($MM_oirs_DATABASE, $MM_oirs_USERNAME, $MM_oirs_PASSWORD);
    }

} else if (($MM_oirs_DBTYPE == 'ibase') or ($MM_oirs_DBTYPE == 'firebird')) {
    if ($MM_oirs_CTYPE == 'P') {
        $oirs->PConnect($MM_oirs_HOSTNAME . ':' . $MM_oirs_DATABASE, $MM_oirs_USERNAME, $MM_oirs_PASSWORD);
    } else {
        $oirs->Connect($MM_oirs_HOSTNAME . ':' . $MM_oirs_DATABASE, $MM_oirs_USERNAME, $MM_oirs_PASSWORD);
    }

} else {
    if ($MM_oirs_CTYPE == 'P') {
        $oirs->PConnect($MM_oirs_HOSTNAME, $MM_oirs_USERNAME, $MM_oirs_PASSWORD, $MM_oirs_DATABASE);
    } else {
        $oirs->Connect($MM_oirs_HOSTNAME, $MM_oirs_USERNAME, $MM_oirs_PASSWORD, $MM_oirs_DATABASE);
    }

}

if (!function_exists('updateMagicQuotes')) {
    function updateMagicQuotes($HTTP_VARS)
    {
        if (is_array($HTTP_VARS)) {
            foreach ($HTTP_VARS as $name => $value) {
                if (!is_array($value)) {
                    $HTTP_VARS[$name] = addslashes($value);
                } else {
                    foreach ($value as $name1 => $value1) {
                        if (!is_array($value1)) {
                            $HTTP_VARS[$name1][$value1] = addslashes($value1);
                        }
                    }
                }
            }
        }
        return $HTTP_VARS;
    }

    if (!get_magic_quotes_gpc()) {
        $_GET    = updateMagicQuotes($_GET);
        $_POST   = updateMagicQuotes($_POST);
        $_COOKIE = updateMagicQuotes($_COOKIE);
    }
}
if (!isset($_SERVER['REQUEST_URI']) && isset($_ENV['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = $_ENV['REQUEST_URI'];
}
if (!isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . (isset($_SERVER['QUERY_STRING']) ? "?" . $_SERVER['QUERY_STRING'] : "");
}
// mysqli_query("SET NAMES 'utf8'");
//mysqli_set_charset('utf8');
