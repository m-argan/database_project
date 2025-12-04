<?php

// --- COPILOT GENERATED ---


/**
 * Helpers to render view pages inside the site's HTML/CSS template.
 *
 * Usage in a view page:
 *   require_once __DIR__ . '/display_views_tools.php';
 *   start_view_capture();
 *   // ... view page emits its HTML and runs any queries
 *   finish_view_capture_and_render($conn);
 *
 * The helper will capture the view's output and pass it to
 * `render_display_table_page()` (if available) so the page gets the
 * site's header/sidebar/footer and `nav.css` styling.
 */

require_once __DIR__ . '/display_table_tools.php';

if (!function_exists('start_view_capture')) {
    function start_view_capture(): void {
        if (!ob_get_level()) {
            ob_start();
        } else {
            // Start an additional buffer level so nested captures are safe
            ob_start();
        }
    }
}

if (!function_exists('finish_view_capture_and_render')) {
    /**
     * End the view capture and render it inside the site's template.
     *
     * @param mysqli $conn Active DB connection (used by sidebar/header)
     * @param bool $is_init If true, render homepage content path in template
     */
    function finish_view_capture_and_render($conn, bool $is_init = false, $is_stu): void {
        // Safely get content from the most recent buffer
        $content = '';
        if (ob_get_level()) {
            $content = ob_get_clean();
        }

        // If the main page renderer exists, use it to wrap the content
        if (function_exists('render_display_table_page')) {
            // When running under CLI (tests) it's often preferable to return
            // the raw content so tests can assert on it without triggering
            // sidebar DB queries. Respect CLI environment by echoing content.
            if (php_sapi_name() === 'cli') {
                // For CLI/tests echo the captured content so tests that use
                // `ob_start()` can capture it. We rely on the test bootstrap
                // to buffer output so header() calls won't fail.
                echo $content;
                return;
            }

            // Normal web path: render full page with header/sidebar/footer
            if($is_stu == false){
            render_display_table_page($conn, $is_init, $content);
            return;}
            elseif($is_stu == true){
                render_display_table_page_student($conn, $is_init, $content);
                return;}
            }
        }

        // Fallback: just output the captured content
        echo $content;
    }
}

if (!function_exists('finish_view_capture_and_return')) {
    /**
     * End capture and return the captured content string (without rendering).
     * Useful for tests or callers that want to inspect the HTML first.
     *
     * @return string
     */
    function finish_view_capture_and_return(): string {
        if (ob_get_level()) {
            return ob_get_clean();
        }
        return '';
    }
}

?>
