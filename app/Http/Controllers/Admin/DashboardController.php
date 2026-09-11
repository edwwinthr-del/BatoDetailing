<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $months = collect(range(11, 0))
            ->map(fn (int $offset) => now()->subMonths($offset)->startOfMonth());

        $revenueByMonth = $months->map(fn (Carbon $month): float => (float) Invoice::whereBetween('issued_at', [$month, $month->copy()->endOfMonth()])
            ->whereNotIn('status', [Invoice::STATUS_CANCELLED, Invoice::STATUS_DRAFT])
            ->sum('total'));

        $appointmentsByMonth = $months->map(fn (Carbon $month): int => Appointment::whereBetween('scheduled_at', [$month, $month->copy()->endOfMonth()])->count());

        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'totalAppointments' => Appointment::count(),
            'pendingAppointments' => Appointment::where('status', Appointment::STATUS_PENDING)->count(),
            'totalRevenue' => (float) Invoice::whereNotIn('status', [Invoice::STATUS_CANCELLED, Invoice::STATUS_DRAFT])->sum('total'),
            'satisfaction' => round((float) Review::avg('rating'), 2),
            'reviewCount' => Review::count(),
            'chartLabels' => $months->map(fn (Carbon $month): string => $month->format('M Y')),
            'chartRevenue' => $revenueByMonth,
            'chartAppointments' => $appointmentsByMonth,
        ]);
    }
}
