<div id="printAuthOverlay" class="fbm-modal-overlay">
    <div class="fbm-modal">

        <div class="fbm-modal-header">
            <h3>Salesman Authentication</h3>
            <i class="fa-solid fa-xmark" id="closeAuthPopup"></i>
        </div>

        <div class="fbm-modal-body">
            <div class="auth-user-wrapper">
                <label for="auth_user_id">Salesman</label>
                <select id="auth_user_id">
                    <option value="">Select salesman</option>
                    <?php
                        $current_user_id = get_current_user_id();
                        $users = get_users();
                        foreach ($users as $user) {
                            $selected = ($user->ID === $current_user_id) ? 'selected' : '';
                            echo '<option value="' . esc_attr($user->ID) . '" ' . $selected . '>' . esc_html($user->display_name) . '</option>';
                        }
                    ?>
                </select>
            </div>

            <div class="auth-password-wrapper">
                <input type="password" id="auth_pincode" inputmode="numeric" pattern="[0-9]*" placeholder="Enter Pincode">
                <i class="fa-solid fa-eye" id="toggleAuthPassword"></i>
            </div>
            <small id="auth_error_message" class="auth-error-message" style="display:none;"></small>
        </div>

        <div class="fbm-modal-footer">
            <button id="authConfirm" class="fbm-btn primary">Confirm</button>
            <button id="authCancel" class="fbm-btn secondary">Cancel</button>
        </div>

    </div>
</div>
