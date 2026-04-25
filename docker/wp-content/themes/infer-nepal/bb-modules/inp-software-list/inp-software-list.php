<?php
/**
 * Infer Nepal — Software List module for Beaver Builder.
 *
 * Drag-and-drop a live list of software, filtered by industry / category,
 * sorted by rating / reviews / date, and rendered as cards or rows.
 */

if (!defined('ABSPATH')) { exit; }

class INPSoftwareListModule extends FLBuilderModule {
    public function __construct() {
        parent::__construct([
            'name'            => __('Software List', 'infer-nepal'),
            'description'     => __('Auto-populated software list, filterable by industry / category.', 'infer-nepal'),
            'category'        => 'Infer Nepal',
            'dir'             => INP_BB_DIR . 'inp-software-list/',
            'url'             => INP_BB_URL . 'inp-software-list/',
            'editor_export'   => true,
            'enabled'         => true,
            'partial_refresh' => true,
            'icon'            => 'editor-ul.svg',
        ]);
    }
}

FLBuilder::register_module('INPSoftwareListModule', [
    'general' => [
        'title'    => __('Filters', 'infer-nepal'),
        'sections' => [
            'filters' => [
                'title'  => __('What to show', 'infer-nepal'),
                'fields' => [
                    'industry' => [
                        'type'        => 'select',
                        'label'       => __('Industry', 'infer-nepal'),
                        'default'     => '',
                        'options'     => inp_bb_term_options('industry', '— Any industry —'),
                    ],
                    'category' => [
                        'type'        => 'select',
                        'label'       => __('Category', 'infer-nepal'),
                        'default'     => '',
                        'options'     => inp_bb_term_options('sw_category', '— Any category —'),
                    ],
                    'count' => [
                        'type'        => 'unit',
                        'label'       => __('How many', 'infer-nepal'),
                        'default'     => '6',
                        'maxlength'   => 2,
                        'size'        => 4,
                    ],
                    'orderby' => [
                        'type'        => 'select',
                        'label'       => __('Sort by', 'infer-nepal'),
                        'default'     => 'rating',
                        'options'     => [
                            'rating'  => __('Rating', 'infer-nepal'),
                            'reviews' => __('Reviews', 'infer-nepal'),
                            'date'    => __('Newest', 'infer-nepal'),
                        ],
                    ],
                ],
            ],
            'layout' => [
                'title'  => __('Layout', 'infer-nepal'),
                'fields' => [
                    'display' => [
                        'type'    => 'select',
                        'label'   => __('Display as', 'infer-nepal'),
                        'default' => 'cards',
                        'options' => [
                            'cards' => __('Cards (3-col grid)', 'infer-nepal'),
                            'rows'  => __('Rows (vertical list)', 'infer-nepal'),
                        ],
                    ],
                    'featured_first' => [
                        'type'    => 'select',
                        'label'   => __('Featured first row', 'infer-nepal'),
                        'default' => '1',
                        'options' => ['1' => __('Yes', 'infer-nepal'), '0' => __('No', 'infer-nepal')],
                        'help'    => __('Highlight the first row as a "Top listing"', 'infer-nepal'),
                    ],
                ],
            ],
        ],
    ],
]);