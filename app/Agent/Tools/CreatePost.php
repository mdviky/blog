<?php

namespace App\Agent\Tools;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Prism\Prism\Facades\Tool;

class CreatePost
{
    public function handle(): \Prism\Prism\Tool
    {
        $allPostTool = Tool::as('create_post')
            ->for('Create Post')
            ->withStringParameter('title', 'The title of the post')
            ->withStringParameter('body', 'The body of the post')
            ->withStringParameter('status', 'The status of the post (draft or published)')
            ->using(function (string $title, string $body, string $status = 'draft'): string {
                $status = strtolower(trim($status));
                $allowedStatuses = ['draft', 'published'];

                if (!in_array($status, $allowedStatuses, true)) {
                    return "Invalid status '{$status}'. Allowed statuses: " . implode(', ', $allowedStatuses) . ".";
                }

                $slug = Str::slug($title);
                $originalSlug = $slug;
                $suffix = 1;

                while (\App\Models\Post::where('slug', $slug)->exists()) {
                    $slug = "{$originalSlug}-{$suffix}";
                    $suffix++;
                }

                $user = Auth::user();
                $post = $user->posts()->create([
                    'title' => $title,
                    'slug' => $slug,
                    'body' => $body,
                    'status' => $status,
                    'published_at' => $status === 'published' ? now() : null,
                ]);

                return "Post '{$post->title}' created successfully with ID {$post->id} and slug '{$post->slug}'.";

            });
        return $allPostTool;
    }

}
