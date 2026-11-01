<?php
?><TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="2">
  <TR>
    <TD> <FONT COLOR="#000066" SIZE="6"><B><FONT SIZE="5">Product Notification
      Email</FONT></B></FONT></TD>
  </TR>
  <TR>
    <TD>
      <TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0">
        <TR>
          <TD BGCOLOR="#000066"><IMG SRC="/_.gif" WIDTH="1" HEIGHT="2"></TD>
        </TR>
        <TR>
          <TD><IMG SRC="/_.gif" WIDTH="1" HEIGHT="1"></TD>
        </TR>
        <TR>
          <TD BGCOLOR="#000066"><IMG SRC="/_.gif" WIDTH="1" HEIGHT="2"></TD>
        </TR>
      </TABLE>
    </TD>
  </TR>
</TABLE>
<!-- END HEADER -->
<!-- MAIN BODY -->
<BR>
<FORM ACTION="product_notify_send_emails.php?id=<?=$id;?>&action=send" name="fHtmlEditor"  METHOD="post">
  <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="4" ALIGN="CENTER" WIDTH="95%">
    <TR>
      <TD COLSPAN="2" BGCOLOR="#EEEEEE"><FONT SIZE="2"><B><FONT COLOR="#000066">Step
        1</FONT></B><FONT COLOR="#000066"> - Enter the email subject.</FONT></FONT></TD>
    </TR>
    <TR>
      <TD WIDTH="11%"> <DIV ALIGN="RIGHT"><B>Subject:</B></DIV></TD>
      <TD WIDTH="89%"> <INPUT TYPE="text" NAME="subject" SIZE="50" CLASS="textbox" value="<?=$subject;?>">
      </TD>
    </TR>
    <TR>
      <TD VALIGN="TOP" COLSPAN="2">&nbsp;</TD>
    </TR>
    <TR>
      <TD VALIGN="TOP" COLSPAN="2" BGCOLOR="#EEEEEE"><FONT SIZE="2"><B><FONT COLOR="#000066">Step
        2</FONT></B><FONT COLOR="#000066"> - Write the HTML message below, please
        check that the image tags and links are working.</FONT></FONT></TD>
    </TR>
    <TR>
      <TD VALIGN="TOP" COLSPAN="2"><B>Message: HTML Style</B></TD>
    </TR>
    <TR>
      <TD VALIGN="TOP" COLSPAN="2"> <DIV ALIGN="CENTER">
          <!-- editor -->
<?php

$send_mail['html_message'] = $html_msg;
include __DIR__ . '/editor.html';

?>
          <!-- //editor -->
          <!--          <TEXTAREA NAME="vars[html_message]" COLS="40" ROWS="15" CLASS="textbox" WRAP="PHYSICAL" style="width:100%"><?=stripslashes($send_mail['contents']);?></TEXTAREA>
-->
        </DIV></TD>
    </TR>
    <TR>
      <TD COLSPAN="2" ALIGN="CENTER"><input name="cancel" type="button" id="cancel" value="Cancel" onClick="javascript:history.back()">
        <INPUT TYPE="submit" NAME="Submit" VALUE="Send &gt;&gt;" CLASS="button" onClick="OnFormSubmit()">
        <BR> </TD>
    </TR>
  </TABLE>
</FORM>
<BR>
<!-- END MAIN BODY -->
<BR>
<!-- FOOTER -->
<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0">
  <TR>
    <TD BGCOLOR="#000066"><IMG SRC="/_.gif" WIDTH="1" HEIGHT="2"></TD>
  </TR>
  <TR>
    <TD><IMG SRC="/_.gif" WIDTH="1" HEIGHT="1"></TD>
  </TR>
  <TR>
    <TD BGCOLOR="#000066"><IMG SRC="/_.gif" WIDTH="1" HEIGHT="2"></TD>
  </TR>
</TABLE>
<!-- END FOOTER -->
<BR>
<!-- COPYRIGHTS -->
<BR>
<!-- THE END -->