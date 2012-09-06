<?php
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
        header("location: index.php");
        exit;
}

// Set the memory_limit high.  Fasthosts does not seem to support .htaccess php_values, 
// nor .user.ini.  --Nick Stokoe 2012-09-06
ini_set('memory_limit', '2048M');

