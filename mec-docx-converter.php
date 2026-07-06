<?php
/**
 * Plugin Name: MEC DOCX CONVERTER
 * Plugin URI:  https://github.com/bungakku/Mec-Docx-Converter
 * Description: Converts semantically marked up .docx documents to clean HTML for pasting from Word, Google Docs, etc.
 * Version:     1.1.0
 * Author:      Biswajit
 * Author URI:  https://github.com/bungakku
 * License:     BSD 2-clause
 * Text Domain: mec-docx-converter
 * Update URI:  https://github.com/bungakku/Mec-Docx-Converter
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// -------------------------------------------------------------------
// 1. Add meta box and enqueue assets (unchanged, but included)
// -------------------------------------------------------------------

add_action( 'add_meta_boxes', 'mec_docx_add_post_meta_box' );
add_action( 'admin_enqueue_scripts', 'mec_docx_admin_assets' );
add_action( 'admin_footer', 'mec_docx_load_scripts' );

function mec_docx_add_post_meta_box() {
    $post_types = get_post_types( array( 'public' => true ) );
    foreach ( $post_types as $post_type ) {
        if ( post_type_supports( $post_type, 'editor' ) ) {
            add_meta_box(
                'mec_docx_converter',
                __( 'MEC DOCX Converter', 'mec-docx-converter' ),
                'mec_docx_render_meta_box',
                $post_type,
                'normal',
                'default'
            );
        }
    }
}

function mec_docx_admin_assets( $hook ) {
    if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
        return;
    }
    wp_enqueue_style(
        'mec-docx-style',
        plugin_dir_url( __FILE__ ) . 'mec-docx.css',
        array(),
        '1.1.0'
    );
}

function mec_docx_render_meta_box( $post ) {
    wp_nonce_field( 'mec_docx_upload', 'mec_docx_nonce' );
    ?>
    <div id="mec-docx-uploader" class="status-empty">
        <div>
            <label for="mec-docx-upload">
                <?php esc_html_e( 'Select docx file:', 'mec-docx-converter' ); ?>
                <input type="file" id="mec-docx-upload" accept=".docx" />
            </label>
        </div>

        <div id="mec-docx-loading">
            <?php esc_html_e( 'Loading...', 'mec-docx-converter' ); ?>
        </div>

        <div id="mec-docx-inserting">
            <?php esc_html_e( 'Inserting...', 'mec-docx-converter' ); ?>
        </div>

        <p class="mec-docx-error">
            <?php esc_html_e( 'Error while attempting to convert file:', 'mec-docx-converter' ); ?>
            <span id="mec-docx-error-message"></span>
        </p>

        <div class="mec-docx-preview">
            <input type="hidden" id="mec-docx-upload-image-nonce" value="<?php echo esc_attr( wp_create_nonce( 'media-form' ) ); ?>" />
            <input type="hidden" id="mec-docx-upload-image-href" value="<?php echo esc_url( admin_url( 'async-upload.php' ) ); ?>" />
            <input type="hidden" id="mec-docx-admin-ajax-href" value="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" />

            <p><input type="button" id="mec-docx-insert" class="button button-primary" value="<?php esc_attr_e( 'Insert into editor', 'mec-docx-converter' ); ?>" /></p>

            <div class="mec-docx-tabs">
                <div class="tab">
                    <h4><?php esc_html_e( 'Visual', 'mec-docx-converter' ); ?></h4>
                    <iframe id="mec-docx-visual-preview"
                            src="about:blank"
                            data-stylesheets="<?php echo esc_attr( implode( ',', get_editor_stylesheets() ) ); ?>">
                    </iframe>
                </div>
                <div class="tab">
                    <h4><?php esc_html_e( 'Raw HTML', 'mec-docx-converter' ); ?></h4>
                    <pre id="mec-docx-raw-preview"></pre>
                </div>
                <div class="tab">
                    <h4><?php esc_html_e( 'Messages', 'mec-docx-converter' ); ?></h4>
                    <div id="mec-docx-messages"></div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function mec_docx_load_scripts() {
    global $post;
    $screen = get_current_screen();
    if ( ! $post || ! in_array( $screen->base, array( 'post', 'post-new' ), true ) ) {
        return;
    }

    wp_enqueue_script(
        'mec-docx-editor',
        plugin_dir_url( __FILE__ ) . 'mec-docx-editor.js',
        array( 'jquery' ),
        '1.1.0',
        true
    );

    wp_enqueue_script(
        'mec-docx-tabs',
        plugin_dir_url( __FILE__ ) . 'mec-docx-tabs.js',
        array(),
        '1.1.0',
        true
    );
}

// -------------------------------------------------------------------
// 2. GitHub Automatic Updater
// -------------------------------------------------------------------

/**
 * Check for updates from GitHub releases.
 */
