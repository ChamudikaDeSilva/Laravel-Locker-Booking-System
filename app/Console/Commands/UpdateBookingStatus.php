<?php

namespace App\Console\Commands;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateBookingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature ='booking:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description ='Update booking status when end time is exceeded';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        $bookings = Booking::all();

        foreach ($bookings as $booking) {
            $now = now();
            if ($now >= $booking->start_time && $now <= $booking->end_time) {
                $booking->status = 'processing';
            } elseif ($now > $booking->end_time) {
                $booking->status = 'completed';
            } else {
                $booking->status = 'active';
            }

            $booking->save();
        }

        $this->info('Booking status updated successfully.');
    }
}
