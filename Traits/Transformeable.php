<?php
namespace Modules\Ihelpers\Traits;

trait Transformeable
{

  /**
   * get custom includes into a transformer
   * @param $data
   */
  public function customIncludes(&$data){

    $classNamespace = get_class($this);
    $classNamespaceExploded = explode('\\',$classNamespace);
    \Log::info($classNamespaceExploded);

    $customProductIncludes = config('asgard.'.strtolower($classNamespaceExploded[1]).'.config.includes.'.$classNamespaceExploded[3]);

    foreach ($customProductIncludes as $include=>$customProductInclude){
      if($customProductInclude['multiple']){
        $data[$include] = $customProductInclude['path']::collection($this->whenLoaded($include));
      }else{
        $data[$include] = new $customProductInclude['path']($this->whenLoaded($include));
      }
    }
  }
}
