<?php
global $DB;

$id   = intval($_GET['id']);
$item = $DB->query_first("SELECT * FROM `product_types` WHERE `id` = " . intval($id) . " LIMIT 1");
$url_data = '/buy/';
if($item['parent'] > 0){
	$Parendata = $DB->query_first("SELECT * FROM `product_types` WHERE `id` = " . intval($item['parent']) . " LIMIT 1");
	$url_data = $url_data.$Parendata['slug'].'/';
}
$url_data = $url_data.$item['slug'];
?>
<script>
	$(function(){
		var stop_words = ["a", "a's", "able", "about", "above", "abroad", "according", "accordingly", "across", "actually", "adj", "after", "afterwards", "again", "against", "ago", "ahead", "ain't", "all", "allow", "allows", "almost", "alone", "along", "alongside", "already", "also", "although", "always", "am", "amid", "amidst", "among", "amongst", "an", "and", "another", "any", "anybody", "anyhow", "anyone", "anything", "anyway", "anyways", "anywhere", "apart", "appear", "appreciate", "appropriate", "are", "aren't", "around", "as", "aside", "ask", "asking", "associated", "at", "available", "away", "awfully", "b", "back", "backward", "backwards", "be", "became", "because", "become", "becomes", "becoming", "been", "before", "beforehand", "begin", "behind", "being", "believe", "below", "beside", "besides", "best", "better", "between", "beyond", "both", "brief", "but", "by", "c", "c'mon", "c's", "came", "can", "can't", "cannot", "cant", "caption", "cause", "causes", "certain", "certainly", "changes", "clearly", "co", "co.", "com", "come", "comes", "concerning", "consequently", "consider", "considering", "contain", "containing", "contains", "corresponding", "could", "couldn't", "course", "currently", "d", "dare", "daren't", "definitely", "described", "despite", "did", "didn't", "different", "directly", "do", "does", "doesn't", "doing", "don't", "done", "down", "downwards", "during", "e", "each", "edu", "eg", "eight", "eighty", "either", "else", "elsewhere", "end", "ending", "enough", "entirely", "especially", "et", "etc", "even", "ever", "evermore", "every", "everybody", "everyone", "everything", "everywhere", "ex", "exactly", "example", "except", "f", "fairly", "far", "farther", "few", "fewer", "fifth", "first", "five", "followed", "following", "follows", "for", "forever", "former", "formerly", "forth", "forward", "found", "four", "from", "further", "furthermore", "g", "get", "gets", "getting", "given", "gives", "go", "goes", "going", "gone", "got", "gotten", "greetings", "h", "had", "hadn't", "half", "happens", "hardly", "has", "hasn't", "have", "haven't", "having", "he", "he'd", "he'll", "he's", "hello", "help", "hence", "her", "here", "here's", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "hi", "him", "himself", "his", "hither", "hopefully", "how", "howbeit", "however", "hundred", "i", "i'd", "i'll", "i'm", "i've", "ie", "if", "ignored", "immediate", "in", "inasmuch", "inc", "inc.", "indeed", "indicate", "indicated", "indicates", "inner", "inside", "insofar", "instead", "into", "inward", "is", "isn't", "it", "it'd", "it'll", "it's", "its", "itself", "j", "just", "k", "keep", "keeps", "kept", "know", "known", "knows", "l", "last", "lately", "later", "latter", "latterly", "least", "less", "lest", "let", "let's", "like", "liked", "likely", "likewise", "little", "look", "looking", "looks", "low", "lower", "ltd", "m", "made", "mainly", "make", "makes", "many", "may", "maybe", "mayn't", "me", "mean", "meantime", "meanwhile", "merely", "might", "mightn't", "mine", "minus", "miss", "more", "moreover", "most", "mostly", "mr", "mrs", "much", "must", "mustn't", "my", "myself", "n", "name", "namely", "nd", "near", "nearly", "necessary", "need", "needn't", "needs", "neither", "never", "neverf", "neverless", "nevertheless", "new", "next", "nine", "ninety", "no", "no-one", "nobody", "non", "none", "nonetheless", "noone", "nor", "normally", "not", "nothing", "notwithstanding", "novel", "now", "nowhere", "o", "obviously", "of", "off", "often", "oh", "ok", "okay", "old", "on", "once", "one", "one's", "ones", "only", "onto", "opposite", "or", "other", "others", "otherwise", "ought", "oughtn't", "our", "ours", "ourselves", "out", "outside", "over", "overall", "own", "p", "particular", "particularly", "past", "per", "perhaps", "placed", "please", "plus", "possible", "presumably", "probably", "provided", "provides", "q", "que", "quite", "qv", "r", "rather", "rd", "re", "really", "reasonably", "recent", "recently", "regarding", "regardless", "regards", "relatively", "respectively", "right", "round", "s", "said", "same", "saw", "say", "saying", "says", "second", "secondly", "see", "seeing", "seem", "seemed", "seeming", "seems", "seen", "self", "selves", "sensible", "sent", "serious", "seriously", "seven", "several", "shall", "shan't", "she", "she'd", "she'll", "she's", "should", "shouldn't", "since", "six", "so", "some", "somebody", "someday", "somehow", "someone", "something", "sometime", "sometimes", "somewhat", "somewhere", "soon", "sorry", "specified", "specify", "specifying", "still", "sub", "such", "sup", "sure", "t", "t's", "take", "taken", "taking", "tell", "tends", "th", "than", "thank", "thanks", "thanx", "that", "that'll", "that's", "that've", "thats", "the", "their", "theirs", "them", "themselves", "then", "thence", "there", "there'd", "there'll", "there're", "there's", "there've", "thereafter", "thereby", "therefore", "therein", "theres", "thereupon", "these", "they", "they'd", "they'll", "they're", "they've", "thing", "things", "think", "third", "thirty", "this", "thorough", "thoroughly", "those", "though", "three", "through", "throughout", "thru", "thus", "till", "to", "together", "too", "took", "toward", "towards", "tried", "tries", "truly", "try", "trying", "twice", "two", "u", "un", "under", "underneath", "undoing", "unfortunately", "unless", "unlike", "unlikely", "until", "unto", "up", "upon", "upwards", "us", "use", "used", "useful", "uses", "using", "usually", "v", "value", "various", "versus", "very", "via", "viz", "vs", "w", "want", "wants", "was", "wasn't", "way", "we", "we'd", "we'll", "we're", "we've", "welcome", "well", "went", "were", "weren't", "what", "what'll", "what's", "what've", "whatever", "when", "whence", "whenever", "where", "where's", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "whichever", "while", "whilst", "whither", "who", "who'd", "who'll", "who's", "whoever", "whole", "whom", "whomever", "whose", "why", "will", "willing", "wish", "with", "within", "without", "won't", "wonder", "would", "wouldn't", "x", "y", "yes", "yet", "you", "you'd", "you'll", "you're", "you've", "your", "yours", "yourself", "yourselves", "z", "zero"];

		
		function seo_find_keywords() {
			var title = $("[name='data[name]']").val();
			var keywords = $("[name='data[meta_keywords]']").val();
			var description = $("[name='data[description]']").val();
			var all = (title + "\n" + keywords + "\n" + description).split(/\b\W+\b/);
			title = title.toLowerCase().split(/\b\W+\b/).join(" ");
			keywords = keywords.toLowerCase().split(/\b\W+\b/).join(" ");
			description = description.toLowerCase().split(/\b\W+\b/).join(" ");
			var phrases = {};
			var w1 = "";
			var w2 = "";
			var w3 = "";
			var locations = {title: {}, keywords: {}, description: {}}; // track locations
			for (var i = 0; i < all.length; i++) if (all[i].length > 1 && !is_numeric(all[i]) && !in_array(all[i].toLowerCase(), stop_words)) {
				w1 = w2;
				w2 = w3;
				w3 = all[i];
				var k3u = w3;
				var k2u = w2 + " " + w3;
				var k1u = w1 + " " + w2 + " " + w3;
				var k3l = k3u.toLowerCase();
				var k2l = k2u.toLowerCase();
				var k1l = k1u.toLowerCase();
				if (w3 != "") {
					if (!phrases[k3l]) phrases[k3l] = {};
					phrases[k3l][k3u] = (phrases[k3l][k3u]) ? (phrases[k3l][k3u] + 1) : 1;
					if (title.includes(k3l)) locations.title[k3l] = true;
					if (keywords.includes(k3l)) locations.keywords[k3l] = true;
					if (description.includes(k3l)) locations.description[k3l] = true;
					if (w2 != "") {
						if (!phrases[k2l]) phrases[k2l] = {};
						phrases[k2l][k2u] = (phrases[k2l][k2u]) ? (phrases[k2l][k2u] + 1) : 1;
						if (title.includes(k2l)) locations.title[k2l] = true;
						if (keywords.includes(k2l)) locations.keywords[k2l] = true;
						if (description.includes(k2l)) locations.description[k2l] = true;
						if (w1 != "") {
							if (!phrases[k1l]) phrases[k1l] = {};
							phrases[k1l][k1u] = (phrases[k1l][k1u]) ? (phrases[k1l][k1u] + 1) : 1;
							if (title.includes(k1l)) locations.title[k1l] = true;
							if (keywords.includes(k1l)) locations.keywords[k1l] = true;
							if (description.includes(k1l)) locations.description[k1l] = true;
						}
					}
				}
			} else w1 = w2 = w3 = "";
			var results = [];
			var all_length = all.join(" ").length;
			for (var i in phrases) {
				var best_case = i;
				var best_case_score = 0;
				for (var j in phrases[i]) if (phrases[i][j] > best_case_score) {
					best_case = j;
					best_case_score = phrases[i][j];
				}
				var count_all = best_case_score;
				var prominence = (best_case_score / all.length) * 100; // calculate prominence within the entire text
				var found_in = [];
				if (locations.title[i]) found_in.push("title");
				if (locations.keywords[i]) found_in.push("keywords");
				if (locations.description[i]) found_in.push("description");
				results.push([
					best_case,
					count_all,
					(all_length != 0) ? sprintf("%0.2f%%", count_all * best_case.length * 100.0 / all_length) : "-",
					prominence.toFixed(2) + "%",
					found_in.join(", ")
				]);
			}
			results.sort(function (a, b) {
				return (b[1] - a[1]); // Sort by repeats in descending order
			});
			return results;
		}

		$("[name='data[name]'], [name='data[meta_keywords]'], [name='data[description]']").change(function() {
			var results = seo_find_keywords();
			console.log(results);
			var $table = $("#seo_phrase_density");
			$("td", $table).closest("tr").detach();
			var alt = false;
			var count = 0;
			for (var i = 0; i < results.length; i++) {
				if (count < 20 || results[i][1] > 0) { // Only show the top 20 or those with non-zero repeats
					var $row = $("<tr></tr>");
					if (alt = !alt) $row.addClass("alt");
					count++;
					$row.append($("<td style='white-space:nowrap;'></td>").text(results[i][0]));
					$row.append($("<td style='text-align:right;'></td>").text(results[i][1]));
					$row.append($("<td style='text-align:right;'></td>").text(results[i][2]));
					$row.append($("<td style='text-align:right;'></td>").text(results[i][3]));
					$row.append($("<td style='text-align:right;'></td>").text(results[i][4]));
					$table.append($row);
				}
			}
		});

	});

