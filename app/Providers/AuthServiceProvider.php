<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
      'App\Models\Answer' => 'App\Policies\AnswerPolicy',
      'App\Models\Question' => 'App\Policies\QuestionPolicy',
      'App\Models\Report' => 'App\Policies\ReportPolicy',
      'App\Models\Tag' => 'App\Policies\TagPolicy',
      'App\Models\Block' => 'App\Policies\BlockPolicy',
      'App\Models\User' => 'App\Policies\UserPolicy'
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
