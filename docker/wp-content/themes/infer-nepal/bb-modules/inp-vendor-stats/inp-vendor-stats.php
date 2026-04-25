<?php
if (!defined('ABSPATH')) { exit; }

class INPVendorStatsModule extends FLBuilderModule {
    public function __construct() {
        parent::__construct([
            'name'            => __('Vendor Stats', 'infer-nepal'),
            'description'     => __('Four stat cards (buyers / demos / reviews / listings) from Customizer.', 'infer-nepal'),
            'category'        => 'Infer Nepal',
            'dir'             => INP_BB_DIR . 'inp-vendor-stats/',
            'url'             => INP_BB_URL . 'inp-vendor-stats/',
            'editor_export'   => true,
            'enabled'         => true,
            'partial_refresh' => true,
            'icon'            => 'chart-bar.svg',
        ]);
    }
}

FLBuilder::register_module('INPVendorStatsModule', [
    'general' => [
        'title'    => __('General', 'infer-nepal'),
        'sections' => [
            'general' => [
                'title'       => __('Numbers come from Customizer → Brand → Platform stats', 'infer-nepal'),
                'description' => __('No per-instance settings. Edit the four numbers in Appearance → Customize.', 'infer-nepal'),
                'fields'      => [],
            ],
        ],
    ],
]);