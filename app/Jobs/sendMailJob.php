<?php

namespace App\Jobs;

use App\Mail\NotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class sendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $data;

    public function __construct($data)
    {
        //
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function middleware()
    {
        $uniqueKey = md5($this->data['email'] . $this->data['message']);
        return [
            (new WithoutOverlapping($uniqueKey))->releaseAfter(120),
        ];
    }

    public function handle()
    {
        //
        Mail::to($this->data['email'])->send(new NotificationMail($this->data['name'], $this->data['url'], $this->data['message'], $this->data['spk_number'] ?? 1));
    }

    public function failed(\Exception $exception)
    {
        // Handle the failure (e.g., log the error or notify admin)
        \Log::error('SendMailJob failed', [
            'error' => $exception->getMessage(),
            'data' => $this->data,
        ]);
    }
}