function updateTags()
{
    setValue("data[meta_title]", getValue("data[name]"));
    setValue("data[meta_keywords]", formatMETA(getValue("data[name]") + ", " + getValue("data[description]"), "keyword"));
    setValue("data[meta_description]", getValue("data[description]"));
    setValue("data[meta_keywords]", getValue("data[name]").split(/[\x00-\x20\x80-\xff]+/).join("-"));
}
function clearTags()
{
    setValue("data[meta_title]", "");
    setValue("data[meta_keywords]", "");
    setValue("data[meta_description]", "");
    setValue("data[meta_keywords]", "");
}
function updateTotalsNew()
{
	var title=getValue('data[name]');
	setValue('meta_title_len',title.length);

	var keys=0;
	var keywords=getValue('data[meta_keywords]').split(',');
	for(i=0;i<keywords.length;i++) if(keywords[i]!='') keys++;
	setValue('meta_keys_count',keys);

	var desc=getValue('data[description]');
	setValue('meta_desc_len',desc.length);
}
</script>
<center>
	<?php
		if(isset($_GET['message']) && $_GET['message'] != ''){
			echo '<p style="color:red;">'.$_GET['message'].'<p>';
		}
	?>
	<table border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td nowrap="nowrap" class="bottom">&nbsp;</td>
		<td class="bottom">&nbsp;</td>
	</tr>
	<tr>
		<td class="left" style="width: 25%;"><b>Parent Type:</b></td>
		<td class="right" style="width: 50%;" colspan="2"><select name="data[parent]" style="width:67%">
			<option value="0">--- None --</option>
