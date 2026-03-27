<?php

namespace App\Agent\Tools;


use Illuminate\Support\Facades\Auth;
use Prism\Prism\Facades\Tool;


class GetAllPosts
{
    public function handle(): \Prism\Prism\Tool
    {
        $allPostTool = Tool::as('get_all_posts')
            ->for('Get All Posts')
            ->using(function (): string {
                $posts = Auth::user()->posts()->get();
                if ($posts->isEmpty()) {
                    return "You have no posts.";
                }

                return $posts->map(function ($post) {
                    return "ID: {$post->id} | Title: {$post->title} | Status: {$post->status}";
                })->implode("\n");
            });
        return $allPostTool;
    }
}
