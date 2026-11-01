<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

?>
<h1>Search ICQ Database</h1>
<br />
<form name="Search_whitepages" action="http://www.icq.com/whitepages/search_result.php" method="get">
	<input type="hidden" name="search_type" value="" />

	First Name:<br />
	<input type="text" name="fname" maxlength="20" value="<?=$posted['first_name']?>" /><br />

	Last Name:<br />
	<input type="text" name="lname" maxlength="20" value="<?=$posted['last_name']?>" /><br />

	Nick Name:<br />
	<input type="text" name="nickname" maxlength="20" value="" /><br />

	Gender:<br />
	<select name="gender">
		<option value="" selected="selected"></option>
		<option value="2">Male</option>
		<option value="1">Female</option>
	</select><br />

	Age:<br />
	<select name="age_group">
		<option value="" selected="selected"></option>
		<option value="18-22">18-22</option>
		<option value="23-29">23-29</option>
		<option value="30-39">30-39</option>
		<option value="40-49">40-49</option>
		<option value="50-59">50-59</option>
		<option value="60+">60 and above</option>
	</select><br />

	Country:<br />
	<select name="home_country_code">
		<option value="">Choose Country</option>
		<option value="93">Afghanistan</option>
		<option value="355">Albania</option>
		<option value="213">Algeria</option>
		<option value="684">American Samoa</option>
		<option value="376">Andorra</option>
		<option value="244">Angola</option>
		<option value="101">Anguilla</option>
		<option value="102">Antigua</option>
		<option value="1021">Antigua &amp; Barbuda</option>
		<option value="5902">Antilles</option>
		<option value="54">Argentina</option>
		<option value="374">Armenia</option>
		<option value="297">Aruba</option>
		<option value="247">Ascension Island</option>
		<option value="61" selected="selected">Australia</option>
		<option value="43">Austria</option>
		<option value="994">Azerbaijan</option>
		<option value="103">Bahamas</option>
		<option value="973">Bahrain</option>
		<option value="880">Bangladesh</option>
		<option value="104">Barbados</option>
		<option value="120">Barbuda</option>
		<option value="375">Belarus</option>
		<option value="32">Belgium</option>
		<option value="501">Belize</option>
		<option value="229">Benin</option>
		<option value="105">Bermuda</option>
		<option value="975">Bhutan</option>
		<option value="591">Bolivia</option>
		<option value="387">Bosnia &amp; Herzegovina</option>
		<option value="267">Botswana</option>
		<option value="55">Brazil</option>
		<option value="106">British Virgin Islands</option>
		<option value="673">Brunei</option>
		<option value="359">Bulgaria</option>
		<option value="226">Burkina Faso</option>
		<option value="257">Burundi</option>
		<option value="855">Cambodia</option>
		<option value="237">Cameroon</option>
		<option value="107">Canada</option>
		<option value="178">Canary Islands</option>
		<option value="238">Cape Verde Islands</option>
		<option value="108">Cayman Islands</option>
		<option value="236">Central African Rep.</option>
		<option value="235">Chad</option>
		<option value="56">Chile, Rep. of</option>
		<option value="86">China</option>
		<option value="672">Christmas Island</option>
		<option value="6102">Cocos (Keeling) Islands</option>
		<option value="6101">Cocos-Keeling Islands</option>
		<option value="57">Colombia</option>
		<option value="2691">Comoros</option>
		<option value="243">Congo, Democratic Rep. of</option>
		<option value="242">Congo, Rep. of the</option>
		<option value="682">Cook Islands</option>
		<option value="506">Costa Rica</option>
		<option value="225">Cote d'Ivoire (Ivory Coast)</option>
		<option value="385">Croatia</option>
		<option value="53">Cuba</option>
		<option value="357">Cyprus</option>
		<option value="42">Czech Rep.</option>
		<option value="45">Denmark</option>
		<option value="246">Diego Garcia</option>
		<option value="253">Djibouti</option>
		<option value="109">Dominica</option>
		<option value="110">Dominican Rep.</option>
		<option value="593">Ecuador</option>
		<option value="20">Egypt</option>
		<option value="503">El Salvador</option>
		<option value="240">Equatorial Guinea</option>
		<option value="291">Eritrea</option>
		<option value="372">Estonia</option>
		<option value="251">Ethiopia</option>
		<option value="298">Faeroe Islands</option>
		<option value="500">Falkland Islands</option>
		<option value="679">Fiji</option>
		<option value="358">Finland</option>
		<option value="33">France</option>
		<option value="5901">French Antilles</option>
		<option value="594">French Guiana</option>
		<option value="689">French Polynesia</option>
		<option value="241">Gabon</option>
		<option value="220">Gambia</option>
		<option value="995">Georgia</option>
		<option value="49">Germany</option>
		<option value="233">Ghana</option>
		<option value="350">Gibraltar</option>
		<option value="30">Greece</option>
		<option value="299">Greenland</option>
		<option value="111">Grenada</option>
		<option value="590">Guadeloupe</option>
		<option value="671">Guam, US Territory of</option>
		<option value="502">Guatemala</option>
		<option value="224">Guinea</option>
		<option value="245">Guinea-Bissau</option>
		<option value="592">Guyana</option>
		<option value="509">Haiti</option>
		<option value="504">Honduras</option>
		<option value="852">Hong Kong</option>
		<option value="36">Hungary</option>
		<option value="354">Iceland</option>
		<option value="91">India</option>
		<option value="62">Indonesia</option>
		<option value="98">Iran, Islamic Rep. of</option>
		<option value="964">Iraq</option>
		<option value="353">Ireland</option>
		<option value="972">Israel</option>
		<option value="39">Italy</option>
		<option value="112">Jamaica</option>
		<option value="81">Japan</option>
		<option value="962">Jordan</option>
		<option value="705">Kazakhstan</option>
		<option value="254">Kenya</option>
		<option value="686">Kiribati</option>
		<option value="850">Korea, North</option>
		<option value="82">Korea, South</option>
		<option value="965">Kuwait</option>
		<option value="706">Kyrgyzstan</option>
		<option value="856">Laos</option>
		<option value="371">Latvia</option>
		<option value="961">Lebanon</option>
		<option value="266">Lesotho</option>
		<option value="231">Liberia</option>
		<option value="218">Libyan Arab Jamahiriya</option>
		<option value="4101">Liechtenstein</option>
		<option value="370">Lithuania</option>
		<option value="352">Luxembourg</option>
		<option value="853">Macau</option>
		<option value="389">Macedonia</option>
		<option value="261">Madagascar</option>
		<option value="265">Malawi</option>
		<option value="60">Malaysia</option>
		<option value="960">Maldives</option>
		<option value="223">Mali</option>
		<option value="356">Malta</option>
		<option value="692">Marshall Islands</option>
		<option value="596">Martinique</option>
		<option value="222">Mauritania</option>
		<option value="230">Mauritius</option>
		<option value="269">Mayotte Island</option>
		<option value="52">Mexico</option>
		<option value="691">Micronesia, Federated States of</option>
		<option value="373">Moldova, Rep. of</option>
		<option value="377">Monaco</option>
		<option value="976">Mongolia</option>
		<option value="113">Montserrat</option>
		<option value="212">Morocco</option>
		<option value="258">Mozambique</option>
		<option value="95">Myanmar</option>
		<option value="264">Namibia</option>
		<option value="674">Nauru</option>
		<option value="977">Nepal</option>
		<option value="31">Netherlands</option>
		<option value="599">Netherlands Antilles</option>
		<option value="114">Nevis</option>
		<option value="687">New Caledonia</option>
		<option value="64">New Zealand</option>
		<option value="505">Nicaragua</option>
		<option value="227">Niger</option>
		<option value="234">Nigeria</option>
		<option value="683">Niue</option>
		<option value="6722">Norfolk Island</option>
		<option value="47">Norway</option>
		<option value="968">Oman</option>
		<option value="9999">Other</option>
		<option value="92">Pakistan</option>
		<option value="680">Palau</option>
		<option value="507">Panama</option>
		<option value="675">Papua New Guinea</option>
		<option value="595">Paraguay</option>
		<option value="51">Peru</option>
		<option value="63">Philippines</option>
		<option value="48">Poland</option>
		<option value="351">Portugal</option>
		<option value="121">Puerto Rico</option>
		<option value="974">Qatar</option>
		<option value="262">Reunion Island</option>
		<option value="40">Romania</option>
		<option value="6701">Rota Island</option>
		<option value="7">Russia</option>
		<option value="250">Rwanda</option>
		<option value="122">Saint Lucia</option>
		<option value="670">Saipan Island</option>
		<option value="378">San Marino</option>
		<option value="239">Sao Tome &amp; Principe</option>
		<option value="966">Saudi Arabia</option>
		<option value="442">Scotland</option>
		<option value="221">Senegal</option>
		<option value="248">Seychelles</option>
		<option value="232">Sierra Leone</option>
		<option value="65">Singapore</option>
		<option value="4201">Slovakia</option>
		<option value="386">Slovenia</option>
		<option value="677">Solomon Islands</option>
		<option value="252">Somalia</option>
		<option value="27">South Africa</option>
		<option value="34">Spain</option>
		<option value="94">Sri Lanka</option>
		<option value="290">St. Helena</option>
		<option value="115">St. Kitts</option>
		<option value="1141">St. Kitts &amp; Nevis</option>
		<option value="508">St. Pierre &amp; Miquelon</option>
		<option value="116">St. Vincent &amp; the Grenadines</option>
		<option value="249">Sudan</option>
		<option value="597">Suriname</option>
		<option value="268">Swaziland</option>
		<option value="46">Sweden</option>
		<option value="41">Switzerland</option>
		<option value="963">Syrian Arab Rep.</option>
		<option value="886">Taiwan</option>
		<option value="708">Tajikistan</option>
		<option value="255">Tanzania</option>
		<option value="66">Thailand</option>
		<option value="6702">Tinian Island</option>
		<option value="228">Togo</option>
		<option value="690">Tokelau</option>
		<option value="676">Tonga</option>
		<option value="117">Trinidad &amp; Tobago</option>
		<option value="216">Tunisia</option>
		<option value="90">Turkey</option>
		<option value="709">Turkmenistan</option>
		<option value="118">Turks &amp; Caicos Islands</option>
		<option value="688">Tuvalu</option>
		<option value="1">USA</option>
		<option value="256">Uganda</option>
		<option value="380">Ukraine</option>
		<option value="971">United Arab Emirates</option>
		<option value="44">United Kingdom</option>
		<option value="598">Uruguay</option>
		<option value="711">Uzbekistan</option>
		<option value="678">Vanuatu</option>
		<option value="379">Vatican City</option>
		<option value="58">Venezuela</option>
		<option value="84">Viet Nam</option>
		<option value="123">Virgin Islands (USA)</option>
		<option value="441">Wales</option>
		<option value="681">Wallis &amp; Futuna Islands</option>
		<option value="685">Western Samoa</option>
		<option value="967">Yemen</option>
		<option value="381">Yugoslavia</option>
		<option value="382">Yugoslavia - Montenegro</option>
		<option value="3811">Yugoslavia - Serbia</option>
		<option value="260">Zambia</option>
		<option value="263">Zimbabwe</option>
	</select><br />

	E-Mail Address:<br />
	<input type="text" name="email" maxlength="60" value="<?=$posted['email']?>" /><br />

	<input type="checkbox" name="online" />Only Online Users&nbsp;&nbsp;&nbsp;<br />
	<input type="checkbox" name="photo" value="1" />Users With Photos First<br />
	<input type="submit" value="Search Now!" />
</form>
