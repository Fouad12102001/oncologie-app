<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Client\ConnectionException;

class IaService
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('IA_SERVICE_URL', 'http://127.0.0.1:8001'), '/');
        $this->timeout = (int) env('IA_SERVICE_TIMEOUT', 20);
    }

    /**
     * Reconnaissance visuelle du médicament (CLIP).
     */
    public function scanMedicament(UploadedFile $file): array
    {
        return $this->safePost(
            fn () => Http::timeout($this->timeout)->attach(
                'file', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
            )->post("{$this->baseUrl}/scan")
        );
    }

    /**
     * Lecture du code-barres GS1 DataMatrix (lot + date d'expiration).
     */
    public function scanCodeBarres(UploadedFile $file): array
    {
        return $this->safePost(
            fn () => Http::timeout($this->timeout)->attach(
                'file', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
            )->post("{$this->baseUrl}/scan-code-barres")
        );
    }

    /**
     * Prévision de consommation / date de rupture estimée.
     *
     * @param array $historique  [['date' => '2026-06-01', 'quantite' => 5], ...]
     */
    public function previsionStock(array $historique, float $stockActuel, int $horizonJours = 30): array
    {
        return $this->safePost(
            fn () => Http::timeout($this->timeout)->post("{$this->baseUrl}/prevision-stock", [
                'historique'    => $historique,
                'stock_actuel'  => $stockActuel,
                'horizon_jours' => $horizonJours,
            ])
        );
    }

    /**
     * Détection d'anomalies sur les mouvements de stock (z-score).
     *
     * @param array $mouvements  [['id' => 1, 'quantite' => 5, 'date' => '2026-06-01'], ...]
     */
    public function detecterAnomalies(array $mouvements, float $seuil = 3.0): array
    {
        return $this->safePost(
            fn () => Http::timeout($this->timeout)->post("{$this->baseUrl}/detecter-anomalies", [
                'mouvements'   => $mouvements,
                'seuil_zscore' => $seuil,
            ])
        );
    }

    /**
     * Exécute l'appel HTTP en capturant toute exception réseau
     * (service Python arrêté, timeout, DNS, etc.) pour toujours
     * renvoyer un tableau JSON exploitable côté front, plutôt
     * qu'une exception PHP non catchée qui produirait une page
     * d'erreur HTML (et casserait le fetch()/res.json() côté JS).
     */
    protected function safePost(\Closure $call): array
    {
        try {
            $response = $call();
            return $this->handle($response);
        } catch (ConnectionException $e) {
            Log::warning('IaService: service IA injoignable', ['error' => $e->getMessage()]);
            return [
                'status'  => 'error',
                'message' => "⛔ Le service IA (FastAPI) n'est pas joignable à {$this->baseUrl}. "
                           . "Vérifiez qu'il est bien démarré (uvicorn main:app --port 8001).",
            ];
        } catch (\Throwable $e) {
            Log::error('IaService: erreur inattendue', ['error' => $e->getMessage()]);
            return [
                'status'  => 'error',
                'message' => 'Erreur interne lors de l\'appel au service IA.',
            ];
        }
    }

    protected function handle($response): array
    {
        if (!$response->successful()) {
            return [
                'status'  => 'error',
                'message' => 'Service IA indisponible ou erreur de traitement (HTTP ' . $response->status() . ').',
                'code'    => $response->status(),
            ];
        }

        return $response->json();
    }
}