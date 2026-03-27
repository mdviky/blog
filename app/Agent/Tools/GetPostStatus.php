<?php

namespace App\Agent\Tools;

use Prism\Prism\Facades\Tool;
use Illuminate\Support\Facades\Auth;


class GetPostStatus
{

    /**
     * Handle the event.
     */
    public function handle(): \Prism\Prism\Tool
    {
        $postTool = Tool::as('get_post_status')
            ->for('Get Post Status')
            ->withStringParameter('post_id', 'The ID of the post to get status for')
            ->using(function (string $post_id): string {
                $post = Auth::user()->posts()->find($post_id);

                if (!$post) {
                    return "Post with ID {$post_id} not found.";
                }

                return "The status of post {$post_id} is {$post->status}.";
            });

        return $postTool;
    }
}
