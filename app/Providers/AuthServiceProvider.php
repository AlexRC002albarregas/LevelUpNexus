<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\{Profile, Game, Post, Comment, Reaction};
use App\Policies\{ProfilePolicy, GamePolicy, PostPolicy, CommentPolicy, ReactionPolicy};
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Profile::class => ProfilePolicy::class,
        Game::class => GamePolicy::class,
        Post::class => PostPolicy::class,
        Comment::class => CommentPolicy::class,
        Reaction::class => ReactionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
