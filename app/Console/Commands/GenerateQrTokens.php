<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LoyaltyUser;
use Illuminate\Support\Str;

class GenerateQrTokens extends Command
{
    protected $signature = 'loyalty:generate-qr-tokens';
    protected $description = 'Generate secure QR tokens for users without one';

    public function handle()
    {
        $users = LoyaltyUser::whereNull('qr_token')->get();
        $updated = 0;

        foreach ($users as $user) {
            do {
                $token = bin2hex(random_bytes(32)); // 64 hex chars, cryptographically secure
            } while (LoyaltyUser::where('qr_token', $token)->exists());

            $user->qr_token = $token;
            $user->save();
            $updated++;
        }

        $this->info("Updated {$updated} users with QR tokens.");
    }
}
