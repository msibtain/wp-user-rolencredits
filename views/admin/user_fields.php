<h3>User Role and Credits</h3>
<table class="form-table" cellpadding="5">
    <?php if (count($wpwcRoles)) { ?>
    <tr style="border: 1px solid #fff;">
        <th style="padding-left: 10px;"><label for="custom_field"><?php echo __('Choose Package'); ?></label></th>
        <td>
            <select name="es_user_role" id="es_user_role">
                <option value=""><?php echo __('Select Package'); ?></option>
                <?php foreach ($wpwcRoles as $key => $value) { ?>
                    <option <?php if (in_array($key, $user->roles)) { echo "selected"; } ?>  value="<?php echo $key; ?>"><?php echo $value; ?></option>
                <?php } ?>
            </select>
        </td>
    </tr>
    <?php } ?>
    <tr style="border: 1px solid #fff;">
        <th style="padding-left: 10px;"><label for="custom_field"><?php echo __('User Credits'); ?></label></th>
        <td>
            <input type="text" name="es_user_credits" id="es_user_credits" />
        </td>
    </tr>
    
</table>