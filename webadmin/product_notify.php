<?php

$section_code = "EMAILS";
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$items = [];

$statement = "SELECT
					p.*, UNIX_TIMESTAMP(p.date_available) AS time_available,
					t.name as type_name,
					COUNT(n.product_id) as total,
					tp.name as section
				FROM
					products p,
					product_types t,
					product_types tp,
					product_notify_email n
				WHERE
					p.type = t.id
					AND t.parent = tp.id
					AND p.id = n.product_id
					AND p.date_available > 0 ";

if ('yes' == $sent) {
    $statement .= "	AND n.notified = 'y' ";
} else {
    $statement .= " AND n.notified != 'y' ";
}

$statement .= "GROUP BY n.product_id";

$result = $DB->query($statement);

while ($row = $DB->fetch_array($result)) {
    $index         = $row['time_available'] + count($items);
    $items[$index] = $row;
}

?>

<br>
<div align="center"><a href="product_notify.php">View Pending Notifications</a> | <a href="product_notify.php?sent=yes">View Past Notifications</a></div>
<br />
<TABLE CELLSPACING="2" CELLPADDING="1" BORDER="0" WIDTH="100%" align="center">
<TR ALIGN="CENTER" BGCOLOR="#f4f4f4">
<td><b><font size="+1">
<?php if ('yes' != $sent) {
    echo "Pending Notifications";
} else {
    echo "Past Notifications";
}
?>
</font></b></td>
</TR>
</TABLE><br />
<?php if ('yes' != $sent) {?>
<table border="0" align="center" cellpadding="4" cellspacing="3">
        <tr>
          <td bgcolor="#FFEBCC"><div align="center">Past notifications Not Yet
              Sent</div></td>
          <td><div align="center"></div></td>
          <td bgcolor="#E0F5CC"><div align="center">Today's Notifications</div></td>
          <td><div align="center"></div></td>
          <td bgcolor="#EBF5FF"><div align="center">Upcoming Notifications</div></td>
        </tr>
      </table>
	  <br />
<?php }?>
<table border="0" cellspacing="1" cellpadding="2" align="center">
  <tr>
    <td bgcolor="#000066"><font color="#FFFFFF"><strong>Product</strong></font></td>
    <td bgcolor="#000066"><font color="#FFFFFF"><strong>Date Available</strong></font></td>
    <td bgcolor="#000066"><font color="#FFFFFF"><strong>Notifications</strong></font></td>
    <td bgcolor="#000066">&nbsp;</td>
  </tr>

<?php
for ($i = 0; $i < count($items); $i++) {
    $key     = key($items);
    $item    = $items[$key];
    $section = $item['section'];

    $item_name = $item['product_name'];

    $type = encode_section_url($item['type_name']);

    $item_link = encode_product_url($item_name, $item['id']);

    $link     = "<A HREF=\"/products/$section/$type/$item_link.html\" target=\"_blank\">";
    $link_end = '</a>';

    if ($i % 2 == 0) {
        $row_color = '#EEEEEE';
    } else {
        $row_color = '#FFFFFF';
    }

    if ($item['time_available'] < time() - 86400) //past
    {
        if ($i % 2 == 0) {
            $row_color = '#FFEBCC';
        } else {
            $row_color = '#FFF7EB';
        }

    } else if (date('d M,Y', $item['time_available']) == date('d M,Y', time())) //current
    {
        if ($i % 2 == 0) {
            $row_color = '#E0F5CC';
        } else {
            $row_color = '#F3FBEB';
        }

    } else if ($item['time_available'] > time()) //future
    {
        if ($i % 2 == 0) {
            $row_color = '#EBF5FF';
        } else {
            $row_color = '#F7FBFF';
        }

    }
?>

  <tr bgcolor="<?=$row_color;?>">
    <td><?=$link . $item_name . $link_end;?></td>
    <td><?=date('d M, Y', strtotime($item['date_available']));?></td>
    <td align="center"><?=$item['total'];?></td>

    <td nowrap><a href="javascript:;" onClick="javascript:MM_openBrWindow('product_notify_view_emails.php?id=<?=$item['id'];?>&sent=<?=$sent;?>','asd','scrollbars=yes,resizable=yes,width=400,height=400')">View Emails</a>
<?php if ('yes' != $sent) {?>
	| <a href="product_notify_send_emails.php?id=<?=$item['id'];?>">Send Emails</a>
<?php }?>
	</td>
  </tr>
<?php next($items);
}?>
</table>
<?php
include_once __DIR__ . '/footer.inc.php';
