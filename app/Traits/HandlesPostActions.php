<?php

namespace App\Traits;

use App\Models\Post;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

trait HandlesPostActions
{
    use WithFileUploads;

    public $repostingPostId = null;
    public $repostContent = '';
    public $repostImage = null;
    public $repostVideo = null;
    public $showRepostModal = false;

    public $showReportModal = false;
    public $reportPostId = null;
    public $reportReason = '';

    public function openRepostModal($postId)
    {
        if (!auth()->user()->isArtisan()) {
            $this->dispatch('toast', type: 'error', title: 'Permission Denied', message: 'Only artisans can repost.');
            return;
        }
        $this->repostingPostId = $postId;
        $this->repostContent = '';
        $this->repostImage = null;
        $this->repostVideo = null;
        $this->showRepostModal = true;
    }

    public function createRepost()
    {
        try {
            $this->validate([
                'repostContent' => 'nullable|string|max:1000',
                'repostImage' => 'nullable|image|max:10240',
                'repostVideo' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:10240',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            foreach ($e->validator->errors()->getMessages() as $field => $messages) {
                if (str_contains($field, 'repostImage') || str_contains($field, 'repostVideo')) {
                    if (str_contains(implode(' ', $messages), 'kilobytes') || str_contains(implode(' ', $messages), 'large')) {
                        $this->dispatch('toast', type: 'error', title: 'File Too Large', message: 'Images and videos must be less than 10MB.');
                        break;
                    }
                }
            }
            throw $e;
        }

        $imagePath = $this->repostImage ? $this->repostImage->store('posts/images', 'public') : null;
        $videoPath = $this->repostVideo ? $this->repostVideo->store('posts/videos', 'public') : null;

        $post = Post::create([
            'user_id' => auth()->id(),
            'repost_of_id' => $this->repostingPostId,
            'content' => $this->repostContent,
            'images' => $imagePath,
            'video' => $videoPath,
        ]);

        if (method_exists($this, 'notifyMentionedUsers')) {
            $this->notifyMentionedUsers($post);
        }

        $this->showRepostModal = false;

        if (method_exists($this, 'loadPosts')) {
            $this->loadPosts(true);
        }

        $this->dispatch('toast', type: 'success', title: 'Reposted!', message: 'Your repost has been published.');
    }

    public function openReportModal($postId)
    {
        $this->reportPostId = $postId;
        $this->reportReason = '';
        $this->showReportModal = true;
    }

    public function submitReport()
    {
        $this->validate([
            'reportReason' => 'required|string|min:10|max:500',
        ]);

        Report::create([
            'user_id' => auth()->id(),
            'post_id' => $this->reportPostId,
            'reason' => $this->reportReason,
            'status' => 'pending',
        ]);

        $this->showReportModal = false;
        $this->dispatch('toast', type: 'success', title: 'Report Submitted', message: 'Thank you for reporting. We will review this post.');
    }
}
