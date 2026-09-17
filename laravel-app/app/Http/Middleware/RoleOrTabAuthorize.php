<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleOrTabAuthorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $tabOrRole
     */
    public function handle(Request $request, Closure $next, string $tabOrRole): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'احراز هویت انجام نشده است. لطفاً ابتدا وارد شوید.'
            ], 401);
        }

        // Super admins have full access always
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if $tabOrRole matches allowed role or allowedTabs
        if ($user->role === $tabOrRole) {
            return $next($request);
        }

        if ($user->isTabAllowed($tabOrRole)) {
            return $next($request);
        }

        return response()->json([
            'error' => 'خطای عدم دسترسی: شما مجاز به انجام این عملیات یا مشاهده این بخش نیستید.'
        ], 403);
    }
}
