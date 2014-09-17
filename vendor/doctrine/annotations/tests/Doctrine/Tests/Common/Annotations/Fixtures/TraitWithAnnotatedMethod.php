<?php
namespace Doctrine\Tests\Common\Annotations\Fixtures;

trait TraitWithAnnotatedMethod
{

    /**
     * @Autoload
     */
    public $traitProperty;

    /**
     * @Autoload
     */
    public function traitMethod()
    {
    }
}
