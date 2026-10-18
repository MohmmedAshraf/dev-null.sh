<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\DeleteUserFollow;
use App\Http\Requests\DeleteUserFollowerRequest;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class UserFollowerController
{
    public function destroy(DeleteUserFollowerRequest $request, #[CurrentUser] User $currentUser, User $user, DeleteUserFollow $action): RedirectResponse
    {
        $action->handle($user, $currentUser);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => "Removed @{$user->username} from your followers.",
        ]);

        return back();
    }
}
