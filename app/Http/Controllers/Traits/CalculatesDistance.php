<?php

namespace App\Http\Controllers\Traits;

trait CalculatesDistance
{
    /**
     * Menghitung jarak optimal, memilih metode terbaik berdasarkan perkiraan.
     */
    private function calculateOptimalDistance($lat1, $lon1, $lat2, $lon2): float
    {
        if (!$this->isValidCoordinate($lat1, $lon1) || !$this->isValidCoordinate($lat2, $lon2)) {
            throw new \InvalidArgumentException('Koordinat yang diberikan tidak valid.');
        }

        // Untuk jarak sangat pendek (< 500m), Equirectangular cukup cepat dan akurat.
        $roughDistance = abs($lat1 - $lat2) + abs($lon1 - $lon2);
        if ($roughDistance < 0.005) { // Kira-kira setara dengan ~500m
            return $this->calculateDistanceEquirectangular($lat1, $lon1, $lat2, $lon2);
        }

        // Untuk akurasi maksimum pada jarak lainnya, gunakan Vincenty.
        return $this->calculateDistanceVincenty($lat1, $lon1, $lat2, $lon2);
    }

    /**
     * Memvalidasi apakah koordinat berada dalam rentang yang valid.
     */
    private function isValidCoordinate($latitude, $longitude): bool
    {
        return is_numeric($latitude) && is_numeric($longitude) &&
            ($latitude >= -90 && $latitude <= 90) &&
            ($longitude >= -180 && $longitude <= 180);
    }

    /**
     * Formula Vincenty (Paling Akurat)
     * Menggunakan ellipsoid WGS84 untuk akurasi maksimum.
     */
    private function calculateDistanceVincenty($lat1, $lon1, $lat2, $lon2): float
    {
        $a = 6378137.0; // semi-major axis (meter)
        $b = 6356752.314245; // semi-minor axis (meter)
        $f = 1 / 298.257223563; // flattening

        $L = deg2rad($lon2 - $lon1);
        $U1 = atan((1 - $f) * tan(deg2rad($lat1)));
        $U2 = atan((1 - $f) * tan(deg2rad($lat2)));
        $sinU1 = sin($U1);
        $cosU1 = cos($U1);
        $sinU2 = sin($U2);
        $cosU2 = cos($U2);

        $lambda = $L;
        $lambdaP = 2 * M_PI;
        $iterLimit = 100;

        do {
            $sinLambda = sin($lambda);
            $cosLambda = cos($lambda);
            $sinSigma = sqrt(($cosU2 * $sinLambda) ** 2 + ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda) ** 2);
            if ($sinSigma == 0) return 0.0;
            $cosSigma = $sinU1 * $sinU2 + $cosU1 * $cosU2 * $cosLambda;
            $sigma = atan2($sinSigma, $cosSigma);
            $sinAlpha = $cosU1 * $cosU2 * $sinLambda / $sinSigma;
            $cosSqAlpha = 1 - $sinAlpha ** 2;
            $cos2SigmaM = ($cosSqAlpha == 0) ? 0 : $cosSigma - 2 * $sinU1 * $sinU2 / $cosSqAlpha;
            $C = $f / 16 * $cosSqAlpha * (4 + $f * (4 - 3 * $cosSqAlpha));
            $lambdaP = $lambda;
            $lambda = $L + (1 - $C) * $f * $sinAlpha * ($sigma + $C * $sinSigma * ($cos2SigmaM + $C * $cosSigma * (-1 + 2 * $cos2SigmaM ** 2)));
        } while (abs($lambda - $lambdaP) > 1e-12 && --$iterLimit > 0);

        if ($iterLimit == 0) return $this->calculateDistanceHaversine($lat1, $lon1, $lat2, $lon2); // fallback jika gagal konvergen

        $uSq = $cosSqAlpha * ($a ** 2 - $b ** 2) / ($b ** 2);
        $A = 1 + $uSq / 16384 * (4096 + $uSq * (-768 + $uSq * (320 - 175 * $uSq)));
        $B = $uSq / 1024 * (256 + $uSq * (-128 + $uSq * (74 - 47 * $uSq)));
        $deltaSigma = $B * $sinSigma * ($cos2SigmaM + $B / 4 * ($cosSigma * (-1 + 2 * $cos2SigmaM ** 2) - $B / 6 * $cos2SigmaM * (-3 + 4 * $sinSigma ** 2) * (-3 + 4 * $cos2SigmaM ** 2)));

        return round($b * $A * ($sigma - $deltaSigma), 3);
    }

    // Metode Haversine dan Equirectangular sebagai fallback atau opsi
    private function calculateDistanceHaversine($lat1, $lon1, $lat2, $lon2): float
    {
        // Radius bumi dalam meter
        $earthRadius = 6371000;

        // Konversi derajat ke radian
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 3);
    }
    private function calculateDistanceEquirectangular($lat1, $lon1, $lat2, $lon2): float
    {
        // Radius bumi dalam meter
        $earthRadius = 6371000;

        // Konversi derajat ke radian
        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);
        $lon1 = deg2rad($lon1);
        $lon2 = deg2rad($lon2);

        $x = ($lon2 - $lon1) * cos(($lat1 + $lat2) / 2);
        $y = $lat2 - $lat1;
        $distance = sqrt($x * $x + $y * $y) * $earthRadius;

        return round($distance, 3);
    }
}
