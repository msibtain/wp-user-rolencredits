<h3>User Role and Credits</h3>
<table class="form-table">
    <?php if (count($wpwcRoles)) { ?>
    <tr>
        <th><label for="custom_field"><?php echo __('Chose Package'); ?></label></th>
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
</table>