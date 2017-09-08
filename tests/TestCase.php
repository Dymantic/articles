<?php

namespace Dymantic\Articles\Test;

use Dymantic\Articles\ArticlesServiceProvider;
use File;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;
use \Spatie\MediaLibrary\MediaLibraryServiceProvider;

abstract class TestCase extends Orchestra
{

    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ArticlesServiceProvider::class,
            MediaLibraryServiceProvider::class
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->initializeDirectory(__DIR__ . '/temp');

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('filesystems.disks.media', [
            'driver' => 'local',
            'root'   => __DIR__ . '/temp/media',
        ]);

        $app['config']->set('medialibrary', [
            'default_filesystem'          => 'media',
            'max_file_size'               => 1024 * 1024 * 10,
            'queue_name'                  => '',
            'media_model'                 => \Spatie\MediaLibrary\Media::class,
            'image_driver'                => 'gd',
            'custom_url_generator_class'  => null,
            'custom_path_generator_class' => null,
            's3'                          => [
                'domain' => 'https://xxxxxxx.s3.amazonaws.com',
            ],
            'remote'                      => [
                'extra_headers' => [
                    'CacheControl' => 'max-age=604800',
                ],
            ],
            'image_generators'            => [
                \Spatie\MediaLibrary\ImageGenerators\FileTypes\Image::class,
                \Spatie\MediaLibrary\ImageGenerators\FileTypes\Pdf::class,
                \Spatie\MediaLibrary\ImageGenerators\FileTypes\Svg::class,
                \Spatie\MediaLibrary\ImageGenerators\FileTypes\Video::class,
            ],
            'image_optimizers'            => [
                \Spatie\ImageOptimizer\Optimizers\Jpegoptim::class => [
                    '--strip-all',  // this strips out all text information such as comments and EXIF data
                    '--all-progressive',  // this will make sure the resulting image is a progressive one
                ],
                \Spatie\ImageOptimizer\Optimizers\Pngquant::class  => [
                    '--force', // required parameter for this package
                ],
                \Spatie\ImageOptimizer\Optimizers\Optipng::class   => [
                    '-i0', // this will result in a non-interlaced, progressive scanned image
                    '-o2',  // this set the optimization level to two (multiple IDAT compression trials)
                    '-quiet', // required parameter for this package
                ],
                \Spatie\ImageOptimizer\Optimizers\Svgo::class      => [
                    '--disable=cleanupIDs', // disabling because it is known to cause troubles
                ],
                \Spatie\ImageOptimizer\Optimizers\Gifsicle::class  => [
                    '-b', // required parameter for this package
                    '-O3', // this produces the slowest but best results
                ],
            ],
            'temporary_directory_path'    => null,
            'ffmpeg_binaries'             => '/usr/bin/ffmpeg',
            'ffprobe_binaries'            => '/usr/bin/ffprobe',
        ]);

        $app->bind('path.public', function () {
            return __DIR__ . '/temp';
        });

        $app['config']->set('app.key', '6rE9Nz59bGRbeMATftriyQjrpF7DcOQm');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('test_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        $app['db']->connection()->getSchemaBuilder()->create('nameless_authors', function (Blueprint $table) {
            $table->increments('id');
        });

        $app['db']->connection()->getSchemaBuilder()->create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('model');
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->unsignedInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->unsignedInteger('order_column')->nullable();
            $table->nullableTimestamps();
        });

        TestUserModel::create(['name' => 'test user']);

        include_once __DIR__ . '/../database/migrations/create_articles_table.php.stub';

        (new \CreateArticlesTable())->up();
    }

    protected function initializeDirectory($directory)
    {
        if (File::isDirectory($directory)) {
            File::deleteDirectory($directory);
        }
        File::makeDirectory($directory);
    }
}