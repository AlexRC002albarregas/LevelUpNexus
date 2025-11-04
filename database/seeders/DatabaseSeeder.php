<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{User, Profile, Game, Group, Post, Comment};

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        $player = User::factory()->create([
            'name' => 'Player One',
            'email' => 'player1@example.com',
            'password' => Hash::make('password'),
            'role' => 'player',
        ]);
        $member = User::factory()->create([
            'name' => 'Guild Member',
            'email' => 'member@example.com',
            'password' => Hash::make('password'),
            'role' => 'group_member',
        ]);

        foreach ([$admin, $player, $member] as $i => $user) {
            Profile::create([
                'user_id' => $user->id,
                'nick' => 'user'.$user->id,
                'platform' => 'pc',
                'favorite_games' => ['Valorant','LoL'],
                'hours_played' => 100 * ($i + 1),
                'achievements' => ['First Blood','MVP'],
                'bio' => 'Bio del usuario '.$user->name,
            ]);

            Game::create([
                'user_id' => $user->id,
                'title' => 'FIFA 24',
                'platform' => 'pc',
                'genre' => 'Sports',
                'hours_played' => 25,
                'is_favorite' => true,
            ]);
        }

        $group = Group::create([
            'name' => 'LevelUp Guild',
            'description' => 'Grupo de prueba',
            'owner_id' => $admin->id,
        ]);
        $group->members()->attach([$admin->id => ['member_role' => 'owner'], $member->id => ['member_role' => 'member']]);

        $post = Post::create([
            'user_id' => $admin->id,
            'group_id' => $group->id,
            'content' => 'Bienvenidos al grupo!',
            'visibility' => 'group',
        ]);
        Comment::create([
            'user_id' => $member->id,
            'post_id' => $post->id,
            'content' => 'Gracias! Feliz de estar aquí.',
        ]);
    }
}
