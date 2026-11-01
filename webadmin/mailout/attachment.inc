<?php
/***************************************
** Title.........:	Attachement HTML
** Version.......:	1.0
** Author........:	Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
** Filename......:	attachment.inc
** Last changed..:	14/12/2000
** Notes.........:	The HTML for the attachment page
***************************************/

?>
<html>
<head>
<title>Attachements</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
body,p,td {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px}
.td_options {  color: #FFFFFF; font-size: 12px}
a:hover {  color: #FF0000}
-->
</style>
</head>
<!-- header -->
<body bgcolor="#FFFFFF" text="#000000">
<FORM ACTION="<?= $PHP_SELF?>" ENCTYPE="multipart/form-data" METHOD="POST">
<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
  <TR>
    <TD BGCOLOR="#EEEEEE"><B>Step 1</B> - Select a file to attach</TD>
  </TR>
  <TR>
    <TD>
      <DIV ALIGN="CENTER">
        <INPUT TYPE="file" NAME="the_file" SIZE="30" class="button">
      </DIV>
    </TD>
  </TR>
  <TR>
    <TD BGCOLOR="#EEEEEE"><B>Step 2</B> - Click on &quot;Attach&quot; to include
      the file in the Email</TD>
  </TR>
  <TR>
    <TD>
      <TABLE CELLPADDING="5" CELLSPACING="0" ALIGN="CENTER">
        <TR>
          <TD ALIGN="center" VALIGN="middle">
            <INPUT TYPE="Submit" NAME="task" VALUE="Attach" class="button">
            <BR>
            <BR>
            <INPUT TYPE="Submit" NAME="task" VALUE="Remove" class="button">
          </TD>
          <TD>
            <SELECT NAME="files" SIZE=5 class="textbox">
              <OPTION VALUE="">-- Attachment --</OPTION>
<?php
fileOptionsList()?>
            </SELECT>
          </TD>
        </TR>
      </TABLE>
    </TD>
  </TR>
  <TR>
    <TD BGCOLOR="#EEEEEE"><B>Step 3</B> - When ready click on &quot;Done&quot;
      below</TD>
  </TR>
  <TR>
    <TD>&nbsp;</TD>
  </TR>
  <TR>
    <TD>
      <DIV ALIGN="CENTER">
        <INPUT TYPE="button" NAME="Done" VALUE="Done" OnClick="self.close()" class="button">
      </DIV>
    </TD>
  </TR>
</TABLE>
</FORM>
</body>
</html>