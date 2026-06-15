<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use App\Policies\ArticleSourcePolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
        //(No model for ArticleSource, so we define the policy methods directly)
        Gate::define('create-article-source', [ArticleSourcePolicy::class, 'create']);
        Gate::define('update-article-source', [ArticleSourcePolicy::class, 'update']);
        Gate::define('delete-article-source', [ArticleSourcePolicy::class, 'delete']);
    }
}
