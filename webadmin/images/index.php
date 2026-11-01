<?php
include_once $_SERVER["DOCUMENT_ROOT"].'/webadmin/common.inc.php';
?>
<html>
 <head>
  <title>Software Errors / Jobs</title>
<?php
	//Includes User Auth Tools
	include_once $_SERVER["DOCUMENT_ROOT"].'/webadmin/users/auth.php';

	//Gets User Name
	$user = get_user();

	//Gets User Sections
	$all_sections = get_all_sections();
	$allowed_sections = get_allowed_sections($user);

	//Checks User Permissions
	if(!has_permission('ERROR', $allowed_sections)){
		exit;
	}

	//Includes Needed Files
	include_once $_SERVER["DOCUMENT_ROOT"].'/webadmin/connection.inc.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/webadmin/admin_common.php';

	//Displays Images For Orders
	function ImageOrder($Ord){
		if($Ord == "desc"){
			return "<img src=\"http://www.highwaygroup.net/webadmin/images/desc.gif\" />";
		}else{
			return "<img src=\"http://www.highwaygroup.net/webadmin/images/asc.gif\"  />";
		}
	}

	//Used To Make Ordering URLS
	function URLMaker($Forget){

		//Loops Thru Get Vars
		$RetVal="";
		foreach($_GET as $key => $value){
			//Checks If Its Ignore Value
			if($key != $Forget){
				//Adds It To Return Value
				$RetVal.="&$key=$value";
			}
		}

		//Returns Value
		if($RetVal!="")
			return $RetVal;
	}
?>
 </head>
 <body>
  <table align="center">
   <tr class="td_options" bgcolor="#3399FF">
    <td><b>Websites</b></td>
 <?php
 	//Gets Users
 	$sql  = "SELECT `au`.*, `u`.id FROM `highwaygroup`.`admin_users` AS `au` INNER JOIN `highwaygroup`.`users` AS `u` ON `u`.`user` = `au`.`user` WHERE `section_id` = 29";
	$data = $DB->query($sql);

	//Displays Users
	while($row = $DB->fetch_array($data)){
		$UserCount++;
		$User[$UserCount]        =$row['id']  ;
		$UserName[$UserCount]    =$row['user'];
		$UserNameLink[$row['id']]=$row['user'];
		echo "    <td><a href='index.php?uid={$row['id']}".URLMaker("uid")."'><b class=\"td_options\">{$row['user']}</b></a></td>\n";
	}
 ?>
    <td><b>Total</b></td>
   </tr>
<?php
	//Gets Sites Data
	$sql  = "SELECT id, website FROM websites;";
	$data = $DB->query($sql);

	//Displays Sites
	while($row = $DB->fetch_array($data)){

		//Draws Link For Website
		echo "<tr>\n";
		echo "<td bgcolor=\"#3399FF\"><a href=\"index.php?sid={$row['id']}".URLMaker("sid")."\"><b class=\"td_options\">".$row["website"]."</b></a></td>\n";

		//Saves Sotes
		$Sites[$row['id']] = $row["website"];

		//Changes BG Color
		$BGTxt = $BGTxt?"":"class=\"TableRowAlt\"";

		//Gets Ready For Next User
		$Total=0;
		for($a=1;$a<=$UserCount;$a++){

			//Draws Amounts For Each Coder
			$sql = "SELECT count(id) as cnt FROM bugs WHERE assigned = '{$User[$a]}' AND website = '{$row['id']}' AND status < '3';";
			$Cnt = $DB->fetch_array($DB->query($sql));
			echo "<td align=\"center\" $BGTxt>".$Cnt["cnt"]."</td>\n";

			//Makes Total
			$Total+=$Cnt["cnt"];

		}

		//Displays Total
		echo "<td align=\"center\" $BGTxt><b>$Total</b></td>\n";
		echo "</tr>\n";
	}
?>
   </tr>
  </table>
