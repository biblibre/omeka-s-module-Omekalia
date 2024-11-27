<?php

namespace Omekalia;

return [
    'controllers' => [
        'factories' => [
            'Omekalia\Controller\Admin\Omekalia' => Service\Controller\Admin\OmekaliaControllerFactory::class,
        ],
    ],
    'navigation' => [
        'AdminGlobal' => [
            [
                'label' => 'Omekalia',
                'class' => 'omekalia fa-info-circle',
                'route' => 'admin/omekalia',
                'action' => 'status',
                'resource' => 'Omekalia\Controller\Admin\Omekalia',
                'privilege' => 'status',
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'admin' => [
                'child_routes' => [
                    'omekalia' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/omekalia/:action',
                            'defaults' => [
                                '__NAMESPACE__' => 'Omekalia\Controller\Admin',
                                'controller' => 'omekalia',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            'Omekalia\Status' => Service\Stdlib\StatusFactory::class,
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => dirname(__DIR__) . '/language',
                'pattern' => '%s.mo',
                'text_domain' => null,
            ],
        ],
    ],
    'view_helpers' => [
        'factories' => [
            'omekalia' => Service\View\Helper\OmekaliaFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
];
