<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PSEO_Admin {

    public function __construct() {
        add_action( 'admin_menu',            [ $this, 'register_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'admin_init',            [ $this, 'handle_db_rebuild' ] );
        add_action( 'admin_notices',         [ $this, 'admin_notices' ] );
    }

    /* ── Menu ─────────────────────────────────────────────── */
    public function register_menu(): void {
        add_menu_page(
        __( 'PSEO PRO by KNR', 'pseo-pro-knr' ),
        __( 'PSEO PRO', 'pseo-pro-knr' ),
        'manage_options',
        'pseo',
        [ $this, 'page_projects' ],
        PSEO_PLUGIN_URL . 'admin/images/icon.png',
        30
        );

        add_submenu_page( 'pseo', __( 'All Projects', 'pseo-pro-knr' ),     __( 'All Projects', 'pseo-pro-knr' ),   'manage_options', 'pseo-pro-knr',              [ $this, 'page_projects' ] );
        add_submenu_page( 'pseo', __( 'New / Edit Project', 'pseo-pro-knr'),__( '+ New Project', 'pseo-pro-knr' ),  'manage_options', 'pseo-project-edit', [ $this, 'page_project_edit' ] );
        add_submenu_page( 'pseo', __( 'Settings', 'pseo-pro-knr' ),         __( 'Settings', 'pseo-pro-knr' ),       'manage_options', 'pseo-settings',     [ $this, 'page_settings' ] );
    }

    /* ── Assets ───────────────────────────────────────────── */
    public function enqueue_assets( string $hook ): void {
        if ( strpos( $hook, 'pseo-pro-knr' ) === false ) return;

        wp_enqueue_style( 'pseo-admin', PSEO_PLUGIN_URL . 'admin/css/pseo-admin.css', [], PSEO_VERSION );
        wp_enqueue_script( 'pseo-admin', PSEO_PLUGIN_URL . 'admin/js/pseo-admin.js', [ 'jquery' ], PSEO_VERSION, true );

        wp_localize_script( 'pseo-admin', 'pseo-pro-knr', [
            'ajax_url'       => admin_url( 'admin-ajax.php' ),
            'nonce'          => wp_create_nonce( 'pseo_nonce' ),
            'confirm_delete' => __( 'Delete this project and ALL its generated pages? This cannot be undone.', 'pseo-pro-knr' ),
            'confirm_pages'  => __( 'Delete all generated pages for this project?', 'pseo-pro-knr' ),
            'generating'     => __( 'Generating…', 'pseo-pro-knr' ),
            'generate'       => __( 'Generate', 'pseo-pro-knr' ),
            'saved'          => __( 'Project saved!', 'pseo-pro-knr' ),
        ] );
    }

    /* ── DB rebuild handler ───────────────────────────────── */
    public function handle_db_rebuild(): void {
        if (
            isset( $_GET['pseo_rebuild'] ) &&
            current_user_can( 'manage_options' ) &&
            check_admin_referer( 'pseo_rebuild' )
        ) {
            PSEO_Database::install();
            wp_safe_redirect( admin_url( 'admin.php?page=pseo-settings&pseo_rebuilt=1' ) );
            exit;
        }
    }

    /* ── Admin notices ────────────────────────────────────── */
    public function admin_notices(): void {
        if ( isset( $_GET['pseo_rebuilt'] ) ) {
            echo '<div class="notice notice-success is-dismissible"><p>'
                . esc_html__( 'Programmatic SEO: database tables rebuilt successfully.', 'pseo-pro-knr' )
                . '</p></div>';
        }
    }

    /* ── Page renderers ───────────────────────────────────── */
    public function page_projects(): void   { require_once PSEO_PLUGIN_DIR . 'admin/views/page-projects.php'; }
    public function page_project_edit(): void { require_once PSEO_PLUGIN_DIR . 'admin/views/page-project-edit.php'; }
    public function page_settings(): void   { require_once PSEO_PLUGIN_DIR . 'admin/views/page-settings.php'; }
}
