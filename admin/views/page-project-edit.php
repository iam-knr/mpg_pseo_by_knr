<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$project_id   = (int) ( $_GET['id'] ?? 0 );
$project      = $project_id ? PSEO_Database::get_project( $project_id ) : null;
$config       = $project ? ( json_decode( $project->source_config, true ) ?: [] ) : [];
$template_posts = get_posts( [ 'post_type' => [ 'page', 'post' ], 'numberposts' => -1,
                                'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC' ] );
$post_types   = get_post_types( [ 'public' => true ], 'objects' );
?>
<div class="wrap pseo-wrap">

    <h1>
        <?php echo $project
            ? esc_html__( 'Edit Project', 'pseo' ) . ' — <em>' . esc_html( $project->name ) . '</em>'
            : esc_html__( 'New Project', 'pseo' ); ?>
    </h1>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=pseo' ) ); ?>" class="page-title-action">
        ← <?php esc_html_e( 'All Projects', 'pseo' ); ?>
    </a>
    <hr class="wp-header-end">

    <form id="pseo-project-form" class="pseo-form" novalidate>
        <?php wp_nonce_field( 'pseo_nonce', 'nonce' ); ?>
        <input type="hidden" name="id" value="<?php echo $project_id; ?>">

        <!-- CARD 1 – Basic Details -->
        <div class="pseo-card">
            <h2 class="pseo-card__title">
                <span class="dashicons dashicons-admin-settings"></span>
                <?php esc_html_e( 'Project Details', 'pseo' ); ?>
            </h2>
            <table class="form-table">
                <tr>
                    <th><label for="pseo-name"><?php esc_html_e( 'Project Name', 'pseo' ); ?> *</label></th>
                    <td>
                        <input id="pseo-name" type="text" name="name" class="regular-text"
                               value="<?php echo esc_attr( $project->name ?? '' ); ?>"
                               placeholder="e.g. Plumber Pages by City" required>
                    </td>
                </tr>
                <tr>
                    <th><label><?php esc_html_e( 'Generate as Post Type', 'pseo' ); ?></label></th>
                    <td>
                        <select name="post_type">
                            <?php foreach ( $post_types as $pt ) : ?>
                                <option value="<?php echo esc_attr( $pt->name ); ?>"
                                    <?php selected( $project->post_type ?? 'page', $pt->name ); ?>>
                                    <?php echo esc_html( $pt->label ); ?> (<?php echo esc_html( $pt->name ); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label><?php esc_html_e( 'Content Template', 'pseo' ); ?></label></th>
                    <td>
                        <select name="template_id">
                            <option value="0"><?php esc_html_e( '— None (blank content) —', 'pseo' ); ?></option>
                            <?php foreach ( $template_posts as $tp ) : ?>
                                <option value="<?php echo $tp->ID; ?>" <?php selected( $project->template_id ?? 0, $tp->ID ); ?>>
                                    <?php echo esc_html( $tp->post_title ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">
                            Syntax: <code>{{city}}</code> value &nbsp;|&nbsp;
                            <code>{{raw:html_col}}</code> unescaped HTML &nbsp;|&nbsp;
                            <code>{Best|Top|Leading}</code> spintax &nbsp;|&nbsp;
                            <code>[if:price>0]text[/if]</code> conditional
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- CARD 2 – Data Source -->
        <div class="pseo-card">
            <h2 class="pseo-card__title">
                <span class="dashicons dashicons-database"></span>
                <?php esc_html_e( 'Data Source', 'pseo' ); ?>
            </h2>
            <table class="form-table">
                <tr>
                    <th><label><?php esc_html_e( 'Source Type', 'pseo' ); ?></label></th>
                    <td>
                        <select id="pseo-source-type" name="source_type">
                            <?php $st = $project->source_type ?? 'csv_url'; ?>
                            <option value="csv_url"       <?php selected( $st, 'csv_url' ); ?>>       📄 CSV via URL</option>
                            <option value="csv_upload"    <?php selected( $st, 'csv_upload' ); ?>>    📤 CSV Upload (server path)</option>
                            <option value="google_sheets" <?php selected( $st, 'google_sheets' ); ?>> 📊 Google Sheets (published)</option>
                            <option value="json_url"      <?php selected( $st, 'json_url' ); ?>>      🔗 JSON URL</option>
                            <option value="rest_api"      <?php selected( $st, 'rest_api' ); ?>>      ⚙️ REST API (paginated)</option>
                        </select>
                    </td>
                </tr>

                <!-- CSV / CSV Upload -->
                <tr class="pseo-source-panel pseo-source-csv_url pseo-source-csv_upload">
                    <th><?php esc_html_e( 'CSV File URL / Server Path', 'pseo' ); ?></th>
                    <td>
                        <input type="text" name="source_config[file_url]" class="large-text"
                               value="<?php echo esc_attr( $config['file_url'] ?? '' ); ?>"
                               placeholder="https://docs.google.com/spreadsheets/d/.../export?format=csv">
                    </td>
                </tr>

                <!-- Google Sheets -->
                <tr class="pseo-source-panel pseo-source-google_sheets">
                    <th><?php esc_html_e( 'Google Sheet ID', 'pseo' ); ?></th>
                    <td>
                        <input type="text" name="source_config[sheet_id]" class="regular-text"
                               value="<?php echo esc_attr( $config['sheet_id'] ?? '' ); ?>"
                               placeholder="1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgVE2upms">
                        <p class="description">The long ID from the sheet URL. Sheet must be "Anyone with link can view".</p>
                    </td>
                </tr>
                <tr class="pseo-source-panel pseo-source-google_sheets">
                    <th><?php esc_html_e( 'Worksheet GID', 'pseo' ); ?></th>
                    <td>
                        <input type="text" name="source_config[gid]" class="small-text"
                               value="<?php echo esc_attr( $config['gid'] ?? '0' ); ?>" placeholder="0">
                        <p class="description">Found in the URL after #gid=. Use 0 for first sheet.</p>
                    </td>
                </tr>

                <!-- JSON URL -->
                <tr class="pseo-source-panel pseo-source-json_url">
                    <th><?php esc_html_e( 'JSON Endpoint URL', 'pseo' ); ?></th>
                    <td>
                        <input type="url" name="source_config[url]" class="large-text"
                               value="<?php echo esc_attr( $config['url'] ?? '' ); ?>"
                               placeholder="https://api.example.com/data.json">
                    </td>
                </tr>
                <tr class="pseo-source-panel pseo-source-json_url">
                    <th><?php esc_html_e( 'Data Path (dot notation)', 'pseo' ); ?></th>
                    <td>
                        <input type="text" name="source_config[path]" class="regular-text"
                               value="<?php echo esc_attr( $config['path'] ?? '' ); ?>" placeholder="data.results">
                        <p class="description">Leave blank if root is the array. Use dot-notation e.g. <code>data.items</code></p>
                    </td>
                </tr>

                <!-- REST API -->
                <tr class="pseo-source-panel pseo-source-rest_api">
                    <th><?php esc_html_e( 'API Base URL', 'pseo' ); ?></th>
                    <td>
                        <input type="url" name="source_config[url]" class="large-text"
                               value="<?php echo esc_attr( $config['url'] ?? '' ); ?>"
                               placeholder="https://api.example.com/v1/listings">
                    </td>
                </tr>
                <tr class="pseo-source-panel pseo-source-rest_api">
                    <th><?php esc_html_e( 'Data Path in Response', 'pseo' ); ?></th>
                    <td>
                        <input type="text" name="source_config[data_path]" class="regular-text"
                               value="<?php echo esc_attr( $config['data_path'] ?? '' ); ?>" placeholder="results">
                    </td>
                </tr>
                <tr class="pseo-source-panel pseo-source-rest_api">
                    <th><?php esc_html_e( 'Pagination', 'pseo' ); ?></th>
                    <td>
                        Page param: <input type="text" name="source_config[page_param]" class="small-text"
                               value="<?php echo esc_attr( $config['page_param'] ?? 'page' ); ?>">
                        &nbsp; Per page: <input type="number" name="source_config[per_page]" class="small-text"
                               value="<?php echo esc_attr( $config['per_page'] ?? '100' ); ?>">
                        &nbsp; Max pages: <input type="number" name="source_config[max_pages]" class="small-text"
                               value="<?php echo esc_attr( $config['max_pages'] ?? '10' ); ?>">
                    </td>
                </tr>
                <tr class="pseo-source-panel pseo-source-rest_api">
                    <th><?php esc_html_e( 'Auth Header (optional)', 'pseo' ); ?></th>
                    <td>
                        <input type="text" name="source_config[headers][Authorization]" class="large-text"
                               value="<?php echo esc_attr( $config['headers']['Authorization'] ?? '' ); ?>"
                               placeholder="Bearer your-token-here">
                    </td>
                </tr>
            </table>
        </div>

        <!-- CARD 3 – URL Structure -->
        <div class="pseo-card">
            <h2 class="pseo-card__title">
                <span class="dashicons dashicons-admin-links"></span>
                <?php esc_html_e( 'URL Structure', 'pseo' ); ?>
            </h2>
            <table class="form-table">
                <tr>
                    <th><label><?php esc_html_e( 'URL Pattern', 'pseo' ); ?></label></th>
                    <td>
                        <div class="pseo-url-preview-wrap">
                            <span class="pseo-url-base"><?php echo esc_html( trailingslashit( home_url() ) ); ?></span>
                            <input type="text" name="url_pattern" class="large-text"
                                   value="<?php echo esc_attr( $project->url_pattern ?? '' ); ?>"
                                   placeholder="{{service}}/{{city}}">
                        </div>
                        <p class="description">
                            <strong>Example:</strong> <code>services/{{service}}/{{city}}</code>
                            → <code>/services/plumbing/bangalore/</code>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- CARD 4 – SEO Meta -->
        <div class="pseo-card">
            <h2 class="pseo-card__title">
                <span class="dashicons dashicons-search"></span>
                <?php esc_html_e( 'SEO Meta', 'pseo' ); ?>
            </h2>
            <table class="form-table">
                <tr>
                    <th><label><?php esc_html_e( 'Title Tag Template', 'pseo' ); ?></label></th>
                    <td>
                        <input type="text" name="seo_title" class="large-text"
                               value="<?php echo esc_attr( $project->seo_title ?? '' ); ?>"
                               placeholder="Best {{service}} in {{city}} | {{brand}}">
                        <p class="description">Recommended: under 60 characters after placeholder substitution.</p>
                    </td>
                </tr>
                <tr>
                    <th><label><?php esc_html_e( 'Meta Description Template', 'pseo' ); ?></label></th>
                    <td>
                        <textarea name="seo_desc" class="large-text" rows="3"
                                  placeholder="Looking for {{service}} in {{city}}? Get free quotes starting from ₹{{price}}."
                        ><?php echo esc_textarea( $project->seo_desc ?? '' ); ?></textarea>
                        <p class="description">Recommended: 120–160 characters.</p>
                    </td>
                </tr>
                <tr>
                    <th><label><?php esc_html_e( 'Robots Directive', 'pseo' ); ?></label></th>
                    <td>
                        <select name="robots">
                            <?php $r = $project->robots ?? 'index,follow'; ?>
                            <option value="index,follow"     <?php selected($r,'index,follow'); ?>>      index, follow (default)</option>
                            <option value="noindex,follow"   <?php selected($r,'noindex,follow'); ?>>    noindex, follow</option>
                            <option value="index,nofollow"   <?php selected($r,'index,nofollow'); ?>>    index, nofollow</option>
                            <option value="noindex,nofollow" <?php selected($r,'noindex,nofollow'); ?>>  noindex, nofollow</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label><?php esc_html_e( 'Schema Markup Type', 'pseo' ); ?></label></th>
                    <td>
                        <select id="pseo-schema-type" name="schema_type">
                            <?php $sc = $project->schema_type ?? ''; ?>
                            <option value=""              <?php selected($sc,''); ?>>             None</option>
                            <option value="Article"       <?php selected($sc,'Article'); ?>>      Article — blog posts, guides</option>
                            <option value="LocalBusiness" <?php selected($sc,'LocalBusiness'); ?>>LocalBusiness — service + city pages</option>
                            <option value="Product"       <?php selected($sc,'Product'); ?>>      Product — ecommerce pages</option>
                            <option value="FAQPage"       <?php selected($sc,'FAQPage'); ?>>      FAQPage — FAQ pages</option>
                            <option value="BreadcrumbList"<?php selected($sc,'BreadcrumbList'); ?>>BreadcrumbList — all pages</option>
                            <option value="JobPosting"    <?php selected($sc,'JobPosting'); ?>>   JobPosting — job listing pages</option>
                        </select>
                        <div id="pseo-schema-hint" class="pseo-schema-hint" style="display:none;"></div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- CARD 5 – Auto-Sync -->
        <div class="pseo-card">
            <h2 class="pseo-card__title">
                <span class="dashicons dashicons-update"></span>
                <?php esc_html_e( 'Auto-Sync Settings', 'pseo' ); ?>
            </h2>
            <table class="form-table">
                <tr>
                    <th><label><?php esc_html_e( 'Sync Interval', 'pseo' ); ?></label></th>
                    <td>
                        <select name="sync_interval">
                            <?php $si = $project->sync_interval ?? 'manual'; ?>
                            <option value="manual" <?php selected($si,'manual'); ?>>🖱 Manual only</option>
                            <option value="hourly" <?php selected($si,'hourly'); ?>>⏱ Hourly</option>
                            <option value="daily"  <?php selected($si,'daily'); ?>> 📅 Daily</option>
                            <option value="weekly" <?php selected($si,'weekly'); ?>>📆 Weekly</option>
                        </select>
                        <p class="description">Auto re-fetches data source and updates pages via WP Cron.</p>
                    </td>
                </tr>
                <tr>
                    <th><label><?php esc_html_e( 'Delete Orphaned Pages', 'pseo' ); ?></label></th>
                    <td>
                        <label>
                            <input type="checkbox" name="delete_orphans" value="1"
                                   <?php checked( $project->delete_orphans ?? 0, 1 ); ?>>
                            <?php esc_html_e( 'Auto-delete pages whose data row was removed from the source.', 'pseo' ); ?>
                        </label>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Submit Bar -->
        <div class="pseo-submit-bar">
            <button type="submit" class="button button-primary button-large">
                💾 <?php esc_html_e( 'Save Project', 'pseo' ); ?>
            </button>
            <?php if ( $project_id ) : ?>
                <button type="button" class="button button-large pseo-btn-generate" data-id="<?php echo $project_id; ?>">
                    ⚡ <?php esc_html_e( 'Generate Pages Now', 'pseo' ); ?>
                </button>
                <button type="button" class="button button-large pseo-btn-preview" data-id="<?php echo $project_id; ?>">
                    👁 <?php esc_html_e( 'Preview Data', 'pseo' ); ?>
                </button>
                <button type="button" class="button button-link-delete pseo-btn-delete-project" data-id="<?php echo $project_id; ?>">
                    🗑 <?php esc_html_e( 'Delete Project', 'pseo' ); ?>
                </button>
            <?php endif; ?>
        </div>
    </form>

    <!-- Preview Modal -->
    <div id="pseo-preview-modal" class="pseo-modal" style="display:none;" role="dialog">
        <div class="pseo-modal-inner">
            <div class="pseo-modal-header">
                <h2><?php esc_html_e( 'Data Preview (first 5 rows)', 'pseo' ); ?></h2>
                <button class="pseo-modal-close">&times;</button>
            </div>
            <div id="pseo-preview-content"></div>
        </div>
    </div>
    <div id="pseo-notice" class="pseo-notice" style="display:none;" role="alert"></div>

</div>