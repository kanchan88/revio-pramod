<?php
if (!defined('ABSPATH')) { exit; }

class INPSectionHeadingModule extends FLBuilderModule {
    public function __construct() {
        parent::__construct([
            'name'            => __('Section Heading', 'infer-nepal'),
            'description'     => __('Section title + subtitle + optional "see all" link, in the Infer Nepal style.', 'infer-nepal'),
            'category'        => 'Infer Nepal',
            'dir'             => INP_BB_DIR . 'inp-section-heading/',
            'url'             => INP_BB_URL . 'inp-section-heading/',
            'editor_export'   => true,
            'enabled'         => true,
            'partial_refresh' => true,
            'icon'            => 'heading.svg',
        ]);
    }
}

FLBuilder::register_module('INPSectionHeadingModule', [
    'general' => [
        'title'    => __('Heading', 'infer-nepal'),
        'sections' => [
            'general' => [
                'title'  => '',
                'fields' => [
                    'title' => [
                        'type'    => 'text',
                        'label'   => __('Title', 'infer-nepal'),
                        'default' => 'Top-rated software',
                    ],
                    'subtitle' => [
                        'type'    => 'text',
                        'label'   => __('Subtitle', 'infer-nepal'),
                        'default' => 'Most reviewed by Nepali buyers this quarter.',
                    ],
                    'link_text' => [
                        'type'    => 'text',
                        'label'   => __('Link text (optional)', 'infer-nepal'),
                        'default' => 'View all →',
                    ],
                    'link_url' => [
                        'type'    => 'link',
                        'label'   => __('Link URL', 'infer-nepal'),
                        'default' => '/software/',
                    ],
                ],
            ],
        ],
    ],
]);