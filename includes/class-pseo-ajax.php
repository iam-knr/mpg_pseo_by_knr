<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PSEO_Ajax {

    public function __construct() {
        foreach ( [ 'pseo_generate', 'pseo_delete_pages', 'pseo_preview_data', 'pseo_save_project', 'pseo_delete_project' ] as $a ) {
            add_action( "wp_ajax_{$a}", [ $this, str_replace( 'pseo_', '', $a ) ] );
        }
    }

    /**
     * Verifies capability and nonce before any AJAX action.
     * All public handlers call this first — nonce is verified here via check_ajax_referer().
     */
    private function verify(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Unauthorized.' ], 403 );
        }
        check_ajax_referer( 'pseo_nonce', 'nonce' );
    }

    public function generate(): void {
        $this->verify();
        // phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified in $this->verify()
        $project_id     = absint( wp_unslash( $_POST['project_id'] ?? 0 ) );
        $delete_orphans = ! empty( $_POST['delete_orphans'] );
        // phpcs:enable WordPress.Security.NonceVerification.Missing
        wp_send_json_success( PSEO_Generator::run( $project_id, $delete_orphans ) );
    }

    public function delete_pages(): void {
        $this->verify();
        // phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified in $this->verify()
        $project_id = absint( wp_unslash( $_POST['project_id'] ?? 0 ) );
        // phpcs:enable WordPress.Security.NonceVerification.Missing
        wp_send_json_success( [ 'deleted' => PSEO_Generator::delete_generated( $project_id ) ] );
    }

    public function preview_data(): void {
        $this->verify();
        // phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified in $this->verify()
        $project_id = absint( wp_unslash( $_POST['project_id'] ?? 0 ) );
        // phpcs:enable WordPress.Security.NonceVerification.Missing
        $project = PSEO_Database::get_project( $project_id );
        if ( ! $project ) {
            wp_send_json_error( [ 'message' => 'Project not found.' ] );
        }
        $rows = PSEO_DataSource::fetch( $project );
        wp_send_json_success( [
            'count'   => count( $rows ),
            'preview' => array_slice( $rows, 0, 5 ),
            'columns' => array_keys( $rows[0] ?? [] ),
        ] );
    }

    public function save_project(): void {
        $this->verify();
        // phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified in $this->verify()
        $data = [
            'name'          => sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) ),
            'post_type'     => sanitize_key( wp_unslash( $_POST['post_type'] ?? 'page' ) ),
            'template_id'   => absint( wp_unslash( $_POST['template_id'] ?? 0 ) ),
            'source_type'   => sanitize_key( wp_unslash( $_POST['source_type'] ?? 'csv_url' ) ),
            'source_config' => wp_json_encode( json_decode( wp_unslash( $_POST['source_config'] ?? '{}' ), true ) ),
            'url_pattern'   => sanitize_text_field( wp_unslash( $_POST['url_pattern'] ?? '' ) ),
            'seo_title'     => sanitize_text_field( wp_unslash( $_POST['seo_title'] ?? '' ) ),
            'seo_desc'      => sanitize_textarea_field( wp_unslash( $_POST['seo_desc'] ?? '' ) ),
            'robots'        => sanitize_text_field( wp_unslash( $_POST['robots'] ?? 'index,follow' ) ),
            'schema_type'   => sanitize_text_field( wp_unslash( $_POST['schema_type'] ?? '' ) ),
            'sync_interval' => sanitize_key( wp_unslash( $_POST['sync_interval'] ?? 'manual' ) ),
        ];
        if ( ! empty( $_POST['id'] ) ) {
            $data['id'] = absint( wp_unslash( $_POST['id'] ) );
        }
        // phpcs:enable WordPress.Security.NonceVerification.Missing
        wp_send_json_success( [ 'id' => PSEO_Database::save_project( $data ) ] );
    }

    public function delete_project(): void {
        $this->verify();
        // phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified in $this->verify()
        $id = absint( wp_unslash( $_POST['project_id'] ?? 0 ) );
        // phpcs:enable WordPress.Security.NonceVerification.Missing
        PSEO_Generator::delete_generated( $id );
        PSEO_Database::delete_project( $id );
        wp_send_json_success();
    }
}
