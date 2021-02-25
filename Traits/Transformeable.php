<?php
namespace Modules\Ihelpers\Traits;

trait Transformeable
{

  /**
   * get custom includes into a transformer
   * @param $data
   */
  public function customIncludes(&$data){

    $classNamespace = get_class($this->resource);
    $classNamespaceExploded = explode('\\',strtolower($classNamespace));


    $customProductIncludes = config('asgard.'.strtolower($classNamespaceExploded[1]).'.config.includes.'.$classNamespaceExploded[3]) ?? [];


    foreach ($customProductIncludes as $include=>$customProductInclude){
      if($customProductInclude['multiple']){
        $data[$include] = $customProductInclude['path']::collection($this->$include()->get() ?? []);
      }else{
        $data[$include] = new $customProductInclude['path']($this->$include()->first() ?? null);
      }
    }
  }
}
