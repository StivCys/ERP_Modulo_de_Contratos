<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Domains\Cliente\Models\Cliente;
use App\Domains\Cliente\Policies\ClientePolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Cliente::class => ClientePolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}