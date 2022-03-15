<?php
if (!defined('WPINC')) {
    die;
}

$this->meta_form->buy_pro(
    array(
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'link' => 'https://logichunt.com/product/wordpress-logo-slider/',
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Column Gap', $this->plugin_name ),
        'desc'      => __( 'Sets the gap between the columns.', $this->plugin_name ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_grid_column_gap]',
        'id'        => 'lgx_grid_column_gap',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '15px'
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Row Gap', $this->plugin_name ),
        'desc'      => __( 'Sets the gap between the columns.', $this->plugin_name ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_grid_row_gap]',
        'id'        => 'lgx_grid_row_gap',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '15px'
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Item Min Height', $this->plugin_name ),
        'desc'      => __( 'Set Minimum Height of the item. Default: 0 [Disabled]. Please add your desired height with unit.E.g. 100px/ 10rem / 10em. :  If you do not want this option, just add 0', $this->plugin_name ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_grid_item_min_height]',
        'id'        => 'lgx_grid_item_min_height',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => 0
    )
);

