<?php

use Modules\Blog\Entities\Post;
use Modules\Iforms\Entities\Form;

if (! function_exists('posts')) {

     function posts() {
         $posts = Post::query();
      return $posts;
     }

}

if (! function_exists('iform')) {

    function iform($id,$templates,$options=array()) {

        $iform = Form::find($id);
        //$template="";
        $view = View::make($templates)
            ->with([
                'form' => $iform,
                'options' => $options,
            ]);

        return $view->render();

    }

}

