<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Loan;
use App\Models\Notification;
use App\Models\Setting;
use Carbon\Carbon;

class CheckOverdueLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for overdue loans and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for overdue loans...');

        // 1. Update status for newly overdue loans
        $newlyOverdue = Loan::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->get();

        foreach ($newlyOverdue as $loan) {
            $loan->update(['status' => 'overdue']);
            
            // Calculate fine
            $finePerDay = Setting::get('fine_per_day', 1000);
            $daysLate = now()->diffInDays($loan->due_date);
            $fine = $daysLate * $finePerDay;
            
            // Send notification
            Notification::send(
                $loan->member->user_id,
                'overdue',
                'Buku Terlambat!',
                "Buku '{$loan->book->title}' sudah melewati batas waktu pengembalian ({$daysLate} hari). " .
                "Denda sementara: Rp " . number_format($fine, 0, ',', '.') . ". " .
                "Segera kembalikan buku untuk menghindari denda bertambah.",
                ['loan_id' => $loan->id, 'days_late' => $daysLate, 'fine' => $fine]
            );
            
            $this->line("- Loan #{$loan->id}: {$loan->book->title} marked as overdue");
        }

        // 2. Send reminders for loans due soon (3 days before)
        $dueSoon = Loan::where('status', 'borrowed')
            ->whereDate('due_date', '=', now()->addDays(3)->toDateString())
            ->get();

        foreach ($dueSoon as $loan) {
            Notification::send(
                $loan->member->user_id,
                'reminder',
                'Pengingat: 3 Hari Lagi!',
                "Buku '{$loan->book->title}' harus dikembalikan dalam 3 hari (tanggal " . 
                $loan->due_date->format('d M Y') . "). Jangan lupa kembalikan tepat waktu!",
                ['loan_id' => $loan->id]
            );
            
            $this->line("- Reminder sent for Loan #{$loan->id}: {$loan->book->title} (3 days)");
        }

        // 3. Send reminders for loans due tomorrow
        $dueTomorrow = Loan::where('status', 'borrowed')
            ->whereDate('due_date', '=', now()->addDay()->toDateString())
            ->get();

        foreach ($dueTomorrow as $loan) {
            Notification::send(
                $loan->member->user_id,
                'reminder',
                'Pengingat: Besok Jatuh Tempo!',
                "Buku '{$loan->book->title}' harus dikembalikan BESOK (tanggal " . 
                $loan->due_date->format('d M Y') . "). Segera kembalikan untuk menghindari denda!",
                ['loan_id' => $loan->id]
            );
            
            $this->line("- Reminder sent for Loan #{$loan->id}: {$loan->book->title} (tomorrow)");
        }

        // 4. Send reminders for loans due today
        $dueToday = Loan::where('status', 'borrowed')
            ->whereDate('due_date', '=', now()->toDateString())
            ->get();

        foreach ($dueToday as $loan) {
            Notification::send(
                $loan->member->user_id,
                'reminder',
                'Pengingat: Hari Ini Jatuh Tempo!',
                "Buku '{$loan->book->title}' harus dikembalikan HARI INI. " .
                "Kembalikan sebelum jam 23:59 untuk menghindari denda!",
                ['loan_id' => $loan->id]
            );
            
            $this->line("- Reminder sent for Loan #{$loan->id}: {$loan->book->title} (today)");
        }

        $this->info('');
        $this->info('Summary:');
        $this->info("- Newly overdue: {$newlyOverdue->count()}");
        $this->info("- Reminders (3 days): {$dueSoon->count()}");
        $this->info("- Reminders (tomorrow): {$dueTomorrow->count()}");
        $this->info("- Reminders (today): {$dueToday->count()}");
        $this->info('');
        $this->info('Done!');

        return Command::SUCCESS;
    }
}
