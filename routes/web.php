<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Admin\UserController;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;

/* use EchoLabs\Prism\Prism;
use EchoLabs\Prism\Enums\Provider;
*/

/*
Prism LLM integration example. For more details on setting up Prism, see the documentation:
https://prismphp.com/getting-started/installation.html
*/
use Prism\Prism\Facades\Prism;
use Prism\Prism\Enums\Provider;

use Prism\Prism\Facades\Tool;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Add this line
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
});

// Public blog routes
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return 'Welcome Admin!';
    })->name('dashboard');

    // Admin post management
    //Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Add this line
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
});

Route::get('/agent-test', function () {
    /* $response = Prism::text()
        ->using(Provider::Gemini, 'gemini-2.5-flash')
        ->withPrompt('Say hello in one sentence.')
        ->asText();

    return $response->text; */

    $postTool = Tool::as('get_post_status')
        ->for('Get Post Status')
        ->withStringParameter('post_id', 'The ID of the post to get status for')
        ->using(function (string $post_id): string {
            //$post = Post::find($post_id);
            $post = Auth::user()->posts()->find($post_id);
            if (!$post) {
                return "Post with ID {$post_id} not found.";
            }

            return "The status of post {$post_id} is {$post->status}.";
        });

    $allPostTool = Tool::as('get_all_posts')
        ->for('Get All Posts')
        ->using(function (): string {
            /* $posts = Auth::user()->posts()->get();
            return $posts->toArray(); */
            $posts = Auth::user()->posts()->get();
            if ($posts->isEmpty()) {
                return "You have no posts.";
            }

            return $posts->map(function ($post) {
                return "ID: {$post->id} | Title: {$post->title} | Status: {$post->status}";
            })->implode("\n");
        });

    $response = Prism::text()
        //->using(Provider::Gemini, 'gemini-2.5-flash')
        ->using(Provider::Gemini, 'gemini-3-flash-preview')
        ->withMaxSteps(2)
        ->withPrompt('What posts do I have and what is the status of post 10?')
        ->withTools([$postTool, $allPostTool])
        ->asText();
    return $response->text;
});

require __DIR__ . '/auth.php';
