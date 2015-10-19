<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

/**
 * Integration Package
 *
 * @package Includes
 * @subpackage Integration
 */

if (!class_exists('wp_adpress_register_style')) {
    class wp_adpress_register_style
    {
        /**
         * @var string
         */
        public $style_id;

        /**
         * @var string
         */
        public $style_nice_name;

        /**
         * @var array
         */
        public $css_classes;

        /**
         * @var array
         */
        public $inline_styles;

        /**
         * Usage:
         *
         * $css_classes = array (
         *      'list' => array('first_class', 'second_class'),
         *      'item' => array('first_class', 'second_class')
         * );
         * $inline_styles = array (
         *      'list' => 'width: 500px; height:350px;',
         *      'item' => 'height: 200px; width 120px;'
         * );
         * @param $style_name string
         * @param $style_nice_name string
         * @param $css_classes array
         * @param $inline_styles array
         */
        function __construct($style_id, $style_nice_name, $css_classes = array(), $inline_styles = array())
        {
            $this->style_id = $style_id;
            $this->style_nice_name = $style_nice_name;
            $this->css_classes = $css_classes;
            $this->inline_styles = $inline_styles;
            $this->register_style();
        }

        function register_style()
        {
            $style = array(
                'id' => $this->style_id,
                'nice_name' => $this->style_nice_name,
                'css_classes' => $this->css_classes,
                'inline_styles' => $this->inline_styles
            );

            $adpress_int = get_option('wp_adpress_integration', array());
            $adpress_int[$this->style_id] = $style;
            update_option('wp_adpress_integration', $adpress_int);
        }
    }
}

if (!class_exists('wp_adpress_load_style')) {
    class wp_adpress_load_style
    {
        private $style;
        public $el;

        /**
         * @param $style_id
         * @param $el
         * @return false|null
         */
        function __construct($style_id, $el)
        {
            if (!$this->get_style($style_id) || $style_id === '') {
                return false;
            }

            $this->get_el($el);
        }

        /**
         * @param $style
         * @return bool
         */
        private function get_style($style)
        {
            $adpress_int = get_option('wp_adpress_integration', array());
            if (array_key_exists($style, $adpress_int)) {
                $this->style = $adpress_int[$style];
                return true;
            } else {
                return false;
            }
        }

        /**
         * @param $el
         * @return bool|string
         */
        private function get_el($el)
        {
            $result = '';
            switch ($el) {
                case 'list_class':
                    if (array_key_exists('list', $this->style['css_classes'])) {
                        $result = implode(' ', $this->style['css_classes']['list']);
                    }
                    break;
                case 'list_inline':
                    if (array_key_exists('list', $this->style['inline_styles'])) {
                        $result = $this->style['inline_styles']['list'];
                    }
                    break;
            }
            $this->el = $result;
        }

        static function widget_print_styles($selected)
        {
            $int = get_option('wp_adpress_integration', array());
            $html = '<option name="">None</option>';
            foreach ($int as $styles => $style) {
                if ($selected === $style['id']) {
                    $html .= '<option value="' . $style['id'] . '" selected>' . $style['nice_name'] . '</option>';
                } else {
                    $html .= '<option value="' . $style['id'] . '">' . $style['nice_name'] . '</option>';
                }
            }

            return $html;
        }

        static function remove_style($style)
        {
            $int = get_option('wp_adpress_integration', array());
            if (array_key_exists($style, $int)) {
                unset($int[$style]);
            }
            update_option('wp_adpress_integration', $int);
        }

        static function remove_all_styles()
        {
            update_option('wp_adpress_integration', array());
        }
    }
}

/**
 * Helper function for the wp_adpress_load_style class
 *
 * @param string $style
 * @param string $el
 * @return string|wp_adpress_load_style
 */
function get_style_el($style, $el)
{
    $el = new wp_adpress_load_style($style, $el);
    return $el->el;
}