<?php
show_sections_options(0, '&nbsp;&nbsp;&nbsp;', $item['parent'], $id, '');
?>
		</select></td>
	</tr>
	<tr>
		<td class="left" style="width: 25%;"><a href="<?=$url_data ?>" style="font-style:italic;text-decoration:underline;margin-right:10px;" target="_blank">(Live)</a><b>New Type:</b></td>
		<td class="right" style="width: 50%;"><input type="text" name="data[name]" size="20" style="width: 100%;" value="<?= htmlspecialchars($item['name'])?>" onchange="updateTotalsNew();"/></td>
		<td  style="width: 25%;background-color: #DFE1F7;">
			<div style="display:flex;">
				<div name="meta_title_len"><?= strlen($item['name'])?> </div> <div style="text-align: right;margin-right: 5px;width: 90%;">Page Name & H1 Metatag</div>
			</div>
		</td>

	</tr>
	<tr>
		<td class="left" style="width: 25%;"><b>Description:</b></td>
		<td class="right" style="width: 50%;"><textarea name="data[description]" cols="20" rows="3" style="width: 100%;" onchange="updateTotalsNew();"><?= htmlspecialchars($item['description'])?></textarea></td>
		<td  style="width: 25%;background-color: #DFE1F7;">
			<div style="display:flex;">
				<div name="meta_desc_len"><?= strlen($item['description'])?> </div> <div style="text-align: right;margin-right: 5px;width: 90%;">Metatag Description</div>
			</div>
		</td>
	</tr>
	<tr>
		<td class="left" style="width: 25%;" style="width: 25%;"><b>Url Keywords:</b></td>
		<td class="right" style="width: 50%;"><input type="text" name="data[meta_keywords]" size="20" style="width: 100%;" value="<?php echo ($item['meta_keywords'])?$item['meta_keywords']:$item['meta_keywords']; ?>" onchange="updateTotalsNew();"/></td>
		<td style="width: 25%;background-color: #DFE1F7;">
			<div style="display:flex;">
				<div name="meta_keys_count"><?= count(explode(',',$item['meta_keywords']))?> </div> <div style="text-align: right;margin-right: 5px;width: 90%;">Metatag Keywords</div>
			</div>
		</td>
	</tr>
	<tr>
		<td class="left" style="width: 25%;"><b>Url Slug:</b></td>
		<td class="right" style="width: 50%;" colspan="2"><input type="text" name="data[slug]"  size="20" style="width:67%" value="<?= htmlspecialchars($item['slug'])?>" required></td>
	</tr>
	
	<!-- Updated on 12th June -->
	<!-- <tr><td colspan="2" nowrap="nowrap" class="centre"><b>Metatags</b></td></tr>
	<tr>
		<td class="left" style="width: 25%;"><b>Title:</b></td>
		<td class="right" style="width: 50%;"><input type="text" name="data[meta_title]" size="20" style="width:67%" value="<?php echo ($item['meta_title'])?$item['meta_title']:$item['name']; ?>" /></td>
	</tr>
	<tr>
		<td class="left" style="width: 25%;"><b>Keywords:</b></td>
		<td class="right" style="width: 50%;"><input type="text" name="data[meta_keywords]" size="20" style="width:67%" value="<?php echo ($item['meta_keywords'])?$item['meta_keywords']:$item['description']; ?>" /></td>
	</tr>
	<tr>
		<td class="left" style="width: 25%;"><b>Meta Description:</b></td>
		<td class="right" style="width: 50%;"><textarea name="data[meta_description]" cols="20" rows="3" style="width:67%"><?php echo ($item['meta_description'])?$item['meta_description']:$item['description']; ?></textarea></td>
	</tr>
	<tr>
		<td class="left" style="width: 25%;"><b>Url Keywords:</b></td>
		<td class="right" style="width: 50%;"><input type="text" name="data[meta_keywords]" size="20" style="width:67%" value="<?php echo ($item['meta_keywords'])?$item['meta_keywords']:$item['meta_keywords']; ?>" /></td>
	</tr> -->
	<!-- <tr>
		<td class="left" style="width: 25%;"><b>META Tags Last Changed :</b></td>
		<td class="right" style="width: 50%;"><span style="width:67%padding-left:3px;background:#FFF;display: block;border: 1px solid;"><?=(!is_null($item['meta_at'])) ? (date("d/m/Y", strtotime($item['meta_at'])) . ', By : ' . $item['staff']) : 'Never'?></span></td>
	</tr> -->
	<!-- end -->
	<!-- Under development Kelvin -->
	<!-- <tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr> -->
	<!-- <tr><td colspan="2" nowrap="nowrap" class="centre"><b>Metatags (Under development ::ignore::)</b></td></tr> -->
	<!-- <tr>
		<td class="left" style="width: 25%;"><b>Title:</b></td>
		<td class="right" style="width: 50%;"><input type="text" name="data[name]" size="20" style="width:67%" value="<?= htmlspecialchars($item['name'])?>" /></td>
	</tr>
	<tr>
		<td class="left" style="width: 25%;"><b>H1:</b></td>
		<td class="right" style="width: 50%;"><input type="text" name="data[name]" size="20" style="width:67%" value="<?= htmlspecialchars($item['name'])?>" /></td>
	</tr>
	<tr>
		<td class="left" style="width: 25%;"><b>Meta Description:</b></td>
		<td class="right" style="width: 50%;"><textarea name="data[description]" cols="20" rows="3" style="width:67%"><?= htmlspecialchars($item['description'])?></textarea></td>
	</tr> -->
	<!-- Under development Kelvin -->

	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
	<?php 
		if($id > 0){
			include "meta_tags_list.tpl.php";
		}
	?>
	<tr>
		<td class="left" style="width: 25%;"><b>Data Directory:</b></td>
		<td class="right" style="width: 50%;" colspan="2" colspan="2"><input type="text" name="data[img_location]" size="20" style="width:67%" value="<?= htmlspecialchars($item['img_location'])?>" /></td>
	</tr>
	<tr>
		<td class="left" style="width: 25%;"><b>Current Header:</b></td>
		<td class="right" style="width: 50%;" colspan="2"><input type="text" size="20" style="width:67%" value="<?= htmlspecialchars($item['header_image'])?>" disabled="disabled" /></td>
	</tr>
	<tr>
		<td class="left" style="width: 25%;"><b>Header Image:</b></td>
		<td class="right" style="width: 50%;" colspan="2">
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input type="file" name="images[header_image]" size="20" style="width:67%" />
		</td>
	</tr>
	<tr>
		<td class="left" style="width: 25%;"><b>Current Icon:</b></td>
		<td class="right" style="width: 50%;" colspan="2"><input type="text" size="20" style="width:67%" value="<?= htmlspecialchars($item['icon_image'])?>" disabled="disabled" /></td>
	</tr>
	<tr>
		<td class="left" style="width: 25%;"><b>Icon Image:</b></td>
		<td class="right" style="width: 50%;" colspan="2">
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input type="file" name="images[icon_image]" size="20" style="width:67%" />
		</td>
	</tr>
	<tr><td colspan="2" nowrap="nowrap" class="centre">&nbsp;</td></tr>
	<tr>
		<td class="left" style="width: 25%;"><b>Ordering:</b></td>
		<td class="right" style="width: 50%;" colspan="2"><input type="text" name="data[ordering]" size="20" style="width:100px;" value="<?= htmlspecialchars($item['ordering'])?>" /></td>
	</tr>
	<tr>
		<td class="left" style="width: 25%;"><b>Active:</b></td>
		<td class="right" style="width: 50%;" colspan="2"><select name="data[active]" style="width:100px;">
			<option value="y"<?php echo ('y' == $item['active']) ? ' selected="selected"' : '' ?>>Yes</option>
			<option value="n"<?php echo ('n' == $item['active']) ? ' selected="selected"' : '' ?>>No</option>
		</select></td>
	</tr>
	<tr><td colspan="2" class="top">&nbsp;</td></tr>
	<tr>
		<td class="left" style="width: 25%;"><b>Special Notes:</b></td>
		<td class="right" style="width: 50%;" colspan="2"><textarea name="data[generic_notes]" cols="20" rows="10" style="width:67%"><?= htmlspecialchars($item['generic_notes'])?></textarea></td>
	</tr>
		<!-- Under development Kelvin -->
	<!-- <tr><td colspan="2" nowrap="nowrap" class="centre"><b>Metatags</b></td></tr>
	<tr>
		<td class="left" style="width: 25%;"><b>Title:</b></td>
		<td class="right" style="width: 50%;"><input type="text" name="data[meta_title]" size="20" style="width:67%" value="<?php echo ($item['meta_title'])?$item['meta_title']:$item['name']; ?>" /></td>
	</tr>
	<tr>
		<td class="left" style="width: 25%;"><b>Keywords:</b></td>
		<td class="right" style="width: 50%;"><input type="text" name="data[meta_keywords]" size="20" style="width:67%" value="<?php echo ($item['meta_keywords'])?$item['meta_keywords']:$item['description']; ?>" /></td>
	</tr>
	<tr>
		<td class="left" style="width: 25%;"><b>Meta Description:</b></td>
		<td class="right" style="width: 50%;"><textarea name="data[meta_description]" cols="20" rows="3" style="width:67%"><?php echo ($item['meta_description'])?$item['meta_description']:$item['description']; ?></textarea></td>
	</tr>
	<tr>
		<td class="left" style="width: 25%;"><b>Url Keywords:</b></td>
		<td class="right" style="width: 50%;"><input type="text" name="data[meta_keywords]" size="20" style="width:67%" value="<?php echo ($item['meta_keywords'])?$item['meta_keywords']:$item['meta_keywords']; ?>" /></td>
	</tr>
	<tr>
		<td class="left" style="width: 25%;"><b>META Tags Last Changed :</b></td>
		<td class="right" style="width: 50%;"><span style="width:67%padding-left:3px;background:#FFF;display: block;border: 1px solid;"><?=(!is_null($item['meta_at'])) ? (date("d/m/Y", strtotime($item['meta_at'])) . ', By : ' . $item['staff']) : 'Never'?></span></td>
	</tr> -->
	<!-- <tr><td colspan="2" class="top">
	<br />
    <div style="margin:auto;text-align:center;">
        <input type="button" value="Auto-Update Tags" onclick="updateTags()" />
        <input type="button" value="Clear Tags" onclick="clearTags()" />
    </div>
	</td></tr> -->
	<!-- Under development Kelvin -->
	<tr><td colspan="2" class="top">&nbsp;</td></tr>
