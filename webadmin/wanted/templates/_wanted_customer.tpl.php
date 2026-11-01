<?php
moduleLoad('member'); //memberGetOptionList
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;customerdetail&quot;);return false">Customer Deatil</a>')?>
<div id="customerdetail">

	<table class="table_fields">
		 <tr>
            <th style="width:25%;">First Name:</th>
            <td>
                <?php if($action=='edit'){ ?>
                 <div><?php hecho($member['full_name']) ?></div>
             <?php }else{ ?>
                    <input type="text"  id="member_name" name="item[first_name]" value="<?php hecho($member['first_name']) ?>" placeholder="First name" style="width:90%"/>
             <?php } ?>
            </td>
        </tr>
         <?php if($action=='add'){ ?>
        <tr>
            <th style="width:25%;">Last Name:</th>
            <td>
                 <input type="text"  id="member_name" name="item[last_name]" value="<?php hecho($member['last_name']) ?>" placeholder="Last name"  style="width:90%"/>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <th style="width:25%;">Email:</th>
            <td>
            <?php if($action=='edit'){ ?>
                  <div><?php hecho(trim($member['email_address'])); ?></div>
            <?php }else{ ?>
                <input type="text" class="form-control col-sm-9" id="member_email" name="item[email_address]" value="<?php hecho($member['email_address']) ?>" placeholder="Your email address" style="width:90%"/>
            <?php } ?>
             </td>
        </tr>
        <?php if($action=='add'){ ?>
        <tr>
            <th style="width:25%;">Phone Numbers:</th>
            <td>
                <input type="text" class="form-control col-sm-9" id="member_phone" name="item[phone_number]" value="<?php hecho($member['phone_number']) ?>" placeholder="Your contact number" style="width:90%"/>
            </td>
        </tr>
        <?php } ?>
         <tr>
            <th style="width:25%;">Customer Location:</th>
            <td>
                <?php if($action=='add'){ ?>
                    <select class="form-control col-sm-9" id="member_location" name="item[member_location]" style="width:90%;" >
                    <option value=""></option>
                    <?= formatHTMLOptions(memberGetOptionList('country'), $member['country']) ?>
                    </select>
                <?php }else{ ?>
                    <div><?php echo memberGetOptionList('country')[$member['country']]; ?></div>
                <?php } ?>
            </td>
        </tr>
	</table>
</div>