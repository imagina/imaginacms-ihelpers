<?php

return [
	'name' => 'Ihelpers',
	'configurationv2'=>[
		/*[
			'Name'=>'Products',
			'RepositoryCategory'=>'Modules\Icommerce\Repositories\CategoryRepository',
			'RepositoryProducts'=>'Modules\Icommerce\Repositories\ProductRepository'
		],*/
		[
			'Name'=>'Pages',
			'RepositoryPages'=>'Modules\Page\Repositories\PageRepository'
		],
		[
			'Name'=>'Post',
			'RepositoryCategory'=>'Modules\Iblog\Repositories\CategoryRepository',
			'RepositoryPost'=>'Modules\Iblog\Repositories\PostRepository'
		]
	]
];
