<?php
/**
 * @var mixed[] $email
 */
?>
<!-- preview -->
<br>
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="0">
  <tr>
    <td width="15%" nowrap><b>Subject : </b></td>
    <td width="85%"><?= $email['subject'] ?></td>
  </tr>
  <tr>
    <td nowrap><b>Attachments : </b></td>
    <td><?= $email['files'] ?></td>
  </tr>
</table>
<br>
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="0">
  <tr>
    <td> <hr size="1" width="100%" noshade> </td>
  </tr>
  <tr>
    <td> <b>Message : HTML Format</b></td>
  </tr>
  <tr>
    <td> <table width="90%" border="0" cellspacing="0" cellpadding="10" align="center">
        <tr>
          <td class="box"><?= $email['html_message'] ?></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td><b>Message : Plain Text</b></td>
  </tr>
  <tr>
    <td> <table width="90%" border="0" cellspacing="0" cellpadding="10" align="center">
        <tr>
          <td class="box"> <pre><?= $email['text_message'] ?></pre>
          </td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td> <hr size="1" width="100%" noshade> </td>
  </tr>
</table>
<!-- //preview -->
