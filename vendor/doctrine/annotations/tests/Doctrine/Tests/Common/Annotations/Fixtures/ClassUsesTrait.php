<?php

namespace Doctrine\Tests\Common\Annotations\Fixtures;

class ClassUsesTrait
{
    use TraitWithAnnotatedMethod;

    /**
     * @Autoload
     */
    public $aProperty;

    /**
     * @Autoload
     */
    public function someMethod()
    {

    }
}


namespace Doctrine\Tests\Common\Annotations\Bar;

/** @Annotation */
class Autoload
{
}
