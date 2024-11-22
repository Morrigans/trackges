<?php

$MM_icrs_HOSTNAME  = 'localhost';
$MM_icrs_DATABASE  = 'mysql:redgescl_icrs';
$MM_icrs_DBTYPE    = preg_replace('/:.*$/', '', $MM_icrs_DATABASE);
$MM_icrs_DATABASE  = preg_replace('/^[^:]*?:/', '', $MM_icrs_DATABASE);
$MM_icrs_USERNAME  = 'redgescl_icrsredgescl';
$MM_icrs_PASSWORD  = '_z]*[nJOBGN-';
$MM_icrs_LOCALE    = 'En';
$MM_icrs_MSGLOCALE = 'En';
$MM_icrs_CTYPE     = 'P';
$KT_locale         = $MM_icrs_MSGLOCALE;
$KT_dlocale        = $MM_icrs_LOCALE;
$KT_serverFormat   = '%Y-%m-%d %H:%M:%S';
$QUB_Caching       = 'false';

$KT_localFormat = $KT_serverFormat;

if (!defined('CONN_DIR')) {
    define('CONN_DIR', dirname(__FILE__));
}

require_once CONN_DIR . '/../adodb/adodb.inc.php';
// esta linea se actualiza para php 7.4
$icrs = ADOnewConnection($MM_icrs_DBTYPE);

if ($MM_icrs_DBTYPE == 'access' || $MM_icrs_DBTYPE == 'odbc') {
    if ($MM_icrs_CTYPE == 'P') {
        $icrs->PConnect($MM_icrs_DATABASE, $MM_icrs_USERNAME, $MM_icrs_PASSWORD);
    } else {
        $icrs->Connect($MM_icrs_DATABASE, $MM_icrs_USERNAME, $MM_icrs_PASSWORD);
    }

} else if (($MM_icrs_DBTYPE == 'ibase') or ($MM_icrs_DBTYPE == 'firebird')) {
    if ($MM_icrs_CTYPE == 'P') {
        $icrs->PConnect($MM_icrs_HOSTNAME . ':' . $MM_icrs_DATABASE, $MM_icrs_USERNAME, $MM_icrs_PASSWORD);
    } else {
        $icrs->Connect($MM_icrs_HOSTNAME . ':' . $MM_icrs_DATABASE, $MM_icrs_USERNAME, $MM_icrs_PASSWORD);
    }

} else {
    if ($MM_icrs_CTYPE == 'P') {
        $icrs->PConnect($MM_icrs_HOSTNAME, $MM_icrs_USERNAME, $MM_icrs_PASSWORD, $MM_icrs_DATABASE);
    } else {
        $icrs->Connect($MM_icrs_HOSTNAME, $MM_icrs_USERNAME, $MM_icrs_PASSWORD, $MM_icrs_DATABASE);
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
mysqli_query("SET NAMES 'utf8'");
//mysqli_set_charset('utf8');
