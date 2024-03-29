<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf78fa4ebcb47a81b96df62b82fea1975
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Test\\Helpers\\' => 13,
            'TADPHP\\Providers\\' => 17,
            'TADPHP\\Exceptions\\' => 18,
            'TADPHP\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Test\\Helpers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/test/helpers',
        ),
        'TADPHP\\Providers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/Providers',
        ),
        'TADPHP\\Exceptions\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/Exceptions',
        ),
        'TADPHP\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf78fa4ebcb47a81b96df62b82fea1975::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf78fa4ebcb47a81b96df62b82fea1975::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
