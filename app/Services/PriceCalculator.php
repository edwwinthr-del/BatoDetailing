<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Vehicle;
use Illuminate\Support\Collection;

class PriceCalculator
{
    public function __construct(
        protected SettingsService $settings,
        protected LoyaltyService $loyalty,
    ) {}

    /**
     * Calculate the price breakdown for a set of services on a vehicle.
     *
     * @param  Collection<int, Service>  $services
     * @return array{subtotal: float, type_modifier: float, discount: float, total: float, points_redeemed: int}
     */
    public function calculate(Vehicle $vehicle, Collection $services, int $pointsToRedeem = 0): array
    {
        $subtotal = round((float) $services->sum('base_price'), 2);
        $modifier = $this->settings->modifierFor($vehicle->type);

        $grossTotal = $subtotal + $modifier;

        // Discount can never exceed the gross total; trim redeemed points to match.
        $redeemValue = $this->loyalty->redeemValue();
        $maxRedeemablePoints = $redeemValue > 0 ? (int) floor($grossTotal / $redeemValue) : 0;
        $pointsRedeemed = max(0, min($pointsToRedeem, $maxRedeemablePoints));
        $discount = round($pointsRedeemed * $redeemValue, 2);

        return [
            'subtotal' => $subtotal,
            'type_modifier' => $modifier,
            'discount' => $discount,
            'total' => round($grossTotal - $discount, 2),
            'points_redeemed' => $pointsRedeemed,
        ];
    }
}
