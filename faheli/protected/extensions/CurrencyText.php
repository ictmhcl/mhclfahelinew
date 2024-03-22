<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of num2text
 *
 * @author Dev-Nazim
 */
class CurrencyText {

    private static $_dictionary = [
        0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten',
        11 => 'eleven', 12 => 'twelve', 13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen', 19 => 'nineteen', 20 => 'twenty',
        30 => 'thirty', 40 => 'fourty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 90 => 'ninety', 100 => 'hundred',
        1000 => 'thousand', 1000000 => 'million', 1000000000 => 'billion',
    ];
    private static $_major = 'Rufiya';
    private static $_minor = 'Laari';
    private static $_hyphen = '-';
    private static $_conjunction = ' and ';
    private static $_separator = ', ';
    private static $_mode = 'en';
    private static $_thousandIteration = 0;
    private static $_baseUnit = null;
    private static $_dvLakhIgnore = false;

    public static function parse($number, $mode = 'en') {

        // overflow
        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            trigger_error('Number to currency words converter only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX, E_USER_WARNING);
            return false;
        }
        
        // if not english then dhivehi
        if ($mode != 'en') {
            self::$_dictionary = [
                0 => 'ސުން', 1 => 'އެއް', 2 => 'ދެ', 3 => 'ތިން', 4 => 'ހަތަރު', 5 => 'ފަސް', 6 => 'ހަ', 7 => 'ހަތް', 8 => 'އަށް', 9 => 'ނުވަ', 10 => 'ދިހަ',
                11 => 'އެގާރަ', 12 => 'ބާރަ', 13 => 'ތޭރަ', 14 => 'ސާދަ', 15 => 'ފަނަރަ', 16 => 'ސޯޅަ', 17 => 'ސަތާރަ', 18 => 'އަށާރަ', 19 => 'ނަވާރަ', 20 => 'ވިހި',
                21 => 'އެކާވީސް', 22 => 'ބާވީސް', 23 => 'ތޭވީސް', 24 => 'ސައުވީސް', 25 => 'ފަންސަވީސް', 26 => 'ސައްބީސް', 27 => 'ހަތާވީސް', 28 => 'އަށާވީސް', 29 => 'ނަވާވީސް', 30 => 'ތިރީސް',
                40 => 'ސާޅީސް', 50 => 'ފަންސާސް', 60 => 'ފަސްދޮޅަސް', 70 => 'ހަތްދިހަ', 80 => 'އަށްޑިހަ', 90 => 'ނުވަދިހަ', 100 => 'ސަތޭކަ',
                200 => 'ދުއިސައްތަ',
                1000 => 'ހާސް', 100000=>'ލައްކަ', 1000000 => 'މިލިއަން', 1000000000 => 'ބިލިއަން'
            ];
            self::$_major = 'ރުފިޔާ';
            self::$_minor = 'ލާރި';
            self::$_hyphen = ' ';
            self::$_conjunction = ' ';
            self::$_separator = '، ';
            self::$_mode = $mode;
        }
        
        // initial values
        $string = $fraction = null;
        
        // check for fractions
        if (strpos($number, '.') !== false)
            list($number, $fraction) = explode('.', $number);
        
        $string = self::_convert_number_to_words($number) . ' ' . self::$_major;
        
        if (null !== $fraction && is_numeric($fraction))
            $string .= ' '.  self::_convert_number_to_words(substr($fraction*100, 0, 2)) . ' ' . self::$_minor;

        return $string;
    }

    private static function _convert_number_to_words($number) {

        if (!is_numeric($number))
            return false;
			
		$number = (int) $number;

        switch (true) {
            case $number < 21 && self::$_mode == 'en':
                $string = self::$_dictionary[$number];
                break;
            case $number < 31 && self::$_mode != 'en':
                $string = self::$_dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                $string = self::$_dictionary[$tens];
                if ($units)
                    $string .= self::$_hyphen . self::$_dictionary[$units];
                break;
            case $number < 1000:
                self::$_thousandIteration++;
                $hundreds = (int) ($number / 100);
                $remainder = $number % 100;
//                if (self::$_baseUnit == 1000 && self::$_mode != 'en' && !self::$_dvLakhIgnore) 
//                    $string = self::$_dictionary[$hundreds] . ' ' . self::$_dictionary[100000];
//                else 
                    $string = ($hundreds == 2 && self::$_mode != 'en') ? self::$_dictionary[200] : (self::$_dictionary[$hundreds] . ' ' . self::$_dictionary[100]);
                if ($remainder)
                    $string .= self::$_conjunction . self::_convert_number_to_words($remainder);
                break;
            default:
                self::$_baseUnit = pow(1000, floor(log($number, 1000)));
//                echo self::$_baseUnit.'<br>';
//                if (self::$_baseUnit == 1000000)
//                    self::$_dvLakhIgnore = true;
//                else
//                    self::$_dvLakhIgnore = false;
                $numBaseUnits = (int) ($number / self::$_baseUnit);
                $remainder = $number % self::$_baseUnit;
                $string = self::_convert_number_to_words($numBaseUnits, self::$_mode) . ' ' . self::$_dictionary[self::$_baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? self::$_conjunction : self::$_separator;
                    $string .= self::_convert_number_to_words($remainder);
                }
                break;
        }


        return $string;
    }

}
