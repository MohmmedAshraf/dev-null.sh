<?php

declare(strict_types=1);

use App\Models\User;

it('may remove a follower', function (): void {
    $user = User::factory()->create();
    $follower = User::factory()->create(['username' => 'octocat']);

    $follower->following()->attach($user);
    $user->following()->attach($follower);

    $response = $this->actingAs($user)
        ->fromRoute('user.show', $follower)
        ->delete(route('user-follower.destroy', $follower));

    $response->assertRedirectToRoute('user.show', $follower)
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Removed @octocat from your followers.']);

    expect($follower->isFollowing($user))->toBeFalse()
        ->and($user->isFollowing($follower))->toBeTrue();
});

it('may remove someone who does not follow them', function (): void {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $response = $this->actingAs($user)
        ->fromRoute('user.show', $other)
        ->delete(route('user-follower.destroy', $other));

    $response->assertRedirectToRoute('user.show', $other);

    expect($other->isFollowing($user))->toBeFalse();
});

it('may not remove themselves as a follower', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->delete(route('user-follower.destroy', $user));

    $response->assertForbidden();
});

it('lets a removed follower follow again', function (): void {
    $user = User::factory()->create();
    $follower = User::factory()->create();

    $follower->following()->attach($user);

    $this->actingAs($user)->delete(route('user-follower.destroy', $follower));

    $this->actingAs($follower)
        ->post(route('user-follow.store', $user))
        ->assertRedirect();

    expect($follower->isFollowing($user))->toBeTrue();
});

it('requires authentication to remove a follower', function (): void {
    $user = User::factory()->create();
    $follower = User::factory()->create();

    $follower->following()->attach($user);

    $response = $this->delete(route('user-follower.destroy', $follower));

    $response->assertRedirectToRoute('home');

    expect($follower->isFollowing($user))->toBeTrue();
});
