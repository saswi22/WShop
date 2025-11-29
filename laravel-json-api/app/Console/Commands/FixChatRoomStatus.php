<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ChatRoom;

class FixChatRoomStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:fix-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix existing chat room status to active';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing chat room status...');
        
        // Update all queued rooms to active and reset queue position
        $updated = ChatRoom::where('status', 'queued')
            ->update([
                'status' => 'active',
                'queue_position' => null
            ]);
        
        $this->info("Updated {$updated} chat rooms to active status.");
        
        // Show current status
        $active = ChatRoom::where('status', 'active')->count();
        $queued = ChatRoom::where('status', 'queued')->count();
        $closed = ChatRoom::where('status', 'closed')->count();
        
        $this->table(
            ['Status', 'Count'],
            [
                ['Active', $active],
                ['Queued', $queued],
                ['Closed', $closed],
            ]
        );
        
        return 0;
    }
}
