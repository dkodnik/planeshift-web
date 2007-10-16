<?
/*
 * settings.php - Author: Christian Svensson
 *
 * Copyright (C) 2004 PlaneShift Team (info@planeshift.it,
 * http://www.planeshift.it)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation (version 2 of the License)
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * Description : This page contains the countries and stuff like that
 */


function PrintCountries()
{
?>
<option value="?">Not Given</option>
<option value ='USA'>United States</option>
<option value='GB'>United Kingdom</option>
<option value='CA'>Canada</option>
<option value='JP'>Japan</option>
<option value='AF'>Afghanistan</option>
<option value='AL'>Albania</option>
<option value='DZ'>Algeria</option>
<option value='AS'>American Samoa</option>
<option value='AD'>Andorra</option>
<option value='AO'>Angola</option>
<option value='AI'>Anguilla</option>
<option value='AQ'>Antarctica</option>
<option value='AG'>Antigua/Barbuda</option>
<option value='AR'>Argentina</option>
<option value='AM'>Armenia</option>
<option value='AW'>Aruba</option>
<option value='AU'>Australia</option>
<option value='AT'>Austria</option>
<option value='AZ'>Azerbaijan</option>
<option value='BS'>Bahamas</option>
<option value='BH'>Bahrain</option>
<option value='BD'>Bangladesh</option>
<option value='BB'>Barbados</option>
<option value='BY'>Belarus</option>
<option value='BE'>Belgium</option>
<option value='BZ'>Belize</option>
<option value='BJ'>Benin</option>
<option value='BM'>Bermuda</option>
<option value='BT'>Bhutan</option>
<option value='BO'>Bolivia</option>
<option value='BA'>Bosnia/Herzegovina</option>
<option value='BW'>Botswana</option>
<option value='BV'>Bouvet Island</option>
<option value='BR'>Brazil</option>
<option value='IO'>British Indian Ocean Territory</option>
<option value='BN'>Brunei Darussalam</option>
<option value='BG'>Bulgaria</option>
<option value='BF'>Burkina Faso</option>
<option value='BI'>Burundi</option>
<option value='KH'>Cambodia</option>
<option value='CM'>Cameroon</option>
<option value='CV'>Cape Verde</option>
<option value='KY'>Cayman Islands</option>
<option value='CF'>Central African Republic</option>
<option value='TD'>Chad</option>
<option value='GB1'>Channel Islands</option>
<option value='CL'>Chile</option>
<option value='CN'>China</option>
<option value='CX'>Christmas Island</option>
<option value='CC'>Cocos (Keeling) Islands</option>
<option value='CO'>Colombia</option>
<option value='KM'>Comoros</option>
<option value='CG'>Congo</option>
<option value='CK'>Cook Islands</option>
<option value='CR'>Costa Rica</option>
<option value='HR'>Croatia (Hrvatska)</option>
<option value='CU'>Cuba</option>
<option value='CY'>Cyprus</option>
<option value='CZ'>Czech Republic</option>
<option value='DK'>Denmark</option>
<option value='DJ'>Djibouti</option>
<option value='DM'>Dominica</option>
<option value='DO'>Dominican Republic</option>
<option value='TP'>East Timor</option>
<option value='EC'>Ecuador</option>
<option value='EG'>Egypt</option>
<option value='SV'>El Salvador</option>
<option value='GB2'>England</option>
<option value='GQ'>Equatorial Guinea</option>
<option value='ER'>Eritrea</option>
<option value='EE'>Estonia</option>
<option value='ET'>Ethiopia</option>
<option value='FK'>Falkland Islands (Malvinas)</option>
<option value='FO'>Faroe Islands</option>
<option value='FJ'>Fiji</option>
<option value='FI'>Finland</option>
<option value='FR'>France</option>
<option value='FX'>France, Metropolitan</option>
<option value='GF'>French Guiana</option>
<option value='PF'>French Polynesia</option>
<option value='TF'>French Southern Territories</option>
<option value='GA'>Gabon</option>
<option value='GM'>Gambia</option>
<option value='GE'>Georgia</option>
<option value='DE'>Germany</option>
<option value='GH'>Ghana</option>
<option value='GI'>Gibraltar</option>
<option value='GB3'>Great Britain</option>
<option value='GR'>Greece</option>
<option value='GL'>Greenland</option>
<option value='GD'>Grenada</option>
<option value='GP'>Guadeloupe</option>
<option value='GU'>Guam</option>
<option value='GT'>Guatemala</option>
<option value='GN'>Guinea</option>
<option value='GW'>Guinea-Bissau</option>
<option value='GY'>Guyana</option>
<option value='HT'>Haiti</option>
<option value='HM'>Heard/McDonald Islands</option>
<option value='HN'>Honduras</option>
<option value='HK'>Hong Kong</option>
<option value='HU'>Hungary</option>
<option value='IS'>Iceland</option>
<option value='IN'>India</option>
<option value='ID'>Indonesia</option>
<option value='IR'>Iran</option>
<option value='IQ'>Iraq</option>
<option value='IE'>Ireland</option>
<option value='GB4'>Isle of Man</option>
<option value='IL'>Israel</option>
<option value='IT'>Italy</option>
<option value='JM'>Jamaica</option>
<option value='JO'>Jordan</option>
<option value='KZ'>Kazakhstan</option>
<option value='KE'>Kenya</option>
<option value='KI'>Kiribati</option>
<option value='KP'>Korea (North)</option>
<option value='KR'>Korea (South)</option>
<option value='KW'>Kuwait</option>
<option value='KG'>Kyrgyzstan</option>
<option value='LA'>Laos</option>
<option value='LV'>Latvia</option>
<option value='LB'>Lebanon</option>
<option value='LS'>Saint Lucia</option>
<option value='LR'>Liberia</option>
<option value='LY'>Libya</option>
<option value='LI'>Liechtenstein</option>
<option value='LT'>Lithuania</option>
<option value='LU'>Luxembourg</option>
<option value='MO'>Macau</option>
<option value='MK'>Macedonia</option>
<option value='MG'>Madagascar</option>
<option value='MW'>Malawi</option>
<option value='MY'>Malaysia</option>
<option value='MV'>Maldives</option>
<option value='ML'>Mali</option>
<option value='MT'>Malta</option>
<option value='MH'>Marshall Islands</option>
<option value='MQ'>Martinique</option>
<option value='MR'>Mauritania</option>
<option value='MU'>Mauritius</option>
<option value='YT'>Mayotte</option>
<option value='MX'>Mexico</option>
<option value='FM'>Micronesia</option>
<option value='MD'>Moldova</option>
<option value='MC'>Monaco</option>
<option value='MN'>Mongolia</option>
<option value='MS'>Montserrat</option>
<option value='MA'>Morocco</option>
<option value='MZ'>Mozambique</option>
<option value='MM'>Myanmar</option>
<option value='NA'>Namibia</option>
<option value='NR'>Nauru</option>
<option value='NP'>Nepal</option>
<option value='NL'>Netherlands</option>
<option value='AN'>Netherlands Antilles</option>
<option value='NC'>New Caledonia</option>
<option value='NZ'>New Zealand</option>
<option value='NI'>Nicaragua</option>
<option value='NE'>Niger</option>
<option value='NG'>Nigeria</option>
<option value='NU'>Niue</option>
<option value='NF'>Norfolk Island</option>
<option value='GB5'>Northern Ireland</option>
<option value='MP'>Northern Mariana Islands</option>
<option value='NO'>Norway</option>
<option value='OM'>Oman</option>
<option value='PK'>Pakistan</option>
<option value='PW'>Palau</option>
<option value='PA'>Panama</option>
<option value='PG'>Papua New Guinea</option>
<option value='PY'>Paraguay</option>
<option value='PE'>Peru</option>
<option value='PH'>Philippines</option>
<option value='PN'>Pitcairn</option>
<option value='PL'>Poland</option>
<option value='PT'>Portugal</option>
<option value='PR'>Puerto Rico</option>
<option value='QA'>Qatar</option>
<option value='RE'>Reunion</option>
<option value='RO'>Romania</option>
<option value='RU'>Russian Federation</option>
<option value='RW'>Rwanda</option>
<option value='GS'>S. Georgia/S. Sandwich Isls.</option>
<option value='KN'>Saint Kitts/Nevis</option>
<option value='VC'>Saint Vincent/Grenadines</option>
<option value='SM'>San Marino</option>
<option value='ST'>Sao Tome/Principe</option>
<option value='SA'>Saudi Arabia</option>
<option value='GB6'>Scotland</option>
<option value='SN'>Senegal</option>
<option value='SC'>Seychelles</option>
<option value='SL'>Sierra Leone</option>
<option value='SG'>Singapore</option>
<option value='SK'>Slovak Republic</option>
<option value='SI'>Slovenia</option>
<option value='SB'>Solomon Islands</option>
<option value='SO'>Somalia</option>
<option value='ZA'>South Africa</option>
<option value='ES'>Spain</option>
<option value='LK'>Sri Lanka</option>
<option value='SH'>St. Helena</option>
<option value='PM'>St. Pierre/Miquelon</option>
<option value='SD'>Sudan</option>
<option value='SR'>Suriname</option>
<option value='SJ'>Svalbard/Jan Mayen Islands</option>
<option value='SZ'>Swaziland</option>
<option value='SE'>Sweden</option>
<option value='CH'>Switzerland</option>
<option value='SY'>Syria</option>
<option value='TW'>Taiwan</option>
<option value='TJ'>Tajikistan</option>
<option value='TZ'>Tanzania</option>
<option value='TH'>Thailand</option>
<option value='TG'>Togo</option>
<option value='TK'>Tokelau</option>
<option value='TO'>Tonga</option>
<option value='TT'>Trinidad/Tobago</option>
<option value='TN'>Tunisia</option>
<option value='TR'>Turkey</option>
<option value='TM'>Turkmenistan</option>
<option value='TC'>Turks/Caicos Islands</option>
<option value='TV'>Tuvalu</option>
<option value='UG'>Uganda</option>
<option value='UA'>Ukraine</option>
<option value='AE'>United Arab Emirates</option>
<option value='UY'>Uruguay</option>
<option value='UM'>US Minor Outlying Islands</option>
<option value='UZ'>Uzbekistan</option>
<option value='VU'>Vanuatu</option>
<option value='VA'>Vatican City State (Holy See)</option>
<option value='VE'>Venezuela</option>
<option value='VN'>Viet Nam</option>
<option value='VG'>Virgin Islands (British)</option>
<option value='VI'>Virgin Islands (U.S.)</option>
<option value='WK'>Wake Island</option>
<option value='GB7'>Wales</option>
<option value='WF'>Wallis/Futuna Islands</option>
<option value='EH'>Western Sahara</option>
<option value='WS'>Western Samoa</option>
<option value='YE'>Yemen</option>
<option value='YU'>Yugoslavia</option>
<option value='ZR'>Zaire</option>
<option value='ZM'>Zambia</option>
<option value='ZW'>Zimbabwe</option>
<?
}
?>