<?php
/**
 * Part of the $author$ PHP packages.
 *
 * License and copyright information bundled with this package in the LICENSE file
 */


namespace Codex\Core\Addons;

use Codex\Core\Addons\Annotations\Hook;
use Codex\Core\Addons\Scanner\Scanner;
use Codex\Core\Codex;
use Codex\Core\Contracts;
use Codex\Core\Support\Collection;
use Codex\Core\Traits;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Illuminate\Support\Traits\Macroable;
use Laradic\Support\Filesystem;
use Laradic\Support\Path;
use ReflectionClass;

class Addons
{
    use Macroable;

    /** @var array */
    protected static $annotations = [
        AddonType::DOCUMENT => Annotations\Document::class,
        AddonType::FILTER   => Annotations\Filter::class,
        AddonType::HOOK     => Annotations\Hook::class,
    ];

    /** @var AddonServiceProvider[] */
    protected static $providers = [ ];

    /** @var bool */
    protected static $initialised = false;

    /** @var array */
    protected static $addons = [ ];

    /** @var \Doctrine\Common\Annotations\AnnotationReader */
    protected static $reader;

    protected static function init()
    {
        if ( static::$initialised ) {
            return;
        }

        static::$reader = new AnnotationReader();

        foreach ( Filesystem::create()->globule(__DIR__ . '/Annotations/*.php') as $filePath ) {
            AnnotationRegistry::registerFile($filePath);
        }
        static::$initialised = true;
    }

    public static function register($providers)
    {
        static::init();

        /** @var AddonServiceProvider[] $providers */
        if ( !is_array($providers) ) {
            $providers = [ $providers ];
        }

        foreach ( $providers as $provider ) {
            if ( array_key_exists($provider->getName(), static::$providers) ) {
                continue;
            }

            $path                                      = (new ReflectionClass($provider))->getFileName();
            $dir                                       = Path::getDirectory($path);
            $provides                                  = static::scanDirectory($dir);
            static::$providers[ $provider->getName() ] = compact('provider', 'provides');
        }
    }

    protected static function scanDirectory($dir)
    {
        $provides = [ ];
        foreach ( static::$annotations as $type => $annotationClass ) {
            $methodName = 'handle' . ucfirst($type);
            $scanner    = (new Scanner(static::$reader))->scan([ $annotationClass ])->in($dir);
            foreach ( $scanner as $file ) {
                /** @var \Codex\Core\Addons\Scanner\ClassFileInfo $file */
                $provide = [
                    'type'        => $type,
                    'class'       => $file->getClassName(),
                    'file'        => $file->getFilename(),
                    'annotations' => Collection::make([
                        'class'      => $file->getClassAnnotations(),
                        'method'     => $file->getMethodAnnotations(),
                        'properties' => $file->getPropertyAnnotations(),
                    ]),
                ];


                if ( method_exists(static::class, $methodName) || static::hasMacro($methodName) ) {

                    forward_static_call_array(static::class . '::' . $methodName, [ $provide ]);
                }

                if ( !array_key_exists($type, static::$addons) ) {
                    static::$addons[ $type ] = [ ];
                }
                static::$addons[ $type ][] = $provide;
                $provides[]                = $provide;
            }
        }
        return $provides;
    }

    public static function handleHook(array $provide)
    {
        foreach($provide['annotations']['class'] as $hook){
            if(!$hook instanceof Hook){
                continue;
            }
            Codex::hook($hook->name[0], function() use ($provide) {
                app()->call($provide['class'], func_get_args(), 'handle');
            });
        }

        foreach($provide['annotations']['method'] as $method => $hooks){
            foreach($hooks as $hook) {
                if ( !$hook instanceof Hook ) {
                    continue;
                }
                Codex::hook($hook->name[0], function () use ($provide, $method) {
                    $args = func_get_args();
                    app()->call($provide[ 'class' ], $args, $method);
                });
            }
        }
        return $provide;
    }

    public static function getDocuments()
    {
        $documents = [ ];
        foreach ( static::get(AddonType::DOCUMENT) as $i => $document ) {
            foreach ( $document[ 'annotations' ][ 'class' ] as $doc ) {
                /** @var \Codex\Core\Addons\Annotations\Document $doc */
                $documents[ $doc->name ] = [
                    'type'       => $doc->name,
                    'extensions' => $doc->extensions,
                    'class'      => $document[ 'class' ],
                ];
            }
        }
        return $documents;
    }

    public static function getFilters($for = [ ])
    {
        $for = is_string($for) ? [ $for ] : $for;

        $filters = [ ];
        foreach ( static::get(AddonType::FILTER) as $i => $filter ) {
            foreach ( $filter[ 'annotations' ][ 'class' ] as $f ) {
                /** @var \Codex\Core\Addons\Annotations\Filter $f */
                if ( count($for) > 0 && !in_array($f->for, $for, true) ) {
                    continue;
                }
                $filters[ $f->name ] = [
                    'for'   => $f->for,
                    'class' => $filter[ 'class' ],
                ];
            }
        }
        return $filters;
    }

    public static function getHooks()
    {
    }

    public static function get($type)
    {
        return static::$addons[ $type ];
    }
}
