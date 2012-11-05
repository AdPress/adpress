<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

class wp_adpress_mu_main
{
    public $blogs_count;
    public $table;

    function __construct()
    {
        // Get command
        if (isset($_GET['action'])) {
            $this->execute_command($_GET['action'], $_GET['blogid']);
        }

        // Blogs Count
        $this->blogs_count = $this->get_blogs_count();

        // Table View
        $this->table = $this->get_blogs_table();
    }

    private function get_blogs_count()
    {
        $count = count(wp_adpress_mu::list_blogs());
        return $count;
    }

    private function get_blogs_table()
    {
        $rows = '';
        $blogs = wp_adpress_mu::list_blogs();
        foreach ($blogs as $blogid) {
            $blog = new wp_adpress_mu($blogid);
            if ($blog->get_admin_panel_status() === 'true') {
                $btn_text = 'Enable';
                $btn_action = 'blog_enable';
            } else {
                $btn_text = 'Disable';
                $btn_action = 'blog_disable';
            }
            $rows .= '<tr>';
            $rows .= '<th>' . $blogid . '</th>';
            $rows .= '<td><a href="' . $blog->blogurl . '">' . $blog->blogname . '</a></td>';
            $rows .= '<td class="buttons" style="width:120px;"><a href="admin.php?page=adpress-mu-main&action=' . $btn_action . '&blogid=' . $blogid . '" class="button-primary">' . $btn_text . '</a></td>';
            $rows .= '<td class="buttons" style="width:120px;"><a href="' . $blog->blogurl . '/wp-admin/admin.php?page=adpress-campaigns" class="button-primary black" target="_blank">Dashboard</a></td>';
            $rows .= '</tr>';
        }

        $table = '
                    <table class="wp-list-table widefat plugins" cellspacing="0">
            <thead>
                <tr>
                    <th class="id-column">' . __('Id', 'wp-adpress') . '</th>
                    <th>' . __('Blog', 'wp-adpress') . '</th>
                    <th>' . __('Admin access', 'wp-adpress') . '</th>
                    <th>' . __('Dashboard', 'wp-adpress') . '</th>
                </tr>
            </thead>
            <tbody class="highlight_rows">
                ' . $rows . '
            </tbody>
            <tfoot>
                <tr>
                    <th class="id-column">' . __('Id', 'wp-adpress') . '</th>
                    <th>' . __('Blog', 'wp-adpress') . '</th>
                    <th>' . __('Admin access', 'wp-adpress') . '</th>
                    <th>' . __('Dashboard', 'wp-adpress') . '</th>
                </tr>
            </tfoot>
        </table>';
        return $table;
    }

    private function execute_command($cmd, $blogid)
    {
        switch ($cmd)
        {
            case 'blog_enable':
                $blog = new wp_adpress_mu($blogid);
                $blog->restrict_admin_panel('false');
                break;
            case 'blog_disable':
                $blog = new wp_adpress_mu($blogid);
                $blog->restrict_admin_panel('true');
                break;
        }
    }
}