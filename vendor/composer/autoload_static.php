<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitba76b259d74b00572a2a571354ae5eab
{
    public static $classMap = array (
        'UsabilityDynamics\\WPJC\\Bootstrap' => __DIR__ . '/../..' . '/lib/classes/class-bootstrap.php',
        'UsabilityDynamics\\WPJC\\User' => __DIR__ . '/../..' . '/lib/classes/class-user.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitba76b259d74b00572a2a571354ae5eab::$classMap;

        }, null, ClassLoader::class);
    }
}
