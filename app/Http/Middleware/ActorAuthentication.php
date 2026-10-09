<?php

namespace App\Http\Middleware;

use App\Support\RememberedActorSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActorAuthentication
{
    public function __construct(private RememberedActorSession $rememberedActorSession)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $this->rememberedActorSession->restore($request);

        if (! $request->session()->has('actor_role') || ! $request->session()->has('actor_id')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}