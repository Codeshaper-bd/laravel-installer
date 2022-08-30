<?php

namespace RachidLaasri\LaravelInstaller\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Redirect;

class NeedToInstall
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$this->alreadyInstalled()) {
            return redirect()->route('LaravelInstaller::welcome');
        }

        return $next($request);
    }

    /**
     * If application is already installed.
     *
     * @return bool
     */
    public function alreadyInstalled()
    {
        return file_exists(storage_path('installed'));
    }
}
