<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;

class IaService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('IA_SERVICE_URL', 'http://127.0.0.1:8001'), '/');
    }

    /**
     * Reconnaissance visuelle du médicament (CLIP).
     */
    public function scanMedicament(UploadedFile $file): array
    {
        $response = Http::attach(
            'file', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
        )->post("{$this->baseUrl}/scan");

        return $this->handle($response);
    }

    /**
     * Lecture du code-barres GS1 DataMatrix (lot + date d'expiration).
     */
    public function scanCodeBarres(UploadedFile $file): array
    {
        $response = Http::attach(
            'file', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
        )->post("{$this->baseUrl}/scan-code-barres");

        return $this->handle($response);
    }

    /**
     * Prévision de consommation / date de rupture estimée.
     *
     * @param array $historique  [['date' => '2026-06-01', 'quantite' => 5], ...]
     */
    public function previsionStock(array $historique, float $stockActuel, int $horizonJours = 30): array
    {
        $response = Http::post("{$this->baseUrl}/prevision-stock", [
            'historique'     => $historique,
            'stock_actuel'   => $stockActuel,
            'horizon_jours'  => $horizonJours,
        ]);

        return $this->handle($response);
    }

    /**
     * Détection d'anomalies sur les mouvements de stock (z-score).
     *
     * @param array $mouvements  [['id' => 1, 'quantite' => 5, 'date' => '2026-06-01'], ...]
     */
    public function detecterAnomalies(array $mouvements, float $seuil = 3.0): array
    {
        $response = Http::post("{$this->baseUrl}/detecter-anomalies", [
            'mouvements'   => $mouvements,
            'seuil_zscore' => $seuil,
        ]);

        return $this->handle($response);
    }

    protected function handle($response): array
    {
        if (!$response->successful()) {
            return [
                'status'  => 'error',
                'message' => 'Service IA indisponible ou erreur de traitement.',
                'code'    => $response->status(),
            ];
        }

        return $response->json();
    }
}