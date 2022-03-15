<?php
if (!defined('WPINC')) {
    die;
}

/**
 *
 * Grab Logo Category for lgx_logo_item_category
 *
 */
$lgx_logo_slider_taxo = 'logosliderwpcat';
$lgx_logo_slider_terms = get_terms(
    array(
        'taxonomy' => $lgx_logo_slider_taxo,
        'orderby'  => 'id',
        'hide_empty'=> true,
    )

); // Get all terms of a taxonomy

$lgx_logo_slider_term_array = array(
    'all' => 'All'
);
if ($lgx_logo_slider_terms && !is_wp_error($lgx_logo_slider_terms)) {

    foreach ($lgx_logo_slider_terms as $term) {
        $lgx_logo_slider_term_array[$term->term_id] = $term->name;
    }

}

$this->meta_form->buy_pro(
    array(
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'link' => 'https://logichunt.com/product/wordpress-logo-slider/',
    )
);


$this->meta_form->select(
    array(
        'label'     => __( 'Select Logo Category', $this->plugin_name ),
        'desc'      => __( 'Filter Logo item by category.', $this->plugin_name ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_from_category]',
        'id'        => 'lgx_from_category',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => 'all',
        'options'   => $lgx_logo_slider_term_array
    )
);

$this->meta_form->number(
    array(
        'label'     => __( 'Item Limit', $this->plugin_name ),
        'desc'      => __( 'Number of total logo item to show. Default: 0 ( all ). The free version allows displaying maximum of 20 images.', $this->plugin_name ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_limit]',
        'id'        => 'lgx_item_limit',
        'default'   => 0
    )
);


$this->meta_form->switch(
    array(
        'label' => __( 'Brand Name', $this->plugin_name ),
        'desc' => __( 'Show brand name in your showcase.', $this->plugin_name ),
        'yes_label' => __( 'Show', $this->plugin_name ),
        'no_label' => __( 'Hide', $this->plugin_name ),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_brand_name_en]',
        'id' => 'lgx_brand_name_en',
        'default' => 'no'

    )
);

$this->meta_form->switch(
    array(
        'label' => __( 'Company URL', $this->plugin_name ),
        'yes_label' => __( 'Enabled', $this->plugin_name ),
        'no_label' => __( 'Disabled', $this->plugin_name ),
        'desc' => __( 'Add Custom Company URL in your showcase.', $this->plugin_name ),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_company_url_en]',
        'id' => 'lgx_company_url_en',
        'default' => 'no'
    )
);

$this->meta_form->switch(
    array(
        'label' => __( 'Brand Description', $this->plugin_name ),
        'yes_label' => __( 'Show', $this->plugin_name ),
        'no_label' => __( 'Hide', $this->plugin_name ),
        'desc' => __( 'Show Description in your showcase.', $this->plugin_name ),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_brand_desc_en]',
        'id' => 'lgx_brand_desc_en',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default' => 'no'
    )
);


$this->meta_form->select(
    array(
        'label'     => __( 'Link Target Type', $this->plugin_name ),
        'desc'      => __( 'Specifies where to open the link.', $this->plugin_name ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_target_type]',
        'id'        => 'lgx_target_type',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '_self',
        'options'   => array(
            '_self' => __( 'Same Tab', $this->plugin_name ),
            '_blank' => __( 'New Tab', $this->plugin_name )
        )
    )
);

$this->meta_form->switch(
    array(
        'label' => __( 'Add Nofollow to Link', $this->plugin_name ),
        'yes_label' => __( 'Yes', $this->plugin_name ),
        'no_label' => __( 'No', $this->plugin_name ),
        'desc' => __( 'Basically this is important for site SEO. "nofollow" is used by Google, to specify that the Google search spider should not follow that link.', $this->plugin_name ),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_nofollow_en]',
        'id' => 'lgx_nofollow_en',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default' => 'no'
    )
);


$this->meta_form->text(
    array(
        'label'     => __( 'Image Max Height', $this->plugin_name ),
        'desc'      => __( 'Set Maximum Height of the Logo image. Default: 100% . Please add your desired maximum height with unit.E.g. 100px/ 10rem / 10em.', $this->plugin_name ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_logo_height]',
        'id'        => 'lgx_logo_height',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '100%'
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Image Max Width', $this->plugin_name ),
        'desc'      => __( 'Set Maximum Width of the Logo image. Default: 100%. Please add your desired maximum height with unit.E.g. 100px/ 10rem / 10em.', $this->plugin_name ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_logo_width]',
        'id'        => 'lgx_logo_width',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '100%'
    )
);

/********************************************************************************/
$this->meta_form->header_spacer(
    array(
        'label'     => __( 'Preloader Settings', $this->plugin_name ),
    )
);
/********************************************************************************/

$this->meta_form->buy_pro(
    array(
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'link' => 'https://logichunt.com/product/wordpress-logo-slider/',
    )
);


$this->meta_form->switch(
    array(
        'label' => __( 'Enable Preloader', $this->plugin_name ),
        'yes_label' => __( 'Enabled', $this->plugin_name ),
        'no_label' => __( 'Disabled', $this->plugin_name ),
        'desc' => __( 'The showcase will be invisible until the page load complete.', $this->plugin_name ),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_preloader_en]',
        'id' => 'lgx_preloader_en',
        'default' => 'yes'
    )
);

$this->meta_form->upload(
    array(
        'label'   => __( 'Preloader Icon', $this->plugin_name ),
        'desc'    => __( 'Upload Background Icon for Preloader.', $this->plugin_name ),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_preloader_icon]',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'id'      => 'lgx_preloader_icon',
    )
);

$this->meta_form->color(
    array(
        'label'     => __( 'Preloader Background', $this->plugin_name ),
        'desc'      => __( 'Please select background color for Preloader.', $this->plugin_name ),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_preloader_bg_color]',
        'id'        => 'lgx_preloader_bg_color',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '#ffffff',
    )
);

