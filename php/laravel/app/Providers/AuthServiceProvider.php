<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
	    Passport::routes();
        // 使用个人token授权，过期时间定时任务自动处理。
        // Passport::tokensExpireIn(now()->addDays(30));  //这里设置为30天过期
	    // Passport::refreshTokensExpireIn(now()->addDays(30));
    }
}
