<?php

namespace CPSE\API\ProjectBundle\Helpers;

class Helper 
{
    /**
     * Ham dung de convert cac ky tu co dau thanh khong dau
     * Dung tot cho cac chuc nang SEO cho browser(vi nhieu engine ko
     * hieu duoc dau tieng viet, nen can phai bo dau tieng viet di)
     *
     * @param mixed $string
     */
    public static function codau2khongdau($string = '', $alphabetOnly = false, $tolower = true)
    {
        $output =  $string;
        if ($output != '') {
            //Tien hanh xu ly bo dau o day
            $search = array(
                '&#225;', '&#224;', '&#7843;', '&#227;', '&#7841;', 				// a' a` a? a~ a.
                '&#259;', '&#7855;', '&#7857;', '&#7859;', '&#7861;', '&#7863;',	// a( a('
                '&#226;', '&#7845;', '&#7847;', '&#7849;', '&#7851;', '&#7853;', 	// a^ a^'..
                '&#273;',											   			// d-
                '&#233;', '&#232;', '&#7867;', '&#7869;', '&#7865;',				// e' e`..
                '&#234;', '&#7871;', '&#7873;', '&#7875;', '&#7877;', '&#7879;',	// e^ e^'
                '&#237;', '&#236;', '&#7881;', '&#297;', '&#7883;',					// i' i`..
                '&#243;', '&#242;', '&#7887;', '&#245;', '&#7885;',					// o' o`..
                '&#244;', '&#7889;', '&#7891;', '&#7893;', '&#7895;', '&#7897;',	// o^ o^'..
                '&#417;', '&#7899;', '&#7901;', '&#7903;', '&#7905;', '&#7907;',	// o* o*'..
                '&#250;', '&#249;', '&#7911;', '&#361;', '&#7909;',					// u'..
                '&#432;', '&#7913;', '&#7915;', '&#7917;', '&#7919;', '&#7921;',	// u* u*'..
                '&#253;', '&#7923;', '&#7927;', '&#7929;', '&#7925;',				// y' y`..
                '&#193;', '&#192;', '&#7842;', '&#195;', '&#7840;',					// A' A` A? A~ A.
                '&#258;', '&#7854;', '&#7856;', '&#7858;', '&#7860;', '&#7862;',	// A( A('..
                '&#194;', '&#7844;', '&#7846;', '&#7848;', '&#7850;', '&#7852;',	// A^ A^'..
                '&#272;',															// D-
                '&#201;', '&#200;', '&#7866;', '&#7868;', '&#7864;',				// E' E`..
                '&#202;', '&#7870;', '&#7872;', '&#7874;', '&#7876;', '&#7878;',	// E^ E^'..
                '&#205;', '&#204;', '&#7880;', '&#296;', '&#7882;',					// I' I`..
                '&#211;', '&#210;', '&#7886;', '&#213;', '&#7884;',					// O' O`..
                '&#212;', '&#7888;', '&#7890;', '&#7892;', '&#7894;', '&#7896;',	// O^ O^'..
                '&#416;', '&#7898;', '&#7900;', '&#7902;', '&#7904;', '&#7906;',	// O* O*'..
                '&#218;', '&#217;', '&#7910;', '&#360;', '&#7908;',					// U' U`..
                '&#431;', '&#7912;', '&#7914;', '&#7916;', '&#7918;', '&#7920;',	// U* U*'..
                '&#221;', '&#7922;', '&#7926;', '&#7928;', '&#7924;'				// Y' Y`..
            );
            $search2 = array(
                'á', 'à', 'ả', 'ã', 'ạ', 				// a' a` a? a~ a.
                'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ',	// a( a('
                'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 	// a^ a^'..
                'đ',											   			// d-
                'é', 'è', 'ẻ', 'ẽ', 'ẹ',				// e' e`..
                'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ',	// e^ e^'
                'í', 'ì', 'ỉ', 'ĩ', 'ị',					// i' i`..
                'ó', 'ò', 'ỏ', 'õ', 'ọ',					// o' o`..
                'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ',	// o^ o^'..
                'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ',	// o* o*'..
                'ú', 'ù', 'ủ', 'ũ', 'ụ',					// u'..
                'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự',	// u* u*'..
                'ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ',				// y' y`..
                'Á', 'À', 'Ả', 'Ã', 'Ạ',					// A' A` A? A~ A.
                'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ',	// A( A('..
                'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ',	// A^ A^'..
                'Đ',															// D-
                'É', 'È', 'Ẻ', 'Ẽ', 'Ẹ',				// E' E`..
                'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ',	// E^ E^'..
                'Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị',					// I' I`..
                'Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ',					// O' O`..
                'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ',	// O^ O^'..
                'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ',	// O* O*'..
                'Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ',					// U' U`..
                'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự',	// U* U*'..
                'Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ'				// Y' Y`..
            );
            $replace = array(
                'a', 'a', 'a', 'a', 'a',
                'a', 'a', 'a', 'a', 'a', 'a',
                'a', 'a', 'a', 'a', 'a', 'a',
                'd',
                'e', 'e', 'e', 'e', 'e',
                'e', 'e', 'e', 'e', 'e', 'e',
                'i', 'i', 'i', 'i', 'i',
                'o', 'o', 'o', 'o', 'o',
                'o', 'o', 'o', 'o', 'o', 'o',
                'o', 'o', 'o', 'o', 'o', 'o',
                'u', 'u', 'u', 'u', 'u',
                'u', 'u', 'u', 'u', 'u', 'u',
                'y', 'y', 'y', 'y', 'y',
                'A', 'A', 'A', 'A', 'A',
                'A', 'A', 'A', 'A', 'A', 'A',
                'A', 'A', 'A', 'A', 'A', 'A',
                'D',
                'E', 'E', 'E', 'E', 'E',
                'E', 'E', 'E', 'E', 'E', 'E',
                'I', 'I', 'I', 'I', 'I',
                'O', 'O', 'O', 'O', 'O',
                'O', 'O', 'O', 'O', 'O', 'O',
                'O', 'O', 'O', 'O', 'O', 'O',
                'U', 'U', 'U', 'U', 'U',
                'U', 'U', 'U', 'U', 'U', 'U',
                'Y', 'Y', 'Y', 'Y', 'Y'
            );
            //print_r($search);
            $output = str_replace($search, $replace, $output);
            $output = str_replace($search2, $replace, $output);
            if ($alphabetOnly) {
                $output = self::alphabetonly($output);
            }
            if ($tolower) {
                $output = strtolower($output);
            }
        }
        return $output;
    }
    
    public static function alphabetonly($string = '')
    {
        $output = $string;
        //replace no alphabet character
        $output = preg_replace("/[^a-zA-Z0-9]/", "-", $output);
        $output = preg_replace("/-+/", "-", $output);
        $output = trim($output, '-');
        return $output;
    }
}