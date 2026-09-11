<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Review;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * @return array{revenue: float, invoiceCount: int, appointmentCount: int, completedCount: int, averageRating: float, reviewCount: int, servicePopularity: Collection}
     */
    public function build(Carbon $from, Carbon $to): array
    {
        $invoices = Invoice::whereBetween('issued_at', [$from, $to])
            ->whereNotIn('status', [Invoice::STATUS_CANCELLED, Invoice::STATUS_DRAFT]);

        $reviews = Review::whereBetween('created_at', [$from, $to]);

        return [
            'revenue' => round((float) $invoices->clone()->sum('total'), 2),
            'invoiceCount' => $invoices->clone()->count(),
            'appointmentCount' => Appointment::whereBetween('scheduled_at', [$from, $to])->count(),
            'completedCount' => Appointment::whereBetween('scheduled_at', [$from, $to])
                ->where('status', Appointment::STATUS_COMPLETED)
                ->count(),
            'averageRating' => round((float) $reviews->clone()->avg('rating'), 2),
            'reviewCount' => $reviews->clone()->count(),
            'servicePopularity' => $this->servicePopularity($from, $to),
        ];
    }

    protected function servicePopularity(Carbon $from, Carbon $to): Collection
    {
        return DB::table('appointment_services')
            ->join('services', 'services.id', '=', 'appointment_services.service_id')
            ->join('appointments', 'appointments.id', '=', 'appointment_services.appointment_id')
            ->whereBetween('appointments.scheduled_at', [$from, $to])
            ->whereNotIn('appointments.status', [Appointment::STATUS_CANCELLED, Appointment::STATUS_REJECTED])
            ->groupBy('services.id', 'services.name')
            ->orderByDesc(DB::raw('count(*)'))
            ->select([
                'services.name',
                DB::raw('count(*) as bookings'),
                DB::raw('sum(appointment_services.price) as revenue'),
            ])
            ->get();
    }
}
