<?php

namespace App\Agent\Tools;


use Illuminate\Support\Facades\Auth;
use Prism\Prism\Facades\Tool;

class UpdatePostStatus
{
    public function handle(): \Prism\Prism\Tool
    {
        $updatePostStatusTool = Tool::as('update_post_status')
            ->for('Update Post Status')
            ->withStringParameter('post_id', 'The ID of the post to update')
            ->withStringParameter('status', 'The new status for the post (e.g., draft, published, archived)')
            ->using(function (string $post_id, string $status): string {
                $post = Auth::user()->posts()->find($post_id);

                if (!$post) {
                    return "Post with ID {$post_id} not found.";
                }

                $allowed = ['draft', 'published'];
                $status = strtolower(trim($status));

                if (!in_array($status, $allowed, true)) {
                    return "Invalid status '{$status}'. Allowed statuses: " . implode(', ', $allowed) . ".";
                }

                $post->status = $status;
                if ($status === 'published' && !$post->published_at) {
                    $post->published_at = now();
                }
                $post->save();

                return "Post ID {$post_id} status updated to '{$status}'.";
            });

        return $updatePostStatusTool;
    }

}
