<?php
// app/helpers_oncologie.php

if (! function_exists('oncologieUserCan')) {
    function oncologieUserCan(string $permission): bool
{
    $user = \Illuminate\Support\Facades\Auth::guard('oncologie')->user();

    if (!$user) {
        return false;
    }

    // Administrateur = accès total
    if ($user->role === 'administrateur') {
        return true;
    }

    $matrix = config('oncologie_permissions.permissions', []);

    return in_array($user->role, $matrix[$permission] ?? [], true);
}
}

if (! function_exists('oncologieUserRole')) {
    function oncologieUserRole(): string
    {
        return \Illuminate\Support\Facades\Auth::guard('oncologie')->user()?->role ?? '';
    }
}

if (! function_exists('oncologieUserIs')) {
    function oncologieUserIs(string ...$roles): bool
    {
        return in_array(oncologieUserRole(), $roles, true);
    }
}