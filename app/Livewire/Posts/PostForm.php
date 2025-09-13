<?php

namespace App\Livewire\Posts;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PostForm extends Component
{
    public $statusMessage = '';

    #[Validate('required|string|max:255')]
    public $title = '';

    #[Validate('required|string')]
    public $content = '';

    public function mount()
    {
        $this->updateStatus();
    }

    public function savePost()
    {
        $this->validate();

        $connection = $this->isOnline ? 'mysql' : 'sqlite';

        try {
            DB::connection($connection)->table('posts')->insert([
                'uuid' => Str::uuid(),
                'title' => $this->title,
                'content' => $this->content,
                'is_synced' => ($connection == 'mysql'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->statusMessage = "Post saved successfully to {$connection}!";
            $this->reset(['title', 'content']);
            $this->updateStatus();
        } catch (\Throwable $th) {
            Log::error('Failed to save post: '.$th->getMessage());
            $this->statusMessage = 'Error saving post. Please try again.';
        }
    }

    // Synchronize data from SQLite to MySQL.
    public function syncData()
    {
        Log::info('Synchronization started.');

        if (! $this->isOnline()) {
            $this->statusMessage = 'Cannot sync: You are offline.';

            return;
        }

        try {
            $offlinePosts = DB::connection('sqlite')
                ->table('posts')
                ->where('is_synced', false)
                ->get();

            if ($offlinePosts->isEmpty()) {
                $this->statusMessage = 'No unsynced posts found.';

                return;
            }

            foreach ($offlinePosts as $post) {
                // Check if a record with the same UUID exists in the MySQL database.
                $existingPost = DB::connection('mysql')->table('posts')
                    ->where('uuid', $post->uuid)
                    ->first();

                if ($existingPost) {
                    // Conflict resolution: Last-write-wins based on updated_at timestamp.
                    if ($post->updated_at > $existingPost->updated_at) {
                        DB::connection('mysql')->table('posts')
                            ->where('id', $existingPost->id)
                            ->update([
                                'title' => $post->title,
                                'content' => $post->content,
                                'is_synced' => true,
                                'updated_at' => $post->updated_at,
                            ]);
                    }
                } else {
                    // No conflict, insert the new record.
                    DB::connection('mysql')->table('posts')->insert([
                        'uuid' => $post->uuid,
                        'title' => $post->title,
                        'content' => $post->content,
                        'is_synced' => true,
                        'created_at' => $post->created_at,
                        'updated_at' => $post->updated_at,
                    ]);
                }

                // Mark the post as synced in SQLite to avoid resyncing.
                DB::connection('sqlite')->table('posts')
                    ->where('uuid', $post->uuid)
                    ->update(['is_synced' => true]);
            }

            $this->statusMessage = 'Synchronization complete.';

        } catch (\Throwable $e) {
            Log::error('Synchronization failed: '.$e->getMessage());
            $this->statusMessage = 'Synchronization failed. Check logs for details.';
        }
    }

    private function isOnline()
    {
        try {
            // Attempt to get a PDO instance for the MySQL connection.
            // If this fails, we are likely offline.
            DB::connection('mysql')->getPdo();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateStatus()
    {
        if ($this->isOnline()) {
            $this->statusMessage = 'Status: Online. Ready to sync.';
        } else {
            $this->statusMessage = 'Status: Offline. Saving locally.';
        }
    }

    public function render()
    {
        return view('livewire.posts.post-form');
    }
}
