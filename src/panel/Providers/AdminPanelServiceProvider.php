<?php

namespace InetStudio\AdminPanel\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Laratrust\Middleware\LaratrustRole;
use Laratrust\Middleware\LaratrustAbility;
use Laratrust\Middleware\LaratrustPermission;
use SocialiteProviders\Manager\SocialiteWasCalled;
use InetStudio\AdminPanel\Console\Commands\SetupCommand;
use InetStudio\AdminPanel\Listeners\AttachUserRoleToUser;
use InetStudio\AdminPanel\Services\Front\ACL\UsersService;
use SocialiteProviders\Instagram\InstagramExtendSocialite;
use SocialiteProviders\VKontakte\VKontakteExtendSocialite;
use InetStudio\AdminPanel\Events\Auth\SocialActivatedEvent;
use InetStudio\AdminPanel\Listeners\AttachSocialRoleToUser;
use InetStudio\AdminPanel\Events\Auth\SocialRegisteredEvent;
use InetStudio\AdminPanel\Events\Auth\UnactivatedLoginEvent;
use InetStudio\AdminPanel\Console\Commands\CreateAdminCommand;
use InetStudio\AdminPanel\Http\Middleware\Back\Auth\AdminAuthenticate;
use InetStudio\AdminPanel\Services\Front\Auth\UsersActivationsService;
use JhaoDa\SocialiteProviders\Odnoklassniki\OdnoklassnikiExtendSocialite;
use InetStudio\AdminPanel\Listeners\Auth\SendActivateNotificationListener;
use InetStudio\AdminPanel\Http\Middleware\Back\Auth\RedirectIfAuthenticated;

class AdminPanelServiceProvider extends ServiceProvider
{
    /**
     * Загрузка сервиса.
     *
     * @param Router $router
     * @return void
     */
    public function boot(Router $router): void
    {
        $this->registerConsoleCommands();
        $this->registerPublishes();
        $this->registerRoutes();
        $this->registerViews();
        $this->registerTranslations();
        $this->registerMiddlewares($router);
        $this->registerEvents();
        $this->registerViewComposers();
    }

    /**
     * Регистрация привязки в контейнере.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerBindings();
    }

    /**
     * Регистрация команд.
     *
     * @return void
     */
    protected function registerConsoleCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SetupCommand::class,
                CreateAdminCommand::class,
            ]);
        }
    }

    /**
     * Регистрация ресурсов.
     *
     * @return void
     */
    protected function registerPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../../../config/admin.php' => config_path('admin.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateUsersTables')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../../../database/migrations/create_users_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_users_tables.php'),
                ], 'migrations');
            }
        }
    }

    /**
     * Регистрация путей.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../../routes/web.php');
    }

    /**
     * Регистрация представлений.
     *
     * @return void
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../../resources/views', 'admin');
    }

    /**
     * Регистрация переводов.
     *
     * @return void
     */
    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../../../resources/lang', 'admin');
    }

    /**
     * Регистрация посредников.
     *
     * @param Router $router
     * @return void
     */
    protected function registerMiddlewares(Router $router): void
    {
        $router->aliasMiddleware('back.auth', AdminAuthenticate::class);
        $router->aliasMiddleware('back.guest', RedirectIfAuthenticated::class);

        $router->aliasMiddleware('role', LaratrustRole::class);
        $router->aliasMiddleware('permission', LaratrustPermission::class);
        $router->aliasMiddleware('ability', LaratrustAbility::class);
    }

    /**
     * Регистрация событий.
     *
     * @return void
     */
    protected function registerEvents(): void
    {
        Event::listen(Registered::class, SendActivateNotificationListener::class);
        Event::listen(Registered::class, AttachUserRoleToUser::class);
        Event::listen(UnactivatedLoginEvent::class, SendActivateNotificationListener::class);
        Event::listen(SocialiteWasCalled::class, VKontakteExtendSocialite::class);
        Event::listen(SocialiteWasCalled::class, OdnoklassnikiExtendSocialite::class);
        Event::listen(SocialiteWasCalled::class, InstagramExtendSocialite::class);
        Event::listen(SocialActivatedEvent::class, SendActivateNotificationListener::class);
        Event::listen(SocialRegisteredEvent::class, AttachSocialRoleToUser::class);
        Event::listen(SocialRegisteredEvent::class, AttachUserRoleToUser::class);
    }

    /**
     * Register Comments's view composers.
     *
     * @return void
     */
    public function registerViewComposers(): void
    {
        $userClassName = Config::get('auth.model');

        if (is_null($userClassName)) {
            $userClassName = Config::get('auth.providers.users.model');
        }

        view()->composer('admin::back.partials.analytics.users.statistic', function ($view) use ($userClassName) {
            $registrations = (new $userClassName())->select(['activated', DB::raw('count(*) as total')])->groupBy('activated')->get();

            $view->with('registrations', $registrations);
        });
    }

    /**
     * Регистрация привязок, алиасов и сторонних провайдеров сервисов.
     *
     * @return void
     */
    public function registerBindings(): void
    {
        $this->app->bind('UsersActivationsService', UsersActivationsService::class);
        $this->app->singleton('UsersService', UsersService::class);

        // Auth
        // Requests
        $this->app->bind('InetStudio\AdminPanel\Contracts\Http\Requests\Front\RegisterRequestContract', 'InetStudio\AdminPanel\Http\Requests\Front\Auth\RegisterRequest');
    }
}
