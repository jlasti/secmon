<?php
/**
 * Created by PhpStorm.
 * User: mkovac
 * Date: 5.3.2019
 * Time: 17:50
 */

namespace app\models\Event;
use Yii;
use yii\db\Exception;

class AnalyzedConfig
{

    /**
     * get analyzed country code count
     * example |"US"|2|
     *
     * @param $params
     * @return array
     */
    public function getAnalyzedCodeCount($params){
        $dataArray = [];
        $defaultPointValue = 0;

        $analyzedCodes = self::getAnalyZedCodes($params[':id']);
        for ($i = 0; $i < sizeof($analyzedCodes); $i++) {
            $myObj = (object)[];
            $myObj->id = self::getIndexOfCountryCode(pg_escape_string($analyzedCodes[$i]["code"]));
            $myObj->value = intval($analyzedCodes[$i]["count"]);
            $defaultPointValue += $myObj->value;
            if ($myObj->id == null)
                continue;
            array_push($dataArray,$myObj);
        }
        // default country from analyse
        $myObj = (object)[];
        $myObj->id = self::getIndexOfCountryCode(pg_escape_string($analyzedCodes[0]["src_code"]));
        $myObj->value = $defaultPointValue;
        array_push($dataArray,$myObj);

        return $dataArray;
    }


    /**
     * get analyzed points
     * example |"Bern"|50.8371|4.3676|1|
     * @param $params
     * @return array
     */
    public function getAnalyzedAllPoints($params){
        $dataArray = [];
        $defaultPointScale = 0;

        $analyzedCodes = self::getAnalyZedPoints($params[':id']);
        for ($i = 0; $i < sizeof($analyzedCodes); $i++) {
            $myObj = (object)[];
            $myObj->title = $analyzedCodes[$i]["city"];
            if ($myObj->title == null)
                $myObj->title = "Unknown city";
            $myObj->latitude = intval($analyzedCodes[$i]["latitude"]);
            $myObj->longitude = intval($analyzedCodes[$i]["longitude"]);
            $myObj->scale = intval($analyzedCodes[$i]["events_count"]);
            $myObj->value = $analyzedCodes[$i]["events_count"];
            if (!$analyzedCodes[$i]["flag"])
                $defaultPointScale += $myObj->value;
            if ($myObj->latitude == 0 || $myObj->longitude == 0)
                continue;
            $myObj->multiGeoLine = self::prepareLines($myObj, $analyzedCodes[$i]["src_latitude"],$analyzedCodes[$i]["src_longitude"]);
            array_push($dataArray,$myObj);
        }
        // default city from analyse
        if ($defaultPointScale != 0) {
            $myObj = (object)[];
            $myObj->title = $analyzedCodes[0]["src_city"];
            if ($myObj->title == null)
                $myObj->title = "Unknown city";
            $myObj->latitude = intval($analyzedCodes[0]["src_latitude"]);
            $myObj->longitude = intval($analyzedCodes[0]["src_longitude"]);
            $myObj->scale = intval($defaultPointScale);
            $myObj->value = $defaultPointScale;
            array_push($dataArray, $myObj);
        }
        return $dataArray;
    }

    /**
     * @param $params
     * @return array
     */
    private function getAnalyZedPoints($params){
        try {
            return Yii::$app->db->createCommand(/** @lang text */
                "SELECT city, latitude, longitude, events_count, src_latitude, src_longitude, src_city, flag FROM analyzed_events WHERE iteration = (SELECT iteration FROM analyzed_events WHERE events_normalized_id = :id ORDER BY iteration DESC LIMIT 1)")
                ->bindValue(':id', $params)
                ->queryAll();
        } catch (Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }
    }

    /**
     * @param $params
     * @return array
     */
    private function getAnalyZedCodes($params){
        try {
            return Yii::$app->db->createCommand(/** @lang text */
                "select t.code, sum (t.events_count) as count, t.src_code as src_code from (select code, events_count, src_code from analyzed_events a where events_normalized_id = :id group by code, src_code, events_count HAVING max(iteration) = (SELECT max(iteration) as iteration FROM analyzed_events)) t GROUP BY t.code, t.src_code")
                ->bindValue(':id', $params)
                ->queryAll();
        } catch (Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }
    }

    /**
     * @param $myObj
     * @param $src_latitude
     * @param $src_longitude
     * @param $flag
     * @return object
     */
    private function prepareLines($myObj, $src_latitude, $src_longitude){
        $newObject = (object)[];
        $newObject->latitude = $myObj->latitude;
        $newObject->longitude = $myObj->longitude;

        $newObject2 = (object)[];
        $newObject2->latitude = intval($src_latitude);
        $newObject2->longitude = intval($src_longitude);

        $newArray = array();
        array_push($newArray, $newObject);
        array_push($newArray, $newObject2);


        $newArray2 = array();
        array_push($newArray2,$newArray);

        $nextObject = (object)[];
        $nextObject->multiGeoLine = $newArray2;

        return $nextObject;
    }

