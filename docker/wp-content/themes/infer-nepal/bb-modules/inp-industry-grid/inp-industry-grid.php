<?php
if (!defined('ABSPATH')) { exit; }

class INPIndustryGridModule extends FLBuilderModule {
    public function __construct() {
        parent::__construct([
            'name'            => __('Industry Grid', 'infer-nepal'),
            'description'     => __('Auto-populated grid of industry tiles.', 'infer-nepal'),
            'category'        => 'Infer Nepal',
            'dir'             => INP_BB_DIR . 'inp-industry-grid/',
            'url'             => INP_BB_URL . 'inp-industry-grid/',
            'editor_export'   => true,
            'enabled'         => true,
            'partial_refresh' => true,
            'icon'            => 'screenoptions.svg',
        ]);
    }
}

FLBuilder::register_module('INPIndustryGridModule', [
    'general' => [
        'title'    => __('General', 'infer-nepal'),
        'sections' => [
            'general' => [
                'title'  => '',
                'fields' => [
                    'count' => [
                        'type'    => 'unit',
                        'label'   => __('How many tiles', 'infer-nepal'),
                        'default' => '10',
                        'size'    => 4,
                    ],
                    'columns' => [
                        'type'    => 'unit',
                        'label'   => __('Columns', 'infer-nepal'),
                        'default' => '5',
                        'size'    => 4,
                    ],
                ],
            ],
        ],
    ],
]);