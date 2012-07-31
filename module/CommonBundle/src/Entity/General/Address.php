<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CommonBundle\Entity\General;

/**
 * This class represents a address entry that is saved in the database
 *
 * @Entity(repositoryClass="CommonBundle\Repository\General\Address")
 * @Table(name="general.address")
 */
class Address 
{
    /**
     * @static
     * @var array All the allowed country values
     */
    public static $countries = array(
        'Africa' => array(
            'DZ' => 'Algeria',
            'AO' => 'Angola',
            'BJ' => 'Benin',
            'BW' => 'Botswana',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'CM' => 'Cameroon',
            'CV' => 'Cape Verde',
            'CF' => 'Central African Republic',
            'TD' => 'Chad',
            'KM' => 'Comoros',
            'CG' => 'Congo - Brazzaville',
            'CD' => 'Congo - Kinshasa',
            'CI' => 'Côte d’Ivoire',
            'DJ' => 'Djibouti',
            'EG' => 'Egypt',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'ET' => 'Ethiopia',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GH' => 'Ghana',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'KE' => 'Kenya',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libya',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'ML' => 'Mali',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'YT' => 'Mayotte',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'NA' => 'Namibia',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'RW' => 'Rwanda',
            'RE' => 'Réunion',
            'SH' => 'Saint Helena',
            'SN' => 'Senegal',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SO' => 'Somalia',
            'ZA' => 'South Africa',
            'SD' => 'Sudan',
            'SZ' => 'Swaziland',
            'ST' => 'São Tomé and Príncipe',
            'TZ' => 'Tanzania',
            'TG' => 'Togo',
            'TN' => 'Tunisia',
            'UG' => 'Uganda',
            'EH' => 'Western Sahara',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
        ),
        'Americas' => array(
            'AI' => 'Anguilla',
            'AG' => 'Antigua and Barbuda',
            'AR' => 'Argentina',
            'AW' => 'Aruba',
            'BS' => 'Bahamas',
            'BB' => 'Barbados',
            'BZ' => 'Belize',
            'BM' => 'Bermuda',
            'BO' => 'Bolivia',
            'BR' => 'Brazil',
            'VG' => 'British Virgin Islands',
            'CA' => 'Canada',
            'KY' => 'Cayman Islands',
            'CL' => 'Chile',
            'CO' => 'Colombia',
            'CR' => 'Costa Rica',
            'CU' => 'Cuba',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'EC' => 'Ecuador',
            'SV' => 'El Salvador',
            'FK' => 'Falkland Islands',
            'GF' => 'French Guiana',
            'GL' => 'Greenland',
            'GD' => 'Grenada',
            'GP' => 'Guadeloupe',
            'GT' => 'Guatemala',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HN' => 'Honduras',
            'JM' => 'Jamaica',
            'MQ' => 'Martinique',
            'MX' => 'Mexico',
            'MS' => 'Montserrat',
            'AN' => 'Netherlands Antilles',
            'NI' => 'Nicaragua',
            'PA' => 'Panama',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PR' => 'Puerto Rico',
            'BL' => 'Saint Barthélemy',
            'KN' => 'Saint Kitts and Nevis',
            'LC' => 'Saint Lucia',
            'MF' => 'Saint Martin',
            'PM' => 'Saint Pierre and Miquelon',
            'VC' => 'Saint Vincent and the Grenadines',
            'SR' => 'Suriname',
            'TT' => 'Trinidad and Tobago',
            'TC' => 'Turks and Caicos Islands',
            'VI' => 'U.S. Virgin Islands',
            'US' => 'United States',
            'UY' => 'Uruguay',
            'VE' => 'Venezuela',
        ),
        'Asia' => array(
            'AF' => 'Afghanistan',
            'AM' => 'Armenia',
            'AZ' => 'Azerbaijan',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BT' => 'Bhutan',
            'BN' => 'Brunei',
            'KH' => 'Cambodia',
            'CN' => 'China',
            'CY' => 'Cyprus',
            'GE' => 'Georgia',
            'HK' => 'Hong Kong SAR China',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran',
            'IQ' => 'Iraq',
            'IL' => 'Israel',
            'JP' => 'Japan',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyzstan',
            'LA' => 'Laos',
            'LB' => 'Lebanon',
            'MO' => 'Macau SAR China',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'MN' => 'Mongolia',
            'MM' => 'Myanmar [Burma]',
            'NP' => 'Nepal',
            'NT' => 'Neutral Zone',
            'KP' => 'North Korea',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PS' => 'Palestinian Territories',
            'YD' => 'People\'s Democratic Republic of Yemen',
            'PH' => 'Philippines',
            'QA' => 'Qatar',
            'SA' => 'Saudi Arabia',
            'SG' => 'Singapore',
            'KR' => 'South Korea',
            'LK' => 'Sri Lanka',
            'SY' => 'Syria',
            'TW' => 'Taiwan',
            'TJ' => 'Tajikistan',
            'TH' => 'Thailand',
            'TL' => 'Timor-Leste',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'AE' => 'United Arab Emirates',
            'UZ' => 'Uzbekistan',
            'VN' => 'Vietnam',
            'YE' => 'Yemen',
        ),
        'Europe' => array(
            'AL' => 'Albania',
            'AD' => 'Andorra',
            'AT' => 'Austria',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BA' => 'Bosnia and Herzegovina',
            'BG' => 'Bulgaria',
            'HR' => 'Croatia',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'EE' => 'Estonia',
            'FO' => 'Faroe Islands',
            'FI' => 'Finland',
            'FR' => 'France',
            'DE' => 'Germany',
            'GI' => 'Gibraltar',
            'GR' => 'Greece',
            'GG' => 'Guernsey',
            'HU' => 'Hungary',
            'IS' => 'Iceland',
            'IE' => 'Ireland',
            'IM' => 'Isle of Man',
            'IT' => 'Italy',
            'JE' => 'Jersey',
            'LV' => 'Latvia',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MK' => 'Macedonia',
            'MT' => 'Malta',
            'FX' => 'Metropolitan France',
            'MD' => 'Moldova',
            'MC' => 'Monaco',
            'ME' => 'Montenegro',
            'NL' => 'Netherlands',
            'NO' => 'Norway',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'RO' => 'Romania',
            'RU' => 'Russia',
            'SM' => 'San Marino',
            'RS' => 'Serbia',
            'CS' => 'Serbia and Montenegro',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'ES' => 'Spain',
            'SJ' => 'Svalbard and Jan Mayen',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'UA' => 'Ukraine',
            'SU' => 'Union of Soviet Socialist Republics',
            'GB' => 'United Kingdom',
            'VA' => 'Vatican City',
            'AX' => 'Åland Islands',
        ),
        'Oceania' => array(
            'AS' => 'American Samoa',
            'AQ' => 'Antarctica',
            'AU' => 'Australia',
            'BV' => 'Bouvet Island',
            'IO' => 'British Indian Ocean Territory',
            'CX' => 'Christmas Island',
            'CC' => 'Cocos [Keeling] Islands',
            'CK' => 'Cook Islands',
            'FJ' => 'Fiji',
            'PF' => 'French Polynesia',
            'TF' => 'French Southern Territories',
            'GU' => 'Guam',
            'HM' => 'Heard Island and McDonald Islands',
            'KI' => 'Kiribati',
            'MH' => 'Marshall Islands',
            'FM' => 'Micronesia',
            'NR' => 'Nauru',
            'NC' => 'New Caledonia',
            'NZ' => 'New Zealand',
            'NU' => 'Niue',
            'NF' => 'Norfolk Island',
            'MP' => 'Northern Mariana Islands',
            'PW' => 'Palau',
            'PG' => 'Papua New Guinea',
            'PN' => 'Pitcairn Islands',
            'WS' => 'Samoa',
            'SB' => 'Solomon Islands',
            'GS' => 'South Georgia and the South Sandwich Islands',
            'TK' => 'Tokelau',
            'TO' => 'Tonga',
            'TV' => 'Tuvalu',
            'UM' => 'U.S. Minor Outlying Islands',
            'VU' => 'Vanuatu',
            'WF' => 'Wallis and Futuna',
        ),
    );

