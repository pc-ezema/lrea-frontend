<?php 

if ($site_version == "online") {
	define("FORM_ENGINE", "https://portal.lrea.co.uk/landing-page-callback.php"); // Change this
}
else {
	define("FORM_ENGINE", "http://localhost/new_portal/landing-page-callback.php"); 
}

// This is used inside "inc.share-course-segment.php"
$this_programme_cant_be_added_to_cart = true;

$use_captcha_on_this_page = true;

// London, Dubai, Sharjah, Lagos, Abuja, Accra, Victoria Island, East Nigeria

if (in_array($coresite_curr_country_name, array('United Kingdom'))) {
	$to_email = INFO_SUPPORT_EMAIL;
	$branch = "London"; 
}
elseif (in_array($coresite_curr_state_name, array('Lagos')) || in_array($coresite_curr_country_name, array('Nigeria'))) {
	$to_email = INFO_SUPPORT_EMAIL;
	$branch = "Lagos";
}
elseif (in_array($coresite_curr_country_name, array('United Arab Emirates'))) {
	$to_email = INFO_SUPPORT_EMAIL;
	$branch = "Dubai";
}
elseif (in_array($coresite_curr_country_name, array('Pakistan'))) {
	$to_email = INFO_SUPPORT_EMAIL;
	$branch = "Islamabad";
}
else {
	$to_email = INFO_SUPPORT_EMAIL;
	$branch = "Online Course";
}

if ($_GET['request'] == 'received') {
	$form_completed = true;
}

if ($dis_programme_location) {
	if (preg_match('/pakistan/i', $dis_programme_location)) {
		$branch = "Pakistan";
	}
	elseif (preg_match('/nigeria/i', $dis_programme_location)) {
		$branch = "Lagos";
	}
	elseif (preg_match('/(london|united kingdom)/i', $dis_programme_location)) {
		$branch = "London";
	}
	elseif (preg_match('/(dubai|emirate)/i', $dis_programme_location)) {
		$branch = "Dubai";
	}

	$branch_determined_by_venue = true;
}

$position = array(
	array('option_value' => 'CEO/Executive Director'),
	array('option_value' => 'Executive'),
	array('option_value' => 'Middle Manager'),
	array('option_value' => 'Officer'),
	array('option_value' => 'Other...'),
	array('option_value' => 'Senior Manager')
);  

$source = array(
	array('option_value' => 'Admissions Office'),
	array('option_value' => 'Alumni'),
	array('option_value' => 'Billboard'),
	array('option_value' => 'Cold Call'),
	array('option_value' => 'Email Campaign'),
	array('option_value' => 'Flyers'),
	array('option_value' => 'Friend'),
	array('option_value' => 'Online - Facebook'),
	array('option_value' => 'Online - Google'),
	array('option_value' => 'Online - Instagram'),
	array('option_value' => 'Online - LinkedIn'),
	array('option_value' => 'Online - Twitter'),
	array('option_value' => 'Other'),
	array('option_value' => 'Radio'),
	array('option_value' => 'Signposts'),
	array('option_value' => 'SMS Campaign'),
	array('option_value' => 'TV'),
	array('option_value' => 'Word of Mouth')
);  

// Get country list
if ($database_config) {
	$loccountries = array();

	// Get the list of countries from the database
	mysqli_select_db($config, $database_config);
	$query_GetCountries4Form = "SELECT countryName 
					FROM loccountries 
					ORDER BY countryName ASC";
	$GetCountries4Form = mysqli_query($config, $query_GetCountries4Form) or die(mysqli_error($config));
	$row_GetCountries4Form = mysqli_fetch_assoc($GetCountries4Form);
	$totalRows_GetCountries4Form = mysqli_num_rows($GetCountries4Form);

	if ($totalRows_GetCountries4Form) {
		do {
			$loccountries[] = array('countryName' => $row_GetCountries4Form['countryName']);
		} while($row_GetCountries4Form = mysqli_fetch_assoc($GetCountries4Form));
	}
}

