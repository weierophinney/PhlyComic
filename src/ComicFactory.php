<?php

namespace PhlyComic;

use DomainException;
use InvalidArgumentException;

abstract class ComicFactory
{
    /** @var array<non-empty-string, class-string<ComicSource>> */
    protected static $aliasMap = [];

    /** @var list<class-string<ComicSource>> List of comic source classes */
    protected static $comicClasses = [
        'PhlyComic\ComicSource\BasicInstructions',
        'PhlyComic\ComicSource\CommitStrip',
        'PhlyComic\ComicSource\CtrlAltDel',
        'PhlyComic\ComicSource\Dilbert',
        'PhlyComic\ComicSource\DorkTower',
        'PhlyComic\ComicSource\Drive',
        'PhlyComic\ComicSource\ForBetterOrForWorse',
        'PhlyComic\ComicSource\FoxTrot',
        'PhlyComic\ComicSource\GarfieldMinusGarfield',
        'PhlyComic\ComicSource\GoComics',
        'PhlyComic\ComicSource\LakeGary',
        'PhlyComic\ComicSource\ListenToMe',
        'PhlyComic\ComicSource\LunarBaboon',
        'PhlyComic\ComicSource\NotInventedHere',
        'PhlyComic\ComicSource\Oatmeal',
        'PhlyComic\ComicSource\PennyArcade',
        'PhlyComic\ComicSource\PhDComics',
        'PhlyComic\ComicSource\ReptilisRex',
        'PhlyComic\ComicSource\SaturdayMorningBreakfastCereal',
        'PhlyComic\ComicSource\Sheldon',
        'PhlyComic\ComicSource\ScenesFromAMultiverse',
        'PhlyComic\ComicSource\UserFriendly',
        'PhlyComic\ComicSource\Xkcd',
    ];

    /** @var array<class-string, Comic> */
    protected static $supported = [];

    /**
     * Retrieve a source class for a given comic
     *
     * @param  string $name Comic "alias" used within a comic source
     */
    public static function factory(string $name): ComicSource
    {
        static::initSupported();

        if (! array_key_exists($name, static::$aliasMap)) {
            throw new InvalidArgumentException(sprintf(
                'Comic "%s" is not supported',
                $name
            ));
        }

        $class  = static::$aliasMap[$name];
        $source = new $class();

        if (!$source instanceof ComicSource) {
            throw new DomainException(sprintf(
                'Comic "%s" does not have a valid ComicSource (uses "%s") associated with it',
                $name,
                $class
            ));
        }

        return $source;
    }

    /**
     * Add a comic source class to use with the factory
     *
     * Must implement ComicSource.
     *
     * @param  class-string<ComicSource> $classname
     */
    public static function addSourceClass($classname): void
    {
        static::$comicClasses[] = $classname;
        static::$supported = [];
    }

    /**
     * Get list of supported comics
     *
     * Returns a list of supported comics. Each key is a comic "alias" used by
     * the comic source, pointing to a Comic intance.
     *
     * @return array<non-empty-string, Comic>
     */
    public static function getSupported()
    {
        static::initSupported();
        return static::$supported;
    }

    /**
     * Initialize the {@link $supported} list
     *
     * @return void
     */
    protected static function initSupported()
    {
        if (! empty(static::$supported)) {
            return;
        }

        foreach (static::$comicClasses as $class) {
            $comic = $class::provides();
            static::$supported[$class] = $comic;
            static::$aliasMap[$comic->name] = $class;
        }
    }
}
