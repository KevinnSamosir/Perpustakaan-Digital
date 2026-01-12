<?php

namespace App\Console\Commands;

use App\Models\Loan;
use Illuminate\Console\Command;

class ExpireEbookAccess extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ebook:expire-access';

    /**
     * The console command description.
     */
    protected $description = 'Automatically expire e-book access that has passed the expiry date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredLoans = Loan::expiredEbooks()->with(['book', 'member'])->get();

        $count = 0;
        foreach ($expiredLoans as $loan) {
            $loan->endEbookAccess();
            $count++;
            $this->info("Expired access for: {$loan->book->title} - Member: {$loan->member->user->name}");
        }

        $this->info("Total e-book access expired: {$count}");

        return Command::SUCCESS;
    }
}
