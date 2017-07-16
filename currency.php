<?php
//get currency symbol to pass country code
function getCurrency_symbol($countryCode = 'USD')
{
	//If user enter country code in Lower case convert it Upper case 
    $countryCode = strtoupper($countryCode);
    $currency = array(
    "AUD" => "&#36;" , //Australian Dollar
    "BRL" => "R&#36;" , // OR add &#8354; Brazilian Real
	"BDT" => "&#2547;", //Bangladeshi Taka
    "CAD" => "C&#36;" , //Canadian Dollar
	"CHF" => "Fr" , //Swiss Franc
	"CRC" => "&#8353;", //Costa Rican Colon
    "CZK" => "K&#269;" , //Czech Koruna
    "DKK" => "kr" , //Danish Krone
    "EUR" => "&euro;" , //Euro
	"GBP" => "&pound;" , //Pound Sterling
    "HKD" => "&#36" , //Hong Kong Dollar
    "HUF" => "Ft" , //Hungarian Forint
    "ILS" => "&#x20aa;" , //Israeli New Sheqel
    "INR" => "&#8377;", //Indian Rupee
	"ILS" => "&#8362;",	//Israeli New Shekel
    "JPY" => "&yen;" , //also use &#165; Japanese Yen
	"KZT" => "&#8376;", //Kazakhstan Tenge
	"KRW" => "&#8361;",	//Korean Won
	"KHR" => "&#6107;", //Cambodia Kampuchean Riel	
    "MYR" => "RM" , //Malaysian Ringgit 
    "MXN" => "&#36" , //Mexican Peso
    "NOK" => "kr" , //Norwegian Krone
	"NGN" => "&#8358;",	//Nigerian Naira
    "NZD" => "&#36" , //New Zealand Dollar
    "PHP" => "&#x20b1;" , //Philippine Peso
	"PKR" => "&#8360;" , //Pakistani Rupees
    "PLN" => "&#122;&#322;" ,//Polish Zloty
    "SEK" => "kr" , //Swedish Krona 
    "TWD" => "&#36;" , //Taiwan New Dollar 
    "THB" => "&#3647;" , //Thai Baht
    "TRY" => "&#8378;", //Turkish Lira
	"USD" => "&#36;" , //U.S. Dollar
	"VND" => "&#8363;"	//Vietnamese Dong
	
    );
    
	//check country code exit in array or not
    if(array_key_exists($countryCode, $currency)){
        return $currency[$countryCode];
    } else{
        echo"kindly add country code and decimal code values in function array </br>using reference link";
    }
    
}
echo getCurrency_symbol($countryCode='INR');
?>