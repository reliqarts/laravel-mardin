<?php

namespace ReliQArts\Mardin;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use ReliQArts\Mardin\Helpers\StringHelper;
use Cmgmyr\Messenger\MessengerServiceProvider;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;

class MardinServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * List of commands.
     *
     * @var array
     */
    protected $commands = [
    ];

    /**
     * Public asset files.
     */
    private function handleAssets()
    {
        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/mardin'),
        ], 'public');
    }

    /**
     * Configuration files.
     */
    private function handleConfigs()
    {
        $configPath = __DIR__.'/../config/mardin.php';

        // Allow publishing the config file, with tag: config
        $this->publishes([$configPath => config_path('mardin.php')], 'config');

        // Merge config files...
        // Allows any modifications from the published config file to be seamlessly merged with default config file
        $this->mergeConfigFrom($configPath, 'mardin');
    }

    /**
     * Translation files.
     */
    private function handleTranslations()
    {
        // Load translations...
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'mardin');
    }

    /**
     * View files.
     */
    private function handleViews()
    {
        // Load the views...
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'mardin');

        // Allow publishing view files, with tag: views
        $this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/mardin'),
        ], 'views');
    }

    /**
     * Migration files.
     */
    private function handleMigrations()
    {
        // Load the migrations...
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * Route files.
     */
    private function handleRoutes()
    {
        // Get the routes...
        require realpath(__DIR__.'/../routes/web.php');
        require realpath(__DIR__.'/../routes/channels.php');
    }

    /**
     * Command files.
     */
    private function handleCommands()
    {
        // Register the commands...
        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }
    }

    /**
     * Register factory files.
     *
     * @param  string  $path
     * @return void
     */
    protected function registerEloquentFactoriesFrom($path)
    {
        $this->app->make(EloquentFactory::class)->load($path);
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->handleConfigs();
        $this->handleMigrations();
        $this->handleViews();
        $this->handleTranslations();
        $this->handleRoutes();
        $this->handleCommands();
        $this->handleAssets();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $loader = AliasLoader::getInstance();

        // Register factories...
        $this->registerEloquentFactoriesFrom(__DIR__.'/../database/factories');

        // Register service providers...
        $this->app->register(MessengerServiceProvider::class);

        // Register facades...
        $loader->alias('MardinStringHelper', StringHelper::class);

        // Bind contracts to models
        $this->app->bind(
            Contracts\User::class,
            config('mardin.user_model')
        );

        $this->app->bind(
            Contracts\UserTransformer::class,
            config('mardin.user_transformer')
        );

        $this->app->bind(
            Contracts\Message::class,
            config('mardin.message_model')
        );

        $this->app->bind(
            Contracts\Thread::class,
            config('mardin.thread_model')
        );

        $this->app->bind(
            Contracts\Participant::class,
            config('mardin.participant_model')
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Contracts\User::class,
            Contracts\Thread::class,
            Contracts\Message::class,
            Contracts\Participant::class,
            Contracts\UserTransformer::class,
        ];
    }
}
