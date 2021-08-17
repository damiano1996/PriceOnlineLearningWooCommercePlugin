<?php


class POL
{

    protected static $_instance = null;

    public static function instance(): ?POL
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        $this->definitions();
        $this->includes();
        $this->init_hooks();
    }

    public function definitions()
    {
        $this->define('POL_ABSPATH', dirname(POL_PLUGIN_FILE) . '/');
    }

    public function includes()
    {
        include_once POL_ABSPATH . 'includes/api/Request.php';
        include_once POL_ABSPATH . 'includes/api/POLApi.php';

        include_once POL_ABSPATH . 'includes/POLUtils.php';
        include_once POL_ABSPATH . 'includes/MyOptions.php';

        include_once POL_ABSPATH . 'includes/admin_menu/PluginsPage.php';
        include_once POL_ABSPATH . 'includes/admin_menu/GeneralSettingsPage.php';

        include_once POL_ABSPATH . 'includes/products/ProductData.php';
        include_once POL_ABSPATH . 'includes/products/ProductPost.php';

        include_once POL_ABSPATH . 'includes/schedules/DailyUpdate.php';
    }

    public function init_hooks()
    {

        $pluginsPage = new PluginsPage();
        $pluginsPage->init();

        $generalSettingsPage = new GeneralSettingsPage();
        $generalSettingsPage->init();

        $productData = new ProductData();
        $productData->init();

        $productPost = new ProductPost();
        $productPost->init();

        $dailyUpdate = new DailyUpdate();
        $dailyUpdate->init();

    }

    private function define($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }

}