</table></center>
<br />
<br />

<script type="text/javascript">
//<![CDATA[
function updateTotals() {
    var title = getValue('data[meta_title]');
    setValue('meta_title', title.length);

    var keys = 0;
    var keywords = getValue('data[meta_keywords]').split(',');
    for (i = 0; i < keywords.length; i++) if (keywords[i] != '') keys++;
    setValue('meta_keywords', keys);

    var desc = getValue('data[meta_description]');
    setValue('meta_description', desc.length);
}
function updateTags() {
    setValue('data[meta_title]', getValue('data[name]'));
    setValue('data[meta_keywords]', formatMETA(getValue('data[name]') + ', ' + getValue('data[description]'), 'keyword'));
    setValue('data[meta_description]', getValue('data[description]'));
    updateTotals();
}
function clearTags() {
    setValue('data[meta_title]', '');
    setValue('data[meta_keywords]', '');
    setValue('data[meta_description]', '');
    updateTotals();
}
//]]>
</script>

<!-- <div class="flagHeader"><span >META Tags</span></div><br />
<table class="table_fields">
	<tr>
		<th><a href="#" onclick="open_win('templates/help.php?action=title');return false" style="color:blue">Title :</a></th>
		<td><input type="text" name="data[meta_title]" style="width:67%" value="<?=htmlspecialchars($item['meta_title'])?>" onchange="updateTotals()"></td>
		<td id="meta_title" name="meta_title"><?=strlen($item['meta_title'])?></td>
	</tr>
	<tr>
		<th><a href="#" onclick="open_win('templates/help.php?action=keywords');return false" style="color:blue">Keywords :</a></th>
		<td><textarea name="data[meta_keywords]" style="width:67%" rows="5" onkeypress="textareaAutoSize(this, 3, 3, 25)" onchange="updateTotals()"><?=htmlspecialchars($item['meta_keywords'])?></textarea></td>
		<td id="meta_keywords" name="meta_keywords"><?=count(preg_split('/\\s*,\\s*/', $item['meta_keywords']))?></td>
	</tr>
	<tr>
		<th><a href="#" onclick="open_win('templates/help.php?action=desc');return false" style="color:blue">Description :</a></th>
		<td><textarea name="data[meta_description]" style="width:67%" rows=5 onkeypress="textareaAutoSize(this, 3, 3, 25)" onchange="updateTotals()"><?=htmlspecialchars($item['meta_description'])?></textarea></td>
		<td id="meta_description" name="meta_description"><?=strlen($item['meta_description'])?></td>
	</tr>
	<tr style="background-color:#fec;">
		<td class="left_title">META Tags Last Changed :</td>
		<td style="padding-left:3px;"><?= (!is_null($item['meta_at'])) ? sprintf(_('%s, By : %s'), date(_("d/m/Y"), strtotime($item['meta_at'])), $staff_list[$item['meta_by']]) : 'Never'?></td>
		<td>&nbsp;</td>
	</tr>
</table><br />
<div style="text-align:center;"><input type="button" value="Auto-Update Tags" onclick="updateTags()" /></div> -->
<br />
<br />

<center><table border="0" cellpadding="4" cellspacing="0">
	<tr><td colspan="2"><center>
		<input type="hidden" name="id" value="<?= htmlspecialchars($item["id"])?>" />
		<input type="hidden" name="action" value="do_edit" />
		<input type="submit" name="add" value="Update" />
		<input type="reset" name="clear" value="Reset" />
	</center></td>
	</tr>
</table></center>