<?php
	//Makes Headers
	$Header.=($_GET["uid"])?"User <a href=\"index.php?uid={$_GET['uid']}\"> {$UserNameLink[$_GET['uid']]} </a>":"";
	$Header.=($_GET["sid"])?"For Site <a href=\"index.php?sid={$_GET['sid']}\"> {$Sites[$_GET['sid']]}</a>":"";

	//Makes Clause
	$Clause .=$_GET["uid"]?" assigned='{$_GET['uid']}' ":"";
	$Clause .=$_GET["sid"]?($Clause?" AND":"")." bugs.website ='{$_GET["sid"]}'":"";
	$Clause  = $Clause?" WHERE $Clause":"";

	//Gets Order
	$OrderBy=$_GET["ord"]?$_GET["ord"]:"level";
	$SortBy =$_GET["by"] ?$_GET["by"] :"desc" ;

	//Gets Data From Database
	$sql  = "SELECT bugs.priority, bugs.date_added, bugs.title, websites.website, user2.user as fromed, users.user as toed, priority.description as status, status.status as active, priority.colour, priority.level FROM bugs INNER JOIN priority ON bugs.priority = priority.id INNER JOIN websites ON bugs.website = websites.id INNER JOIN users ON bugs.assigned = users.id INNER JOIN users as user2 ON bugs.reporter = user2.id INNER JOIN status ON status.id = bugs.status $Clause ORDER BY $OrderBy $SortBy";
	$data = $DB->query($sql) or die($DB->error());
?>
  <p align="center"><?php echo $Header?></p>
  <table align="center" width="80%">
   <tr style="font-weight: bold" class="td_options" bgcolor="#3399FF">
    <td align="center"><?php echo ($_GET["ord"]=="level")?"<a href=\"index.php?by=".($_GET["by"]=="asc"?"desc":"asc").URLMaker("by")."\"\>":"<a href=\"index.php?ord=level".URLMaker("ord")."\"\>"?>Priority</a></td>
	<td align="center"><?php echo ($_GET["ord"]=="date_added")?"<a href=\"index.php?by=".($_GET["by"]=="asc"?"desc":"asc").URLMaker("by")."\"\>":"<a href=\"index.php?ord=date_added".URLMaker("ord")."\"\>"?>Dated</a></td>
	<td align="center"><?php echo ($_GET["ord"]=="title")?"<a href=\"index.php?by=".($_GET["by"]=="asc"?"desc":"asc").URLMaker("by")."\"\>":"<a href=\"index.php?ord=title".URLMaker("ord")."\"\>"?>Title</a></td>
	<td align="center"><?php echo ($_GET["ord"]=="website")?"<a href=\"index.php?by=".($_GET["by"]=="asc"?"desc":"asc").URLMaker("by")."\"\>":"<a href=\"index.php?ord=website".URLMaker("ord")."\"\>"?>Site</a></td>
	<td align="center"><?php echo ($_GET["ord"]=="fromed")?"<a href=\"index.php?by=".($_GET["by"]=="asc"?"desc":"asc").URLMaker("by")."\"\>":"<a href=\"index.php?ord=fromed".URLMaker("ord")."\"\>"?>From</a></td>
	<td align="center"><?php echo ($_GET["ord"]=="toed")?"<a href=\"index.php?by=".($_GET["by"]=="asc"?"desc":"asc").URLMaker("by")."\"\>":"<a href=\"index.php?ord=toed".URLMaker("ord")."\"\>"?>To</a></td>
	<td align="center"><?php echo ($_GET["ord"]=="active")?"<a href=\"index.php?by=".($_GET["by"]=="asc"?"desc":"asc").URLMaker("by")."\"\>":"<a href=\"index.php?ord=active".URLMaker("ord")."\"\>"?>Status</a></td>
	<td align="center">Days</td>
   </tr>
<?php
	while($row = $DB->fetch_array($data)){

		//Changes BG Color
		$BGTxt = $BGTxt?"":"class=\"TableRowAlt\"";
?>

   <tr <?php echo $BGTxt?>>
    <td nowrap align="left"><span style="background-color:<?php echo $row["colour"]?>">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;<?php echo substr($row["status"],0,2)?></td>
	<td nowrap align="center"><?php echo substr($row["date_added"],0,10)?></td>
	<td nowrap align="center"><?php echo $row["title"]?></td>
	<td nowrap align="center"><?php echo $row["website"]?></td>
	<td nowrap align="center"><?php echo $row["fromed"]?></td>
	<td nowrap align="center"><?php echo $row["toed"]?></td>
	<td nowrap align="center"><?php echo $row["active"]?></td>
	<td nowrap align="center"></td>
   </tr>

<?php } ?>
  </table>
 </body>
</html>