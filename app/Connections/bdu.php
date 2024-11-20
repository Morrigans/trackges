<?php

$MM_bdu_HOSTNAME  = 'localhost';
$MM_bdu_DATABASE  = 'mysql:redgescl_bdu';
$MM_bdu_DBTYPE    = preg_replace('/:.*$/', '', $MM_bdu_DATABASE);
$MM_bdu_DATABASE  = preg_replace('/^[^:]*?:/', '', $MM_bdu_DATABASE);
$MM_bdu_USERNAME  = 'redgescl_icrsredgescl';
$MM_bdu_PASSWORD  = '_z]*[nJOBGN-';
$MM_bdu_LOCALE    = 'En';
$MM_bdu_MSGLOCALE = 'En';
$MM_bdu_CTYPE     = 'P';
$KT_locale         = $MM_bdu_MSGLOCALE;
$KT_dlocale        = $MM_bdu_LOCALE;
$KT_serverFormat   = '%Y-%m-%d %H:%M:%S';
$QUB_Caching       = 'false';

$KT_localFormat = $KT_serverFormat;

if (!defined('CONN_DIR')) {
    define('CONN_DIR', dirname(__FILE__));
}

require_once CONN_DIR . '/../adodb/adodb.inc.php';
// esta linea se actualiza para php 7.4
$bdu = ADOnewConnection($MM_bdu_DBTYPE);

if ($MM_bdu_DBTYPE == 'access' || $MM_bdu_DBTYPE == 'odbc') {
    if ($MM_bdu_CTYPE == 'P') {
        $bdu->PConnect($MM_bdu_DATABASE, $MM_bdu_USERNAME, $MM_bdu_PASSWORD);
    } else {
        $bdu->Connect($MM_bdu_DATABASE, $MM_bdu_USERNAME, $MM_bdu_PASSWORD);
    }

} else if (($MM_bdu_DBTYPE == 'ibase') or ($MM_bdu_DBTYPE == 'firebird')) {
    if ($MM_bdu_CTYPE == 'P') {
        $bdu->PConnect($MM_bdu_HOSTNAME . ':' . $MM_bdu_DATABASE, $MM_bdu_USERNAME, $MM_bdu_PASSWORD);
    } else {
        $bdu->Connect($MM_bdu_HOSTNAME . ':' . $MM_bdu_DATABASE, $MM_bdu_USERNAME, $MM_bdu_PASSWORD);
    }

} else {
    if ($MM_bdu_CTYPE == 'P') {
        $bdu->PConnect($MM_bdu_HOSTNAME, $MM_bdu_USERNAME, $MM_bdu_PASSWORD, $MM_bdu_DATABASE);
    } else {
        $bdu->Connect($MM_bdu_HOSTNAME, $MM_bdu_USERNAME, $MM_bdu_PASSWORD, $MM_bdu_DATABASE);
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
