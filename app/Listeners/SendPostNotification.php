<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Mail\NewPostMail;
use Illuminate\Support\Facades\Mail;


use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Log;


class SendPostNotification implements ShouldQueue
{
        use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    

    public function handle(PostCreated $event): void
    {
        // Get the post from the event
        $post = $event->post;

        // For now we'll log it instead of sending real email
        // (sending real email requires mail server setup)
        Log::info('New post created: ' . $post->title . ' by ' . $post->user->name);

        // When you have mail configured, you would do:
        // Mail::to('subscribers@blog.com')->send(new NewPostMail($post));

        // Send email notification
        Mail::to('admin@blog.com')->send(new NewPostMail($post));
        
    }
}
