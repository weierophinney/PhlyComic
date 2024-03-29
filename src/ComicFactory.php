<?php

namespace PhlyComic;

use DomainException;
use InvalidArgumentException;

abstract class ComicFactory
{
    /**
     * @var array List of comic source classes
     */
    protected static $comicClasses = array(
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
    );

    /**
     * @var array List of comic => array( 'name' => name, 'class' => source class)
     */
    protected static $supported = array();

    /**
     * Retrieve a source class for a given comic
     *
     * @param  string $name Comic "alias" used within a comic source
     * @return ComicSource
     */
    public static function factory($name)
    {
        static::initSupported();

        if (!isset(static::$supported[$name])) {
            throw new InvalidArgumentException(sprintf(
                'Comic "%s" is not supported',
                $name
            ));
        }

        $class  = static::$supported[$name]['class'];
        $source = new $class($name);

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
     * @param  string $classname
     * @return void
     */
    public static function addSourceClass($classname)
    {
        static::$comicClasses[] = $classname;
        static::$supported = array();
    }

    /**
     * Get list of supported comics
     *
     * Returns a list of supported comics. Each key is a comic "alias" used by
     * the comic source, pointing to an array with "name" and "class" keys; the
     * "name" is the comic name, and the "class" is the comic source class used
     * to retrieve it.
     *
     * @return array
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
        if (!empty(static::$supported)) {
            return;
        }

        foreach (static::$comicClasses as $class) {
            $supported = call_user_func($class . '::supports');
            foreach ($supported as $alias => $comic) {
                static::$supported[$alias] = array(
                    'name'  => $comic,
                    'class' => $class,
                );
            }
        }
    }
}
