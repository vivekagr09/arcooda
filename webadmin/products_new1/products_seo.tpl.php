<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
global $_GLOBALS;

/*
url
- max 78 chars (email)

title tag
- No '"<>{}[]()
- max 64 chars (w3c)
- max 67 chars (Yahoo)
- max 70 chars (Google)
- 5-65 chars (Bing)
- 3-80 chars (word-wrapping)

description tag
- No '"<>{}[] characters
- max 256 chars (Yahoo)
- 25-150 chars (Bing)

keywords tag
- 1-2% density (Google)
- 3% density (Yahoo)
- 16.1-21.8% Title
- 1.0-2.1% Links
- 0.9-2.1% Alt Text
- 1.7-2.9% Keywords
- 2.0-3.9% Description
- 2.7-4.4% Bold
- 3.9-6.4% H1
 */
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
	var stop_words = ["a", "a's", "able", "about", "above", "abroad", "according", "accordingly", "across", "actually", "adj", "after", "afterwards", "again", "against", "ago", "ahead", "ain't", "all", "allow", "allows", "almost", "alone", "along", "alongside", "already", "also", "although", "always", "am", "amid", "amidst", "among", "amongst", "an", "and", "another", "any", "anybody", "anyhow", "anyone", "anything", "anyway", "anyways", "anywhere", "apart", "appear", "appreciate", "appropriate", "are", "aren't", "around", "as", "aside", "ask", "asking", "associated", "at", "available", "away", "awfully", "b", "back", "backward", "backwards", "be", "became", "because", "become", "becomes", "becoming", "been", "before", "beforehand", "begin", "behind", "being", "believe", "below", "beside", "besides", "best", "better", "between", "beyond", "both", "brief", "but", "by", "c", "c'mon", "c's", "came", "can", "can't", "cannot", "cant", "caption", "cause", "causes", "certain", "certainly", "changes", "clearly", "co", "co.", "com", "come", "comes", "concerning", "consequently", "consider", "considering", "contain", "containing", "contains", "corresponding", "could", "couldn't", "course", "currently", "d", "dare", "daren't", "definitely", "described", "despite", "did", "didn't", "different", "directly", "do", "does", "doesn't", "doing", "don't", "done", "down", "downwards", "during", "e", "each", "edu", "eg", "eight", "eighty", "either", "else", "elsewhere", "end", "ending", "enough", "entirely", "especially", "et", "etc", "even", "ever", "evermore", "every", "everybody", "everyone", "everything", "everywhere", "ex", "exactly", "example", "except", "f", "fairly", "far", "farther", "few", "fewer", "fifth", "first", "five", "followed", "following", "follows", "for", "forever", "former", "formerly", "forth", "forward", "found", "four", "from", "further", "furthermore", "g", "get", "gets", "getting", "given", "gives", "go", "goes", "going", "gone", "got", "gotten", "greetings", "h", "had", "hadn't", "half", "happens", "hardly", "has", "hasn't", "have", "haven't", "having", "he", "he'd", "he'll", "he's", "hello", "help", "hence", "her", "here", "here's", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "hi", "him", "himself", "his", "hither", "hopefully", "how", "howbeit", "however", "hundred", "i", "i'd", "i'll", "i'm", "i've", "ie", "if", "ignored", "immediate", "in", "inasmuch", "inc", "inc.", "indeed", "indicate", "indicated", "indicates", "inner", "inside", "insofar", "instead", "into", "inward", "is", "isn't", "it", "it'd", "it'll", "it's", "its", "itself", "j", "just", "k", "keep", "keeps", "kept", "know", "known", "knows", "l", "last", "lately", "later", "latter", "latterly", "least", "less", "lest", "let", "let's", "like", "liked", "likely", "likewise", "little", "look", "looking", "looks", "low", "lower", "ltd", "m", "made", "mainly", "make", "makes", "many", "may", "maybe", "mayn't", "me", "mean", "meantime", "meanwhile", "merely", "might", "mightn't", "mine", "minus", "miss", "more", "moreover", "most", "mostly", "mr", "mrs", "much", "must", "mustn't", "my", "myself", "n", "name", "namely", "nd", "near", "nearly", "necessary", "need", "needn't", "needs", "neither", "never", "neverf", "neverless", "nevertheless", "new", "next", "nine", "ninety", "no", "no-one", "nobody", "non", "none", "nonetheless", "noone", "nor", "normally", "not", "nothing", "notwithstanding", "novel", "now", "nowhere", "o", "obviously", "of", "off", "often", "oh", "ok", "okay", "old", "on", "once", "one", "one's", "ones", "only", "onto", "opposite", "or", "other", "others", "otherwise", "ought", "oughtn't", "our", "ours", "ourselves", "out", "outside", "over", "overall", "own", "p", "particular", "particularly", "past", "per", "perhaps", "placed", "please", "plus", "possible", "presumably", "probably", "provided", "provides", "q", "que", "quite", "qv", "r", "rather", "rd", "re", "really", "reasonably", "recent", "recently", "regarding", "regardless", "regards", "relatively", "respectively", "right", "round", "s", "said", "same", "saw", "say", "saying", "says", "second", "secondly", "see", "seeing", "seem", "seemed", "seeming", "seems", "seen", "self", "selves", "sensible", "sent", "serious", "seriously", "seven", "several", "shall", "shan't", "she", "she'd", "she'll", "she's", "should", "shouldn't", "since", "six", "so", "some", "somebody", "someday", "somehow", "someone", "something", "sometime", "sometimes", "somewhat", "somewhere", "soon", "sorry", "specified", "specify", "specifying", "still", "sub", "such", "sup", "sure", "t", "t's", "take", "taken", "taking", "tell", "tends", "th", "than", "thank", "thanks", "thanx", "that", "that'll", "that's", "that've", "thats", "the", "their", "theirs", "them", "themselves", "then", "thence", "there", "there'd", "there'll", "there're", "there's", "there've", "thereafter", "thereby", "therefore", "therein", "theres", "thereupon", "these", "they", "they'd", "they'll", "they're", "they've", "thing", "things", "think", "third", "thirty", "this", "thorough", "thoroughly", "those", "though", "three", "through", "throughout", "thru", "thus", "till", "to", "together", "too", "took", "toward", "towards", "tried", "tries", "truly", "try", "trying", "twice", "two", "u", "un", "under", "underneath", "undoing", "unfortunately", "unless", "unlike", "unlikely", "until", "unto", "up", "upon", "upwards", "us", "use", "used", "useful", "uses", "using", "usually", "v", "value", "various", "versus", "very", "via", "viz", "vs", "w", "want", "wants", "was", "wasn't", "way", "we", "we'd", "we'll", "we're", "we've", "welcome", "well", "went", "were", "weren't", "what", "what'll", "what's", "what've", "whatever", "when", "whence", "whenever", "where", "where's", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "whichever", "while", "whilst", "whither", "who", "who'd", "who'll", "who's", "whoever", "whole", "whom", "whomever", "whose", "why", "will", "willing", "wish", "with", "within", "without", "won't", "wonder", "would", "wouldn't", "x", "y", "yes", "yet", "you", "you'd", "you'll", "you're", "you've", "your", "yours", "yourself", "yourselves", "z", "zero"];
	function seo_find_keywords()
	{
		var title = $("[name='item[product_name]']").val();
		var keywords = $("[name='item[meta_keywords]']").val();
		var description = ($("[name='item[long_desc]']").val() + "\n" + $("[name='item[product_details]']").val()).split(/<[^>]*>/).join(" ");
		var all = (title + "\n" + keywords + "\n" + description).split(/\b\W+\b/);
		title = title.toLowerCase().split(/\b\W+\b/).join(" ");
		keywords = keywords.toLowerCase().split(/\b\W+\b/).join(" ");
		description = description.toLowerCase().split(/\b\W+\b/).join(" ");
		var phrases = {};
		var w1 = "";
		var w2 = "";
		var w3 = "";
		for (var i = 0;i < all.length;i++) if (all[i].length > 1 && !is_numeric(all[i]) && !in_array(all[i].toLowerCase(), stop_words))
		{
			w1 = w2;
			w2 = w3;
			w3 = all[i];
			var k3u = w3;
			var k2u = w2 + " " + w3;
			var k1u = w1 + " " + w2 + " " + w3;
			var k3l = k3u.toLowerCase();
			var k2l = k2u.toLowerCase();
			var k1l = k1u.toLowerCase();
			if (w3 != "")
			{
				if (!phrases[k3l]) phrases[k3l] = {};
				phrases[k3l][k3u] = (phrases[k3l][k3u]) ? (phrases[k3l][k3u] + 1) : 1;
				if (w2 != "")
				{
					if (!phrases[k2l]) phrases[k2l] = {};
					phrases[k2l][k2u] = (phrases[k2l][k2u]) ? (phrases[k2l][k2u] + 1) : 1;
					if (w1 != "")
					{
						if (!phrases[k1l]) phrases[k1l] = {};
						phrases[k1l][k1u] = (phrases[k1l][k1u]) ? (phrases[k1l][k1u] + 1) : 1;
					}
				}
			}
		}
		else w1 = w2 = w3 = "";
		var results = [];
		var all_length = all.join(" ").length;
		for (var i in phrases)
		{
			var best_case = i;
			var best_case_score = 0;
			for (var j in phrases[i]) if (phrases[i][j] > best_case_score)
			{
				best_case = j;
				best_case_score = phrases[i][j];
			}
			var count_title = title.split(new RegExp("\\b" + i.replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1") + "\\b")).length - 1;
			var count_keywords = keywords.split(new RegExp("\\b" + i.replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1") + "\\b")).length - 1;
			var count_description = description.split(new RegExp("\\b" + i.replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1") + "\\b")).length - 1;
			var count_all = count_title + count_description;
			results.push([
				best_case,
				count_all,
				(all_length != 0) ? sprintf("%0.2f%%", count_all * best_case.length * 100.0 / all_length) : "-",
				count_title,
				(title.length != 0) ? sprintf("%0.2f%%", count_title * best_case.length * 100.0 / title.length) : "-",
				count_keywords,
				(keywords.length != 0) ? sprintf("%0.2f%%", count_keywords * best_case.length * 100.0 / keywords.length) : "-",
				count_description,
				(description.length != 0) ? sprintf("%0.2f%%", count_description * best_case.length * 100.0 / description.length) : "-",
			]);
		}
		results.sort(function(a, b) {
			var temp = (b[3] * b[0].length - a[3] * a[0].length);
			if (temp != 0) return temp;
			temp = (b[7] * b[0].length - a[7] * a[0].length);
			if (temp != 0) return temp;
			return (b[1] * b[0].length - a[1] * a[0].length);
		});
		return results;
	}

	$("[name='item[meta_title]'], [name='item[meta_keywords]'], [name='itemitem[meta_description]'], [name='item[product_name]'], [name='item[long_desc]'], [name='item[product_details]']").change(function() {
		var results = seo_find_keywords();
		var $table = $("#seo_phrase_density");
		$("td", $table).closest("tr").detach();
		var alt = false;
		var count = 0;
		for (var i = 0;i < results.length;i++) if (count < 20 || results[i][3] > 0 || results[i][7] > 0)
		{
			var $row = $("<tr></tr>");
			if (alt = !alt) $row.addClass("alt");
			count++;
			$row.append($("<td style=\"white-space:nowrap;\"></td>").text(results[i][0]));
			$row.append($("<td style=\"text-align:right;\"></td>").text(results[i][1]));
			$row.append($("<td style=\"text-align:right;\"></td>").text(results[i][2]));
			$row.append($("<td style=\"text-align:right;\"></td>").text(results[i][3]));
			$row.append($("<td style=\"text-align:right;\"></td>").text(results[i][4]));
			$row.append($("<td style=\"text-align:right;\"></td>").text(results[i][5]));
			$row.append($("<td style=\"text-align:right;\"></td>").text(results[i][6]));
			$row.append($("<td style=\"text-align:right;\"></td>").text(results[i][7]));
			$row.append($("<td style=\"text-align:right;\"></td>").text(results[i][8]));
			$table.append($row);
		}
	});
	$("[name='item[long_desc]'], [name='item[meta_description]'], [name='item[meta_keywords]'], [name='item[meta_title]'], [name='item[product_details]'], [name='item[product_name]'], [name='item[type]'], [name^='signal_from['], [name^='signal_to['], [name^='signals_from['], [name^='signals_to[']").change(function() {
		var partial_url = "";

		var signal_from = $("[name^='signals_from[']:checked").first().closest("table").prev("a").text().split(/[^a-zA-Z0-9]+/).join("_").toLowerCase();
		if (!signal_from) signal_from = $("[name^='signal_from[']:checked").first().next("a").text().split(/[^a-zA-Z0-9]+/).join("_").toLowerCase();
		if (signal_from)
		{
			var signal_to = $("[name^='signals_to[']:checked").first().closest("table").prev("a").text().split(/[^a-zA-Z0-9]+/).join("_").toLowerCase();
			if (!signal_to) signal_from = $("[name^='signal_to[']:checked").first().next("a").text().split(/[^a-zA-Z0-9]+/).join("_").toLowerCase();
			if (signal_to) partial_url = signal_from + "_to_" + signal_to + "/<?=$product_id?>.html";
			else partial_url = signal_from + "/<?=$product_id?>.html";
		}
		else partial_url = $("[name='item[type]'] option:selected").text().split(/[^a-zA-Z0-9]+/).join("_").toLowerCase() + "_<?=$product_id?>.html";

		var $table = $("#seo_engine_notes");
		$("td", $table).closest("tr").detach();
		var alt = false;

		var product_url = <?=json_encode(rtrim($_GLOBALS["site_settings"]["home_uri"], "/"))?> + "/products/" + partial_url;
		var title = $("[name='item[meta_title]']").val();
		var description = $("[name='item[meta_description]']").val();

		var keywords = $("[name='item[meta_keywords]']").val().toLowerCase().split(/\b\W+\b/);
		var	all = (title + "\n" + ($("[name='item[long_desc]']").val() + "\n" + $("[name='item[product_details]']").val()).split(/<[^>]*>/).join(" ")).toLowerCase().split(/\b\W+\b/).join(" ");
		var keyword_density = 0;
		for (var i = 0;i < keywords.length;i++) if (!in_array(keywords[i], stop_words)) keyword_density += all.length - (" " + all + " ").split(" " + keywords[i] + " ").join(" ").length;
		keyword_density = (100.0 * keyword_density) / (all.length * keywords.length);

		var $row;

		$row = $("<tr></tr>");
		if (alt = !alt) $row.addClass("alt");
		$row.append("<td>Actual</td>");
		$row.append($("<td></td>").text(/*product_url.substr(0, 78) +*/ "(" + product_url.length + " Characters)"));
		$row.append($("<td></td>").text(/*title.substr(0, 64) +*/ "(" + title.length + " Characters)"));
		$row.append($("<td></td>").text(/*description.substr(0, 150) +*/ "(" + description.length + " Characters)"));
		$row.append($("<td></td>").text("(" + sprintf("%0.2f%%", keyword_density) + " Density)"));
		$table.append($row);

		$row = $("<tr></tr>");
		if (alt = !alt) $row.addClass("alt");
		$row.append("<td>E-Mail</td>");
		$row.append("<td" + ((product_url.length > 78) ? " style=\"color:#f00;\"" : "") + ">78 Characters</td>");
		$row.append("<td" + ((title.length > 80 || title.length < 3) ? " style=\"color:#f00;\"" : "") + ">3-80 Characters</td>");
		$row.append("<td>-</td>");
		$row.append("<td>-</td>");
		$table.append($row);

		$row = $("<tr></tr>");
		if (alt = !alt) $row.addClass("alt");
		$row.append("<td>Google</td>");
		$row.append("<td>-</td>");
		$row.append("<td" + ((title.length > 70) ? " style=\"color:#f00;\"" : "") + ">70 Characters</td>");
		$row.append("<td>-</td>");
		$row.append("<td" + ((keyword_density > 2.0 || keyword_density < 1.0) ? " style=\"color:#f00;\"" : "") + ">1-2% Density</td>");
		$table.append($row);

		$row = $("<tr></tr>");
		if (alt = !alt) $row.addClass("alt");
		$row.append("<td>Yahoo</td>");
		$row.append("<td>-</td>");
		$row.append("<td" + ((title.length > 67) ? " style=\"color:#f00;\"" : "") + ">67 Characters</td>");
		$row.append("<td" + ((description.length > 256) ? " style=\"color:#f00;\"" : "") + ">256 Characters</td>");
		$row.append("<td" + ((keyword_density > 3.0) ? " style=\"color:#f00;\"" : "") + ">3% Density</td>");
		$table.append($row);

		$row = $("<tr></tr>");
		if (alt = !alt) $row.addClass("alt");
		$row.append("<td>Bing</td>");
		$row.append("<td>-</td>");
		$row.append("<td" + ((title.length > 65 || title.length < 5) ? " style=\"color:#f00;\"" : "") + ">5-65 Characters</td>");
		$row.append("<td" + ((description.length > 150 || description.length < 25) ? " style=\"color:#f00;\"" : "") + ">25-150 Characters</td>");
		$row.append("<td>-</td>");
		$table.append($row);

		$row = $("<tr></tr>");
		if (alt = !alt) $row.addClass("alt");
		$row.append("<td>W3C</td>");
		$row.append("<td>-</td>");
		$row.append("<td" + ((title.length > 64 || title.split(/[\'\"\<\>\{\}\[\]\(\)]/).length > 1) ? " style=\"color:#f00;\"" : "") + ">64 Characters<br />Avoid \'\"<>{}[]() Characters</td>");
		$row.append("<td" + ((description.split(/[\'\"\<\>\{\}\[\]]/).length > 1) ? " style=\"color:#f00;\"" : "") + ">Avoid \'\"<>{}[] Characters</td>");
		$row.append("<td>-</td>");
		$table.append($row);
	});
	$("[name='item[meta_title]']").change();
});
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSEO\');return false">Search Engine Optimisation</a>')?>

<div id="sectionSEO" style="display:none;">
	<table id="seo_engine_notes" class="table_grid" style="width:95%;">
		<tr>
			<th></th>
			<th>URL</th>
			<th>META Title</th>
			<th>META Description</th>
			<th>META Keywords</th>
		</tr>
	</table>

	<div style="max-height:480px;overflow-y:scroll;overflow-x:hidden;width:95%;margin:auto;"><table id="seo_phrase_density" class="table_grid" style="width:100%;">
		<tr>
			<th>Phrase</th>
			<th colspan="2">All Words</th>
			<th colspan="2">Title</th>
			<th colspan="2">Keywords</th>
			<th colspan="2">Description</th>
		</tr>
		<tr>
			<th style="width:100%;"></th>
			<th>Repeats</th>
			<th>Density</th>
			<th>Repeats</th>
			<th>Density</th>
			<th>Repeats</th>
			<th>Density</th>
			<th>Repeats</th>
			<th>Density</th>
		</tr>
	</table></div>
</div>

<div id="sectionSEO_hideNote" style="width:95%;margin:auto;">
	Click "Search Engine Optimisation" to display this section
</div>
<br />
<br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
