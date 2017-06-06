    <div class="extra-fields">
        <p class="form-row form-row-first">
            <label for="first_name">First Name<span class="required">*</span></label>
            <input type="text" class="input-text" name="first_name" id="first_name" value="<?php if ( ! empty( $_POST['first_name'] ) ) esc_attr_e( $_POST['first_name'] ); ?>" />
        </p>

        <p class="form-row form-row-last">
            <label for="last_name">Last Name<span class="required">*</span></label>
            <input type="text" class="input-text" name="last_name" id="last_name" value="<?php if ( ! empty( $_POST['last_name'] ) ) esc_attr_e( $_POST['last_name'] ); ?>" />
        </p>
        <div class="clear"></div>
    </div>