    /**
     * @var integer The ID of the address 
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var string The street
     *
     * @Column(type="string")
     */
    private $street;

    /**
     * @var string The house number
     *
     * @Column(type="string")
     */
    private $number;
    
    /**
     * @var string The postal
     *
     * @Column(type="string")
     */
    private $postal;
    
    /**
     * @var string The city
     *
     * @Column(type="string")
     */
    private $city;
    
    /**
     * @var string The country
     *
     * @Column(type="string")
     */
    private $country;
    
    /**
     * @param string $street
     * @param string $number
     * @param string $postal
     * @param string $city
     * @param string $country
     */
    public function __construct($street, $number, $postal, $city, $country)
    {
        $this->setStreet($street)
            ->setNumber($number)
            ->setPostal($postal)
            ->setCity($city)
            ->setCountry($country);
    }
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }
    
    /**
     * @param string $street
     *
     * @return \CommonBundle\Entity\General\Address
     */
    public function setStreet($street)
    {
        $this->street = $street;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }
    
    /**
     * @param string $number
     *
     * @return \CommonBundle\Entity\General\Address
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getPostal()
    {
        return $this->postal;
    }
    
    /**
     * @param string $postal
     *
     * @return \CommonBundle\Entity\General\Address
     */
    public function setPostal($postal)
    {
        $this->postal = $postal;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }
    
    /**
     * @param string $city
     *
     * @return \CommonBundle\Entity\General\Address
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->country;
    }
    
    /**
     * @return string
     */
    public function getCountry()
    {
        foreach(self::$countries as $continent) {
            if (array_key_exists($this->country, $continent))
                return $continent[$this->country];
        }
        return '';
    }
    
    /**
     * @param string $number
     *
     * @return \CommonBundle\Entity\General\Address
     */
    public function setCountry($country)
    {
        if (self::isValidCountry($country))
            $this->country = $country;
        
        return $this;
    }
    
    /**
     * Checks whether the given status is valid.
     *
     * @param $status string A status
     * @return bool
     */
    public static function isValidCountry($country)
    {
        foreach(self::$countries as $continent) {
            if (array_key_exists($country, $continent))
                return true;
        }
        return false;
    }
}
