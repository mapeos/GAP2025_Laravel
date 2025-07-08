<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\CustomEmailNotification;
use Illuminate\Support\Facades\Notification;

class SendEmailNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-notification
                            {--subject= : The email subject}
                            {--greeting=Hello! : The email greeting}
                            {--body= : The email body content}
                            {--action-text= : The action button text}
                            {--action-url= : The action button URL}
                            {--footer=Thank you for using our application! : The footer text}
                            {--active-only : Send only to active users (status = 1)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send custom email notifications to all users or active users only';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get command options
        $subject = $this->option('subject') ?: 'Important Notification';
        $greeting = $this->option('greeting') ?: 'Hello!';
        $body = $this->option('body') ?: 'This is an important notification from our system.';
        $actionText = $this->option('action-text');
        $actionUrl = $this->option('action-url');
        $footer = $this->option('footer') ?: 'Thank you for using our application!';
        $activeOnly = $this->option('active-only');

        // Build user query
        $query = User::query();

        if ($activeOnly) {
            $query->where('status', 1);
        }

        // Get users with email addresses
        $users = $query->whereNotNull('email')
                      ->where('email', '!=', '')
                      ->get();

        if ($users->isEmpty()) {
            $this->error('No users found to send notifications to.');
            return 1;
        }

        $this->info("Found {$users->count()} users to notify.");

        // Confirm before sending
        if (!$this->confirm('Do you want to send the notification to all these users?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        // Create the notification
        $notification = new CustomEmailNotification(
            $subject,
            $greeting,
            $body,
            $actionText,
            $actionUrl,
            $footer
        );

        // Send notifications
        $this->info('Sending notifications...');
        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($users as $user) {
            try {
                $user->notify($notification);
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $this->newLine();
                $this->error("Failed to send notification to {$user->email}: " . $e->getMessage());
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Show results
        $this->info("Notification sending completed!");
        $this->info("✅ Successfully sent: {$successCount}");

        if ($errorCount > 0) {
            $this->error("❌ Failed to send: {$errorCount}");
        }

        return 0;
    }
}
