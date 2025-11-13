<?php

namespace App\Providers;

use App\Models\{Profile, Game, Post, Comment, Reaction};
use App\Policies\{ProfilePolicy, GamePolicy, PostPolicy, CommentPolicy, ReactionPolicy};
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Profile::class => ProfilePolicy::class,
        Game::class => GamePolicy::class,
        Post::class => PostPolicy::class,
        Comment::class => CommentPolicy::class,
        Reaction::class => ReactionPolicy::class,
    ];

    public function boot(): void
    {
    }
}
