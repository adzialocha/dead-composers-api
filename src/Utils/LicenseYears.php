<?php

namespace DeadComposers\Utils;

class LicenseYears {
    private $years = array(
        'af' => 50, // afghanistan
        'al' => 70, // albania
        'dz' => 50, // algeria
        'ad' => 70, // andorra
        'ao' => 50, // angola
        'ag' => 50, // antigua and barbuda
        'ar' => 70, // argentina
        'au' => 70, // australia
        'at' => 70, // austria
        'az' => 50, // azerbaijan
        'bs' => 70, // bahamas
        'bh' => 50, // bahrain
        'bd' => 60, // bangladesh
        'bb' => 50, // barbados
        'by' => 50, // belarus
        'be' => 70, // belgium
        'bz' => 50, // belize
        'bj' => 70, // benin
        'bt' => 50, // bhutan
        'bo' => 50, // bolivia
        'ba' => 70, // bosnia and herzegovina
        'bw' => 50, // botswana
        'br' => 70, // brazil
        'bn' => 50, // brunei darussalam
        'bg' => 70, // bulgaria
        'bf' => 70, // burkina faso
        'bi' => 50, // burundi
        'kh' => 50, // cambodia
        'cm' => 50, // cameroon
        'ca' => 50, // canada
        'cv' => 50, // cape verde
        'cf' => 50, // central african republic
        'td' => 70, // chad
        'cl' => 50, // chile
        'cn' => 50, // china
        'co' => 80, // colombia
        'km' => 50, // comoros
        'cg' => 50, // congo
        'cr' => 70, // costa rica
        'ci' => 70, // côte d\'ivoire
        'hr' => 70, // croatia
        'cu' => 50, // cuba
        'cy' => 70, // cyprus
        'cz' => 70, // czech republic
        'dk' => 70, // denmark
        'dj' => 50, // djibouti
        'dm' => 50, // dominica
        'do' => 50, // dominican republic
        'ec' => 70, // ecuador
        'eg' => 50, // egypt
        'sv' => 50, // el salvador
        'gq' => 80, // equatorial guinea
        'er' => 50, // eritrea
        'ee' => 70, // estonia
        'et' => 50, // ethiopia
        'fo' => 70, // faroe islands
        'fj' => 50, // fiji
        'fi' => 70, // finland
        'fr' => 70, // france
        'ga' => 50, // gabon
        'gm' => 50, // gambia
        'ge' => 70, // georgia
        'de' => 70, // germany
        'gh' => 70, // ghana
        'gr' => 70, // greece
        'gd' => 50, // grenada
        'gt' => 75, // guatemala
        'gn' => 50, // guinea
        'gw' => 50, // guinea-bissau
        'gy' => 50, // guyana
        'ht' => 60, // haiti
        'hn' => 50, // honduras
        'hu' => 70, // hungary
        'is' => 70, // iceland
        'in' => 70, // india
        'id' => 70, // indonesia
        'ir' => 50, // iran
        'iq' => 50, // iraq
        'ie' => 70, // ireland
        'il' => 70, // israel
        'it' => 70, // italy
        'jm' => 95, // jamaica
        'jp' => 50, // japan
        'jo' => 50, // jordan
        'kz' => 50, // kazakhstan
        'ke' => 50, // kenya
        'ki' => 50, // kiribati
        'kp' => 50, // north korea
        'kr' => 70, // south korea
        'kw' => 50, // kuwait
        'la' => 50, // lao people's democratic republic
        'lv' => 70, // latvia
        'lb' => 50, // lebanon
        'ls' => 50, // lesotho
        'lr' => 50, // liberia
        'ly' => 50, // libya
        'li' => 70, // liechtenstein
        'lt' => 70, // lithuania
        'lu' => 70, // luxembourg
        'mo' => 50, // macao
        'mk' => 70, // macedonia
        'mg' => 70, // madagascar
        'mw' => 50, // malawi
        'my' => 50, // malaysia
        'mv' => 50, // maldives
        'ml' => 70, // mali
        'mt' => 70, // malta
        'mr' => 70, // mauritania
        'mu' => 50, // mauritius
        'mx' => 100,// mexico
        'md' => 70, // moldova
        'mc' => 50, // monaco
        'mn' => 50, // mongolia
        'me' => 70, // montenegro
        'ma' => 70, // morocco
        'mz' => 70, // mozambique
        'mm' => 50, // myanmar
        'na' => 50, // namibia
        'nr' => 50, // nauru
        'np' => 50, // nepal
        'nl' => 70, // netherlands
        'nz' => 50, // new zealand
        'ni' => 70, // nicaragua
        'ne' => 50, // niger
        'ng' => 70, // nigeria
        'no' => 70, // norway
        'om' => 50, // oman
        'pk' => 50, // pakistan
        'pw' => 50, // palau
        'pa' => 50, // panama
        'pg' => 50, // papua new guinea
        'py' => 70, // paraguay
        'pe' => 50, // peru
        'ph' => 50, // philippines
        'pl' => 70, // poland
        'pt' => 70, // portugal
        'qa' => 50, // qatar
        'ro' => 70, // romania
        'ru' => 70, // russian federation
        'rw' => 50, // rwanda
        'kn' => 50, // saint kitts and nevis
        'lc' => 50, // saint lucia
        'vc' => 50, // saint vincent and the grenadines
        'ws' => 75, // samoa
        'sm' => 50, // san marino
        'st' => 70, // sao tome and principe
        'sa' => 50, // saudi arabia
        'sn' => 70, // senegal
        'rs' => 70, // serbia
        'sc' => 50, // seychelles
        'sg' => 70, // singapore
        'sk' => 70, // slovakia
        'si' => 70, // slovenia
        'sb' => 50, // solomon islands
        'za' => 50, // south africa
        'es' => 70, // spain
        'lk' => 70, // sri lanka
        'sd' => 50, // sudan
        'sr' => 50, // suriname
        'se' => 70, // sweden
        'ch' => 70, // switzerland
        'sy' => 50, // syria
        'tw' => 50, // taiwan, province of china
        'tj' => 50, // tajikistan
        'tz' => 50, // tanzania, united republic of
        'th' => 50, // thailand
        'tg' => 50, // togo
        'to' => 50, // tonga
        'tt' => 50, // trinidad and tobago
        'tn' => 50, // tunisia
        'tr' => 70, // turkey
        'tm' => 50, // turkmenistan
        'tv' => 50, // tuvalu
        'ug' => 50, // uganda
        'ua' => 70, // ukraine
        'ae' => 50, // united arab emirates
        'gb' => 70, // united kingdom
        'us' => 70, // united states of america
        'uy' => 50, // uruguay
        'uz' => 50, // uzbekistan
        'vu' => 50, // vanuatu
        've' => 60, // venezuela
        'vn' => 50, // vietnam
        'ye' => 30, // yemen
        'zm' => 50, // zambia
        'zw' => 70, // zimbabwe
    );

    function get_year($country_code) {
        if (!array_key_exists($country_code, $this->years)) {
            return false;
        }

        return $this->years[$country_code];
    }
}
