<?php

namespace Aliaswpeu\SferaApi\Services;

use COM;
use Illuminate\Support\Facades\Log;

class SubiektGTService
{
    protected COM $gt;

    /**
     * SubiektGTService constructor.
     * Initializes the Subiekt GT COM object.
     */
    public function __construct()
    {
        $this->initializeGT();
    }

    /**
     * Initialize COM object for Subiekt GT
     */
    private function initializeGT(): void
    {
        try {
            $this->gt = new COM("InsERT.GT") or die("Cannot create Subiekt GT COM object");
            $this->gt->Produkt = 1;
            $this->gt->Serwer = config('sfera-api.sfera_server');
            $this->gt->Baza = config('sfera-api.sfera_database');
            $this->gt->Autentykacja = 0;
            $this->gt->Uzytkownik = config('sfera-api.sfera_user');
            $this->gt->UzytkownikHaslo = config('sfera-api.sfera_password');
            $this->gt->Operator = config('sfera-api.sfera_operator');
            $this->gt->OperatorHaslo = config('sfera-api.sfera_operator_password');
        } catch (\Throwable $e) {
            Log::error('Failed to initialize Subiekt GT COM object: ' . $e->getMessage());
            throw new \Exception('Subiekt GT initialization failed');
        }
    }

    /**
     * Creates a new customer (kontrahent) in Subiekt GT.
     *
     * @param array $request
     * @return int|string
     */
    public function createKontrahent(array $request): array|string
    {
        try {
            $Sgt = $this->gt->Uruchom(0, 4);
            $Okh = $Sgt->KontrahenciManager->DodajKontrahenta();
            $this->setKontrahentProperties($Okh, $request);
            $Okh->Zapisz();

            return ['kh_Id' => $Okh->Identyfikator()];
        } catch (\Throwable $e) {
            Log::error('Failed to create Kontrahent: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Set Kontrahent properties from request data
     *
     * @param object $Okh
     * @param array $request
     */
    private function setKontrahentProperties($Okh, array $request): void
    {
        $Okh->Symbol = $request['Symbol'];
        $Okh->Nazwa = $request['Nazwa'];

        $OkhMaile = $Okh->Emaile();
        $OkhMaile->Dodaj($request['Email']);

        $Okh->Pole1 = $request['Pole1'];
        $Okh->AdresDostawy = true;
        $Okh->AdrDostNazwa = $request['AdrDostNazwa'];
        $Okh->AdrDostUlica = $request['AdrDostUlica'];
        $Okh->AdrDostNrDomu = $request['AdrDostNrDomu'];
        $Okh->AdrDostKodPocztowy = $request['AdrDostKodPocztowy'];
        $Okh->AdrDostMiejscowosc = $request['AdrDostMiejscowosc'];
        $Okh->AdrDostPanstwo = $request['AdrDostPanstwo'];
    }

    /**
     * Creates a new product (towar) in Subiekt GT.
     *
     * @param array $request
     * @return int|string
     */
    public function createTowar(array $request): array|string
    {
        try {
            $Sgt = $this->gt->Uruchom(0, 4);
            $Otw = $Sgt->TowaryManager->DodajTowar();
            $this->setTowarProperties($Otw, $request);
            $Otw->Zapisz();

            return ['tw_Id' => $Otw->Identyfikator()];
        } catch (\Throwable $e) {
            Log::error('Failed to create Towar: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Set Towar properties from request data
     *
     * @param object $Otw
     * @param array $request
     */
    private function setTowarProperties($Otw, array $request): void
    {
        $Otw->Symbol = $request['Symbol'];
        $Otw->Nazwa = $request['Nazwa'];
        $Otw->Opis = $request['Opis'];
    }
}
