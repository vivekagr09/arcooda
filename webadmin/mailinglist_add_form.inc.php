<?php
?>
          <table border=0 cellpadding=2 align="center">
            <tr>
              <td>Full Name:</td>
              <td>
                <input type="text" name="name" size="20" style="width:300px">
              </td>
            </tr>
            <tr>
              <td><B>Email Address:</b></td>
              <td>
                <input type="text" name="email" size="20" style="width:300px">
              </td>
            </tr>
            <tr>
              <td>Country:</td>
              <td>
      <select name="country" style="width:300px">
	  <option value=""></option>
    <?=formatHTMLOptions(getCountryList(false))?>
      </select>
              </td>
            </tr>
            <tr>
              <td>Company Name:</td>
              <td>
                <input type="text" name="company" size="20" style="width:300px">
              </td>
            </tr>
            <tr>
              <td>Comments:</td>
              <td>
                <textarea name="comments" cols="20" rows="7" style="width:300px"></textarea>
              </td>
            </tr>
  <tr>
    <td nowrap><b>Active:</b></td>
    <td>
      <select name="active" style="width:70px">
		<option value="1" selected>Yes</option>
		<option value="0">No</option>
	  </select>
    </td>
  </tr>

            <tr>
              <td colspan="2">
                <center>
		<input type="hidden" name="action" value="do_add">
        <input type="submit" name="add" value="Add">
        <input type="reset" name="clear" value="Clear">
                </center>
              </td>
            </tr>
          </table>
        <p>