// Country list fallback
if (!is_array($loccountries) || !count($loccountries)) {
	$loccountries = array(
		array('countryName' => 'Afghanistan'),
		array('countryName' => 'Albania'),
		array('countryName' => 'Algeria'),
		array('countryName' => 'Andorra'),
		array('countryName' => 'Angola'),
		array('countryName' => 'Argentina'),
		array('countryName' => 'Armenia'),
		array('countryName' => 'Australia'),
		array('countryName' => 'Austria'),
		array('countryName' => 'Azerbaijan'),
		array('countryName' => 'Bahamas'),
		array('countryName' => 'Bahrain'),
		array('countryName' => 'Bangladesh'),
		array('countryName' => 'Barbados'),
		array('countryName' => 'Belarus'),
		array('countryName' => 'Belgium'),
		array('countryName' => 'Belize'),
		array('countryName' => 'Benin'),
		array('countryName' => 'Bermuda'),
		array('countryName' => 'Bhutan'),
		array('countryName' => 'Bolivia'),
		array('countryName' => 'Bosnia and Herzegovina'),
		array('countryName' => 'Botswana'),
		array('countryName' => 'Brazil'),
		array('countryName' => 'British Virgin Islands'),
		array('countryName' => 'Brunei'),
		array('countryName' => 'Bulgaria'),
		array('countryName' => 'Burkina Faso'),
		array('countryName' => 'Burundi'),
		array('countryName' => 'Cambodia'),
		array('countryName' => 'Cameroon'),
		array('countryName' => 'Canada'),
		array('countryName' => 'Cape Verde'),
		array('countryName' => 'Cayman Islands'),
		array('countryName' => 'Central African Republic'),
		array('countryName' => 'Chad'),
		array('countryName' => 'Chile'),
		array('countryName' => 'China'),
		array('countryName' => 'Colombia'),
		array('countryName' => 'Congo-Brazzaville'),
		array('countryName' => 'Congo-Kinshasa'),
		array('countryName' => 'Costa Rica'),
		array('countryName' => 'Croatia'),
		array('countryName' => 'Cuba'),
		array('countryName' => 'Cyprus'),
		array('countryName' => 'Czech Republic'),
		array('countryName' => 'Denmark'),
		array('countryName' => 'Djibouti'),
		array('countryName' => 'Dominica'),
		array('countryName' => 'Dominican Republic'),
		array('countryName' => 'East Timor'),
		array('countryName' => 'Ecuador'),
		array('countryName' => 'Egypt'),
		array('countryName' => 'El Salvador'),
		array('countryName' => 'Equatorial Guinea'),
		array('countryName' => 'Eritrea'),
		array('countryName' => 'Estonia'),
		array('countryName' => 'Ethiopia'),
		array('countryName' => 'Falkland Islands'),
		array('countryName' => 'Fiji'),
		array('countryName' => 'Finland'),
		array('countryName' => 'France'),
		array('countryName' => 'French Guiana'),
		array('countryName' => 'French Polynesia'),
		array('countryName' => 'Gabon'),
		array('countryName' => 'Gambia'),
		array('countryName' => 'Georgia'),
		array('countryName' => 'Germany'),
		array('countryName' => 'Ghana'),
		array('countryName' => 'Gibraltar'),
		array('countryName' => 'Greece'),
		array('countryName' => 'Greenland'),
		array('countryName' => 'Grenada'),
		array('countryName' => 'Guadeloupe'),
		array('countryName' => 'Guam'),
		array('countryName' => 'Guatemala'),
		array('countryName' => 'Guernsey'),
		array('countryName' => 'Guinea'),
		array('countryName' => 'Guinea-Bissau'),
		array('countryName' => 'Guyana'),
		array('countryName' => 'Haiti'),
		array('countryName' => 'Honduras'),
		array('countryName' => 'Hong Kong'),
		array('countryName' => 'Hungary'),
		array('countryName' => 'Iceland'),
		array('countryName' => 'India'),
		array('countryName' => 'Indonesia'),
		array('countryName' => 'Iran'),
		array('countryName' => 'Iraq'),
		array('countryName' => 'Ireland'),
		array('countryName' => 'Isle of Man'),
		array('countryName' => 'Israel'),
		array('countryName' => 'Italy'),
		array('countryName' => 'Ivory Coast'),
		array('countryName' => 'Jamaica'),
		array('countryName' => 'Japan'),
		array('countryName' => 'Jordan'),
		array('countryName' => 'Kazakhstan'),
		array('countryName' => 'Kenya'),
		array('countryName' => 'Kiribati'),
		array('countryName' => 'Kosovo'),
		array('countryName' => 'Kuwait'),
		array('countryName' => 'Kyrgyzstan'),
		array('countryName' => 'Laos'),
		array('countryName' => 'Latvia'),
		array('countryName' => 'Lebanon'),
		array('countryName' => 'Lesotho'),
		array('countryName' => 'Liberia'),
		array('countryName' => 'Libya'),
		array('countryName' => 'Liechtenstein'),
		array('countryName' => 'Lithuania'),
		array('countryName' => 'Luxembourg'),
		array('countryName' => 'Macau'),
		array('countryName' => 'Macedonia'),
		array('countryName' => 'Madagascar'),
		array('countryName' => 'Malawi'),
		array('countryName' => 'Malaysia'),
		array('countryName' => 'Maldives'),
		array('countryName' => 'Mali'),
		array('countryName' => 'Malta'),
		array('countryName' => 'Marshall Islands'),
		array('countryName' => 'Martinique'),
		array('countryName' => 'Mauritania'),
		array('countryName' => 'Mauritius'),
		array('countryName' => 'Mexico'),
		array('countryName' => 'Micronesia'),
		array('countryName' => 'Moldova'),
		array('countryName' => 'Monaco'),
		array('countryName' => 'Mongolia'),
		array('countryName' => 'Montenegro'),
		array('countryName' => 'Montserrat'),
		array('countryName' => 'Morocco'),
		array('countryName' => 'Mozambique'),
		array('countryName' => 'Myanmar (Burma)'),
		array('countryName' => 'Namibia'),
		array('countryName' => 'Nauru'),
		array('countryName' => 'Nepal'),
		array('countryName' => 'Netherlands'),
		array('countryName' => 'New Caledonia'),
		array('countryName' => 'New Zealand'),
		array('countryName' => 'Nicaragua'),
		array('countryName' => 'Niger'),
		array('countryName' => 'Nigeria'),
		array('countryName' => 'North Korea'),
		array('countryName' => 'Norway'),
		array('countryName' => 'Oman'),
		array('countryName' => 'Pakistan'),
		array('countryName' => 'Panama'),
		array('countryName' => 'Papua New Guinea'),
		array('countryName' => 'Paraguay'),
		array('countryName' => 'Peru'),
		array('countryName' => 'Philippines'),
		array('countryName' => 'Poland'),
		array('countryName' => 'Portugal'),
		array('countryName' => 'Puerto Rico'),
		array('countryName' => 'Qatar'),
		array('countryName' => 'Reunion'),
		array('countryName' => 'Romania'),
		array('countryName' => 'Russia'),
		array('countryName' => 'Rwanda'),
		array('countryName' => 'Samoa'),
		array('countryName' => 'San Marino'),
		array('countryName' => 'Sao Tome and Principe'),
		array('countryName' => 'Saudi Arabia'),
		array('countryName' => 'Senegal'),
		array('countryName' => 'Serbia'),
		array('countryName' => 'Seychelles'),
		array('countryName' => 'Sierra Leone'),
		array('countryName' => 'Singapore'),
		array('countryName' => 'Slovakia'),
		array('countryName' => 'Slovenia'),
		array('countryName' => 'Solomon Islands'),
		array('countryName' => 'Somalia'),
		array('countryName' => 'South Africa'),
		array('countryName' => 'South Korea'),
		array('countryName' => 'Spain'),
		array('countryName' => 'Sri Lanka'),
		array('countryName' => 'Sudan'),
		array('countryName' => 'Suriname'),
		array('countryName' => 'Swaziland'),
		array('countryName' => 'Sweden'),
		array('countryName' => 'Switzerland'),
		array('countryName' => 'Syria'),
		array('countryName' => 'Taiwan'),
		array('countryName' => 'Tajikistan'),
		array('countryName' => 'Tanzania'),
		array('countryName' => 'Thailand'),
		array('countryName' => 'Togo'),
		array('countryName' => 'Tonga'),
		array('countryName' => 'Trinidad and Tobago'),
		array('countryName' => 'Tunisia'),
		array('countryName' => 'Turkey'),
		array('countryName' => 'Turkmenistan'),
		array('countryName' => 'Tuvalu'),
		array('countryName' => 'Uganda'),
		array('countryName' => 'Ukraine'),
		array('countryName' => 'United Arab Emirates'),
		array('countryName' => 'United Kingdom'),
		array('countryName' => 'United States'),
		array('countryName' => 'United States Virgin Islands'),
		array('countryName' => 'Uruguay'),
		array('countryName' => 'Uzbekistan'),
		array('countryName' => 'Vanuatu'),
		array('countryName' => 'Vatican City'),
		array('countryName' => 'Venezuela'),
		array('countryName' => 'Vietnam'),
		array('countryName' => 'Wallis and Futuna'),
		array('countryName' => 'Western Sahara'),
		array('countryName' => 'Yemen'),
		array('countryName' => 'Zambia'),
		array('countryName' => 'Zimbabwe')
	);
}