    /**
     * @param $code
     * @return int
     */
    private function getIndexOfCountryCode($code){
        if ($code == null || !is_string($code))
            return null;

        switch ($code){
            case "FR" : return 0 ;
            case "TV" : return 1 ;
            case "BV" : return 2 ;
            case "GI" : return 3 ;
            case "GO" : return 4 ;
            case "JU" : return 5 ;
            case "UM-DQ" : return 6 ;
            case "UM-FQ" : return 7 ;
            case "UM-HQ" : return 8 ;
            case "UM-JQ" : return 9 ;
            case "UM-MQ" : return 10 ;
            case "UM-WQ" : return 11 ;
            case "BQ" : return 12 ;
            case "NL" : return 13 ;
            case "ZW" : return 14 ;
            case "ZM" : return 15 ;
            case "ZA" : return 16 ;
            case "YE" : return 17 ;
            case "WS" : return 18 ;
            case "WF" : return 19 ;
            case "PS" : return 20 ;
            case "VU" : return 21 ;
            case "VN" : return 22 ;
            case "VI" : return 23 ;
            case "VG" : return 24 ;
            case "VE" : return 25 ;
            case "VC" : return 26 ;
            case "VA" : return 27 ;
            case "UZ" : return 28 ;
            case "US" : return 29 ;
            case "UY" : return 30 ;
            case "UA" : return 31 ;
            case "UG" : return 32 ;
            case "TZ" : return 33 ;
            case "TW" : return 34 ;
            case "TR" : return 35 ;
            case "TN" : return 36 ;
            case "TT" : return 37 ;
            case "TO" : return 38 ;
            case "TL" : return 39 ;
            case "TM" : return 40 ;
            case "TK" : return 41 ;
            case "TJ" : return 42 ;
            case "TH" : return 43 ;
            case "TG" : return 44 ;
            case "TD" : return 45 ;
            case "TC" : return 46 ;
            case "SY" : return 47 ;
            case "SC" : return 48 ;
            case "SX" : return 49 ;
            case "SZ" : return 50 ;
            case "SE" : return 51 ;
            case "SI" : return 52 ;
            case "SK" : return 53 ;
            case "SR" : return 54 ;
            case "ST" : return 55 ;
            case "RS" : return 56 ;
            case "PM" : return 57 ;
            case "SO" : return 58 ;
            case "SM" : return 59 ;
            case "SV" : return 60 ;
            case "SL" : return 61 ;
            case "SB" : return 62 ;
            case "SH" : return 63 ;
            case "GS" : return 64 ;
            case "SG" : return 65 ;
            case "SN" : return 66 ;
            case "SS" : return 67 ;
            case "SD" : return 68 ;
            case "SA" : return 69 ;
            case "EH" : return 70 ;
            case "RW" : return 71 ;
            case "RU" : return 72 ;
            case "RO" : return 73 ;
            case "RE" : return 74 ;
            case "QA" : return 75 ;
            case "PF" : return 76 ;
            case "PY" : return 77 ;
            case "PT" : return 78 ;
            case "KP" : return 79 ;
            case "PR" : return 80 ;
            case "PL" : return 81 ;
            case "PG" : return 82 ;
            case "PW" : return 83 ;
            case "PH" : return 84 ;
            case "PE" : return 85 ;
            case "PN" : return 86 ;
            case "PA" : return 87 ;
            case "PK" : return 88 ;
            case "OM" : return 89 ;
            case "NZ" : return 90 ;
            case "SJ" : return 91 ;
            case "NR" : return 92 ;
            case "NP" : return 93 ;
            case "NO" : return 94 ;
            case "NU" : return 95 ;
            case "NI" : return 96 ;
            case "NG" : return 97 ;
            case "NF" : return 98 ;
            case "NE" : return 99 ;
            case "NC" : return 100 ;
            case "NA" : return 101 ;
            case "YT" : return 102 ;
            case "MY" : return 103 ;
            case "MW" : return 104 ;
            case "MU" : return 105 ;
            case "MQ" : return 106 ;
            case "MS" : return 107 ;
            case "MR" : return 108 ;
            case "MZ" : return 109 ;
            case "MP" : return 110 ;
            case "MN" : return 111 ;
            case "ME" : return 112 ;
            case "MM" : return 113 ;
            case "MT" : return 114 ;
            case "ML" : return 115 ;
            case "MK" : return 116 ;
            case "MH" : return 117 ;
            case "MX" : return 118 ;
            case "MV" : return 119 ;
            case "MG" : return 120 ;
            case "MD" : return 121 ;
            case "MC" : return 122 ;
            case "MA" : return 123 ;
            case "MF" : return 124 ;
            case "MO" : return 125 ;
            case "LV" : return 126 ;
            case "LU" : return 127 ;
            case "LT" : return 128 ;
            case "LS" : return 129 ;
            case "LK" : return 130 ;
            case "LI" : return 131 ;
            case "LC" : return 132 ;
            case "LY" : return 133 ;
            case "LR" : return 134 ;
            case "LB" : return 135 ;
            case "LA" : return 136 ;
            case "KW" : return 137 ;
            case "XK" : return 138 ;
            case "KR" : return 139 ;
            case "KN" : return 140 ;
            case "KI" : return 141 ;
            case "KH" : return 142 ;
            case "KG" : return 143 ;
            case "KE" : return 144 ;
            case "KZ" : return 145 ;
            case "JP" : return 146 ;
            case "JO" : return 147 ;
            case "JE" : return 148 ;
            case "JM" : return 149 ;
            case "IT" : return 150 ;
            case "IL" : return 151 ;
            case "IS" : return 152 ;
            case "IQ" : return 153 ;
            case "IR" : return 154 ;
            case "IE" : return 155 ;
            case "IO" : return 156 ;
            case "IN" : return 157 ;
            case "IM" : return 158 ;
            case "ID" : return 159 ;
            case "HU" : return 160 ;
            case "HT" : return 161 ;
            case "HR" : return 162 ;
            case "HN" : return 163 ;
            case "HM" : return 164 ;
            case "HK" : return 165 ;
            case "GY" : return 166 ;
            case "GU" : return 167 ;
            case "GF" : return 168 ;
            case "GT" : return 169 ;
            case "GL" : return 170 ;
            case "GD" : return 171 ;
            case "GR" : return 172 ;
            case "GQ" : return 173 ;
            case "GW" : return 174 ;
            case "GM" : return 175 ;
            case "GP" : return 176 ;
            case "GN" : return 177 ;
            case "GH" : return 178 ;
            case "GG" : return 179 ;
            case "GE" : return 180 ;
            case "GA" : return 181 ;
            case "FM" : return 182 ;
            case "FO" : return 183 ;
            case "FK" : return 184 ;
            case "FJ" : return 185 ;
            case "FI" : return 186 ;
            case "ET" : return 187 ;
            case "EE" : return 188 ;
            case "ES" : return 189 ;
            case "ER" : return 190 ;
            case "GB" : return 191 ;
            case "EG" : return 192 ;
            case "EC" : return 193 ;
            case "DZ" : return 194 ;
            case "DO" : return 195 ;
            case "DK" : return 196 ;
            case "DM" : return 197 ;
            case "DJ" : return 198 ;
            case "DE" : return 199 ;
            case "CZ" : return 200 ;
            case "CY" : return 201 ;
            case "KY" : return 202 ;
            case "CX" : return 203 ;
            case "CW" : return 204 ;
            case "CU" : return 205 ;
            case "CR" : return 206 ;
            case "CV" : return 207 ;
            case "KM" : return 208 ;
            case "CO" : return 209 ;
            case "CK" : return 210 ;
            case "CG" : return 211 ;
            case "CD" : return 212 ;
            case "CM" : return 213 ;
            case "CI" : return 214 ;
            case "CN" : return 215 ;
            case "CL" : return 216 ;
            case "CH" : return 217 ;
            case "CC" : return 218 ;
            case "CA" : return 219 ;
            case "CF" : return 220 ;
            case "BE" : return 221 ;
            case "BW" : return 222 ;
            case "BT" : return 223 ;
            case "BN" : return 224 ;
            case "BB" : return 225 ;
            case "BR" : return 226 ;
            case "BO" : return 227 ;
            case "BM" : return 228 ;
            case "BZ" : return 229 ;
            case "BY" : return 230 ;
            case "BL" : return 231 ;
            case "BS" : return 232 ;
            case "BH" : return 233 ;
            case "BA" : return 234 ;
            case "BG" : return 235 ;
            case "BD" : return 236 ;
            case "BF" : return 237 ;
            case "BJ" : return 238 ;
            case "BI" : return 239 ;
            case "AZ" : return 240 ;
            case "AT" : return 241 ;
            case "AU" : return 242 ;
            case "TF" : return 243 ;
            case "AS" : return 244 ;
            case "AM" : return 245 ;
            case "AR" : return 246 ;
            case "AE" : return 247 ;
            case "AD" : return 248 ;
            case "AX" : return 249 ;
            case "AL" : return 250 ;
            case "AI" : return 251 ;
            case "AO" : return 252 ;
            case "AF" : return 253 ;
            case "AG" : return 254 ;
            case "AW" : return 255 ;
        }
    }
}