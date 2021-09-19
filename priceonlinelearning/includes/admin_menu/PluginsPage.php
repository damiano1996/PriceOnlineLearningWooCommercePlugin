<?php

class PluginsPage
{

    public function init()
    {
        add_filter(
            'plugin_action_links_' . plugin_basename(POL_PLUGIN_FILE),
            array($this, 'plugin_page_settings_links')
        );

        register_activation_hook(POL_PLUGIN_FILE, array($this, 'plugin_activation_notice'));
        add_action('admin_notices', array($this, 'admin_notice_plugin_activation'));

    }

    public function plugin_page_settings_links($links)
    {
        // Build and escape the URL.
        $url = esc_url(add_query_arg(
            'page',
            'priceonlinelearning-settings',
            get_admin_url() . 'admin.php'
        ));
        // Create the link.
        $settings_link = "<a href='$url'>" . __('Settings') . '</a>';
        // Adding the setting link
        array_unshift(
            $links,
            $settings_link
        );
        return $links;
    }

    public function plugin_activation_notice()
    {
        set_transient('plugin-activation-notice', true, 5);
    }

    public function admin_notice_plugin_activation()
    {

        /* Check transient, if available display notice */
        if (get_transient('plugin-activation-notice')) {

            // Is woocommerce active?
            if (POLUtils::is_woocommerce_active()) {
                ?>
                <div class="updated notice is-dismissible">
                    <p><b>Price Online Learning</b> has been successfully activated!<br>
                        You can proceed with the <a
                                href="admin.php?page=priceonlinelearning-settings"><b>configurations</b></a>.</p>
                </div>
                <?php
            } else {
                ?>
                <div class="notice notice-warning is-dismissible">
                    <p>
                        <b>WooCommerce</b> is not active...
                        <br>
                        <b>Price Online Learning</b> has been activated, but it won't work properly.
                        <br>
                        Please, activate <b>WooCommerce</b>.
                    </p>
                </div>
                <?php
            }

            // Is server to server communication active?
            if (!POLUtils::is_server_to_server_communication_available()) {
                ?>
                <div class="notice notice-error is-dismissible">
                    <p>
                        Server to server communication is not available for <b>Price Online Learning</b>.
                        <br>
                        Please, active it.
                    </p>
                </div>
                <?php
            }

            /* Delete transient, only display this notice once. */
            delete_transient('plugin-activation-notice');
        }
    }

}