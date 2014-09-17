<?php

namespace Doctrine\Tests\Common\Annotations\Fixtures;

class ClassOverwritesTrait
{
    use TraitWithAnnotatedMethod;

    /**
     * @Autoload
     */
    public function traitMethod()
    {

    }
}


namespace Doctrine\Tests\Common\Annotations\Bar2;

/** @Annotation */
class Autoload
{
}
