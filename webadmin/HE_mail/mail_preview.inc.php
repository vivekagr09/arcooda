<script language="JavaScript" type="text/JavaScript">
<!--
function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
//-->
</script>

<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="2">
  <TR>
    <TD> <FONT COLOR="#000066" SIZE="6"><B><FONT SIZE="5">Previewing Email</FONT></B></FONT></TD>
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
<BR>
<!-- MAIN BODY -->
<BR>
<TABLE WIDTH="95%" BORDER="0" CELLSPACING="0" CELLPADDING="2" ALIGN="CENTER">
  <TR>
    <TD WIDTH="18%">
      <DIV ALIGN="RIGHT"><B>Subject : </B></DIV>
    </TD>
    <TD WIDTH="82%"><?= $db_vars[subject] ?></TD>
  </TR>
  <TR>
    <TD WIDTH="18%">
      <DIV ALIGN="RIGHT"><B>Attachments : </B></DIV>
    </TD>
    <TD WIDTH="82%"><?= $db_vars[files] ?></TD>
  </TR>
  <TR>
    <TD COLSPAN="2">
      <HR SIZE="1" WIDTH="100%" NOSHADE>
    </TD>
  </TR>
  <TR>
    <TD COLSPAN="2"> <B>Message : HTML Format</B></TD>
  </TR>
  <TR>
    <TD COLSPAN="2">
      <TABLE WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="0" ALIGN="CENTER">
        <TR>
          <TD BGCOLOR="#CCCCCC">
            <TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="10">
              <TR>
                <TD BGCOLOR="#FFFFFF"><?= $db_vars[html_message] ?></TD>
              </TR>
            </TABLE>
          </TD>
        </TR>
      </TABLE>
    </TD>
  </TR>
  <TR>
    <TD COLSPAN="2"><B>Message : Plain Text</B></TD>
  </TR>
  <TR>
    <TD COLSPAN="2">
      <TABLE WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="0" ALIGN="CENTER">
        <TR>
          <TD BGCOLOR="#CCCCCC">
            <TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="10">
              <TR>
                <TD BGCOLOR="#FFFFFF">
                  <PRE><?= $db_vars[text_message] ?></PRE>
                </TD>
              </TR>
            </TABLE>
          </TD>
        </TR>
      </TABLE>
    </TD>
  </TR>
  <TR>
    <TD COLSPAN="2">
      <HR SIZE="1" WIDTH="100%" NOSHADE>
    </TD>
  </TR>
  <TR>
    <TD COLSPAN="2">&nbsp;</TD>
  </TR>
</TABLE>
<TABLE WIDTH="95%" BORDER="0" CELLSPACING="0" CELLPADDING="2" ALIGN="CENTER">
  <TR BGCOLOR="#EEEEEE">
    <TD><FONT SIZE="2" COLOR="#000066"><B>Final Step</B> - The above is the email
      that will be sent. Clicking on &quot;Send&quot; will send the email, there
      is no going back after clicking send. Please allow the emails to finish
      sending.</FONT></TD>
  </TR>
</TABLE>
<CENTER>
  <FORM ACTION="HE_mail.php?action=do_send&time=<?= time() ?>" METHOD="POST">
    <INPUT TYPE="button" NAME="Submit" VALUE="&lt;&lt; Back"  onClick="MM_goToURL('parent','HE_mail.php');return document.MM_returnValue"  class="button">
    <INPUT TYPE="button" NAME="Submit2" VALUE="Attachments" onClick="MM_openBrWindow('HE_mail/attachment.php','addatt','scrollbars=yes,resizable=yes,width=400,height=300')" class="button">
    <INPUT TYPE="Submit" NAME="do_send" VALUE="Send >>" class="button">
  </FORM>
</CENTER>
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
<DIV ALIGN="CENTER"> <FONT SIZE="1" COLOR="#666666"> Highwaygames Email System,
  created by Wei Zhuo<BR>
  &copy; Copyrights, 2000. All rights reservered. </FONT></DIV>