add_filter( 'pre_set_site_transient_update_plugins', 'mec_docx_check_for_update' );

function mec_docx_check_for_update( $transient ) {
    if ( empty( $transient->checked ) ) {
        return $transient;
    }

    $plugin_slug = plugin_basename( __FILE__ );
    if ( ! function_exists( 'get_plugin_data' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $this_plugin_data = get_plugin_data( __FILE__, false, false );
    $current_version  = $this_plugin_data['Version'] ?? '1.1.0';

    // Get the latest release info from GitHub API (cached for 12 hours).
    $release = get_transient( 'mec_docx_github_release' );
    if ( false === $release ) {
        $url = 'https://api.github.com/repos/bungakku/Mec-Docx-Converter/releases/latest';
        $response = wp_remote_get( $url, array( 'timeout' => 10 ) );

        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
            return $transient;
        }

        $release = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( empty( $release['tag_name'] ) ) {
            return $transient;
        }
        set_transient( 'mec_docx_github_release', $release, 12 * HOUR_IN_SECONDS );
    }

    $latest_version = ltrim( $release['tag_name'], 'v' );
    $download_url = $release['zipball_url'] ?? '';

    if ( version_compare( $latest_version, $current_version, '>' ) ) {
        $transient->response[ $plugin_slug ] = (object) array(
            'slug'        => $plugin_slug,
            'new_version' => $latest_version,
            'url'         => 'https://github.com/bungakku/Mec-Docx-Converter',
            'package'     => $download_url,
            'tested'      => '6.9.4',
            'requires'    => '5.0',
        );
    }

    return $transient;
}

/**
 * Provide plugin information for the "View details" popup.
 */
add_filter( 'plugins_api', 'mec_docx_plugin_info', 10, 3 );

function mec_docx_plugin_info( $res, $action, $args ) {
    if ( 'plugin_information' !== $action ) {
        return $res;
    }

    $plugin_slug = plugin_basename( __FILE__ );
    if ( $args->slug !== $plugin_slug ) {
        return $res;
    }

    // Get release data from cache (same as above).
    $release = get_transient( 'mec_docx_github_release' );
    if ( false === $release ) {
        $url = 'https://api.github.com/repos/bungakku/Mec-Docx-Converter/releases/latest';
        $response = wp_remote_get( $url, array( 'timeout' => 10 ) );
        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
            return $res;
        }
        $release = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( empty( $release['tag_name'] ) ) {
            return $res;
        }
        set_transient( 'mec_docx_github_release', $release, 12 * HOUR_IN_SECONDS );
    }

    $res = new stdClass();
    $res->name          = 'MEC DOCX Converter';
    $res->slug          = $plugin_slug;
    $res->version       = ltrim( $release['tag_name'], 'v' );
    $res->author        = '<a href="https://github.com/bungakku">Biswajit</a>';
    $res->homepage      = 'https://github.com/bungakku/Mec-Docx-Converter';
    $res->download_link = $release['zipball_url'] ?? '';
    $res->sections      = array(
        'description' => 'Converts .docx documents to clean HTML. For details, see the <a href="https://github.com/bungakku/Mec-Docx-Converter">GitHub repository</a>.',
        'changelog'   => 'See the <a href="https://github.com/bungakku/Mec-Docx-Converter/releases">release notes</a> on GitHub.',
    );

    return $res;
}

/**
 * Clear the update cache when the plugin is activated or deactivated.
 */
register_activation_hook( __FILE__, 'mec_docx_clear_update_cache' );
register_deactivation_hook( __FILE__, 'mec_docx_clear_update_cache' );

function mec_docx_clear_update_cache() {
    delete_transient( 'mec_docx_github_release' );
}