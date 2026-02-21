<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PSEO_Database {

    public static function install(): void {
        global $wpdb;
        $charset = $wpdb->get_charset_collate();
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta( "CREATE TABLE {$wpdb->prefix}pseo_projects (
            id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name          VARCHAR(255)    NOT NULL DEFAULT '',
            post_type     VARCHAR(100)    NOT NULL DEFAULT 'page',
            template_id   BIGINT UNSIGNED NOT NULL DEFAULT 0,
            source_type   VARCHAR(50)     NOT NULL DEFAULT 'csv',
            source_config LONGTEXT        NOT NULL DEFAULT '',
            url_pattern   VARCHAR(500)    NOT NULL DEFAULT '',
            seo_title     VARCHAR(500)    NOT NULL DEFAULT '',
            seo_desc      VARCHAR(500)    NOT NULL DEFAULT '',
            robots        VARCHAR(100)    NOT NULL DEFAULT 'index,follow',
            schema_type   VARCHAR(100)    NOT NULL DEFAULT '',
            status        VARCHAR(20)     NOT NULL DEFAULT 'active',
            sync_interval VARCHAR(50)     NOT NULL DEFAULT 'manual',
            created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset;" );

        dbDelta( "CREATE TABLE {$wpdb->prefix}pseo_data_rows (
            id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            project_id BIGINT UNSIGNED NOT NULL,
            row_hash   VARCHAR(64)     NOT NULL DEFAULT '',
            row_data   LONGTEXT        NOT NULL DEFAULT '',
            created_at DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY project_id (project_id),
            KEY row_hash (row_hash)
        ) $charset;" );

        dbDelta( "CREATE TABLE {$wpdb->prefix}pseo_pages (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            project_id  BIGINT UNSIGNED NOT NULL,
            data_row_id BIGINT UNSIGNED NOT NULL,
            post_id     BIGINT UNSIGNED NOT NULL,
            url_slug    VARCHAR(500)    NOT NULL DEFAULT '',
            created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY project_id (project_id),
            KEY post_id (post_id)
        ) $charset;" );

        update_option( 'pseo_db_version', PSEO_VERSION );
    }

    public static function get_project( int $id ): ?object {
        global $wpdb;
        return $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pseo_projects WHERE id = %d", $id )
        );
    }

    public static function get_projects(): array {
        global $wpdb;
        return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}pseo_projects ORDER BY created_at DESC" );
    }

    public static function save_project( array $data ): int {
        global $wpdb;
        $table = $wpdb->prefix . 'pseo_projects';
        if ( ! empty( $data['id'] ) ) {
            $id = (int) $data['id'];
            unset( $data['id'] );
            $wpdb->update( $table, $data, [ 'id' => $id ] );
            return $id;
        }
        $wpdb->insert( $table, $data );
        return (int) $wpdb->insert_id;
    }

    public static function delete_project( int $id ): void {
        global $wpdb;
        $wpdb->delete( $wpdb->prefix . 'pseo_projects',  [ 'id'         => $id ] );
        $wpdb->delete( $wpdb->prefix . 'pseo_data_rows', [ 'project_id' => $id ] );
        $wpdb->delete( $wpdb->prefix . 'pseo_pages',     [ 'project_id' => $id ] );
    }

    public static function save_data_rows( int $project_id, array $rows ): void {
        global $wpdb;
        $table = $wpdb->prefix . 'pseo_data_rows';
        $wpdb->delete( $table, [ 'project_id' => $project_id ] );
        foreach ( $rows as $row ) {
            $wpdb->insert( $table, [
                'project_id' => $project_id,
                'row_hash'   => md5( json_encode( $row ) ),
                'row_data'   => json_encode( $row ),
            ] );
        }
    }

    public static function get_data_rows( int $project_id ): array {
        global $wpdb;
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}pseo_data_rows WHERE project_id = %d ORDER BY id ASC",
                $project_id
            )
        );
        return array_map( fn( $r ) => json_decode( $r->row_data, true ) + [ '__row_id' => $r->id ], $rows );
    }

    public static function record_generated_page( int $project_id, int $row_id, int $post_id, string $slug ): void {
        global $wpdb;
        $wpdb->replace( $wpdb->prefix . 'pseo_pages', [
            'project_id'  => $project_id,
            'data_row_id' => $row_id,
            'post_id'     => $post_id,
            'url_slug'    => $slug,
        ] );
    }

    public static function get_generated_page_ids( int $project_id ): array {
        global $wpdb;
        return $wpdb->get_col(
            $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}pseo_pages WHERE project_id = %d", $project_id )
        );
    }
}
