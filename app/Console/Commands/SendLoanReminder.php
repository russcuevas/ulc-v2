<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendLoanReminder extends Command
{
    // Command signature to run it via artisan
    protected $signature = 'loan:reminder';

    protected $description = 'Send daily loan reminders for clients based on loan start date';

    public function handle()
    {
        $today = Carbon::today();

        // Get clients whose loan starts today or already started
        $loans = DB::table('clients_loans')
            ->join('clients', 'clients_loans.client_id', '=', 'clients.id')
            ->whereDate('clients_loans.loan_from', '=', $today)
            ->where('clients_loans.payment_status', 'unpaid')
            ->select('clients.fullname', 'clients.phone', 'clients_loans.loan_from', 'clients_loans.loan_to', 'clients_loans.loan_amount')
            ->get();

        foreach ($loans as $loan) {
            $message = "Magandang araw {$loan->fullname}, ngayon ay simula ng loan mo ({$loan->loan_from}). Makakatanggap ka ng text araw-araw bilang paalala sa payment. Salamat!";
            Log::info("Sending reminder to {$loan->phone}: {$message}");
            $this->info("Reminder for {$loan->fullname} ({$loan->phone}): {$message}");

            // In production, here you would call your SMS service
            // SMS::send($loan->phone, $message);
        }

        $this->info('Loan reminders processed successfully.');
    }
}
