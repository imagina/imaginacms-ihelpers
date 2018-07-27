<?php

return [
    'name' => 'Ihelpers',
    'configuration' => [
        [
            'name' => 'Category', 
            'Entity' => '\Modules\Icommerce\Entities\Category',
            'orderBy' => [
                'param0' => 'title',
                'param1' => 'ASC'
            ], 
            'findTitle' => false,
            'url' => env('APP_URL') . '/category',
            'replaceInUrl' => 'category',
            'params' => [
                [
                    'param0' => 'parent_id',
                    'param1' => 0, 
                    'operator' => '='
                ]
            ],
            'relation' => [
                'name' => 'SubCategory',
                'Entity' => '\Modules\Icommerce\Entities\Category',
                'orderBy' => [
                    'param0' => 'title', 
                    'param1' => 'ASC'
                ], 
                'findTitle' => false,
                'url' => env('APP_URL') . '/subcategory', 'replaceInUrl' => 'subcategory',
                'params' => [
                    [
                        'param0' => 'parent_id', 
                        'operator' => '=',
                        'param1' => 'getParam'
                    ]
                ],
                'relation' => [
                    'name' => 'Products',
                    'Entity' => '\Modules\Icommerce\Entities\Product',
                    'orderBy' => [
                        'param0' => 'created_at', 
                        'param1' => 'DESC'
                    ], 
                    'findTitle' => false,
                    'url' => env('APP_URL') . '/product', 'replaceInUrl' => 'product',
                    'params' => [
                        [
                            'param0' => 'status', 
                            'param1' => 1, 
                            'operator' => '='
                        ],
                        [
                            'param0' => 'date_available',
                            'param1' => "'" . date('Y-m-d') . "'", 
                            'operator' => '<='
                        ], 
                        [
                            'param0' => 'category_id',
                            'operator' => '=', 
                            'param1' => 'getParam'
                        ]
                    ]
                ]
            ]
        ],
        [
            'name' => 'CategoryPost',
            'Entity' => '\Modules\Iblog\Entities\Category',
            'orderBy' => [
                'param0' => 'title',
                'param1' => 'ASC'
            ], 
            'findTitle' => false,
            'url' => env('APP_URL') . '/category',
            'replaceInUrl' => 'category',
            'params' => [
                [
                    'param0' => 'parent_id', 'param1' => 0, 
                    'operator' => '='
                ]
            ],
            'relation' => [
                'name' => 'Post',
                'Entity' => '\Modules\Iblog\Entities\Post',
                'orderBy' => [
                    'param0' => 'created_at', 
                    'param1' => 'DESC'
                ], 
                'findTitle' => false,
                'url' => env('APP_URL') . '/post',
                'replaceInUrl' => 'post',
                'params' => [
                    [
                        'param0' => 'category_id',
                        'operator' => '=', 
                        'param1' => 'getParam'
                    ]
                ]
            ]
        ]
    ]
];