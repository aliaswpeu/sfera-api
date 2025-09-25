<?php

namespace Aliaswpeu\SferaApi\Services;

use COM;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

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
            $this->gt = new COM("InsERT.GT", null, CP_UTF8) or die("Cannot create Subiekt GT COM object");
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
     * @param Request $request
     * @return array
     */
    public function createKontrahent(Request $request): array
    {
        $fields = [
            'Symbol',
            'Nazwa',
            'NazwaPelna',
            'Wojewodztwo',
            'Ulica',
            'NrDomu',
            'NrLokalu',
            'KodPocztowy',
            'Miejscowosc',
            'Panstwo',
            'NIP',
            'Pole1',
            'Pole2',
            'Pole3',
            'Pole4',
            'Pole5',
            'Pole6',
            'Pole7',
            'Pole8',
            'KhSklepuInternetowego',
            'Email',
            'OpiekunId',
            'AdrDostNazwa',
            'AdrDostUlica',
            'AdrDostNrDomu',
            'AdrDostKodPocztowy',
            'AdrDostMiejscowosc',
            'AdrDostPanstwo',
        ];
        $data = $request->only($fields);

        try {
            $Sgt = $this->gt->Uruchom(0, 4);
            $Okh = $Sgt->KontrahenciManager->DodajKontrahenta();
            $this->setKontrahentProperties($Okh, $data);
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
     * @param object  $Okh
     * @param array   $data
     */
    private function setKontrahentProperties($Okh, array $data): void
    {
        $map = [
            'Symbol' => 'Symbol',
            'Nazwa' => 'Nazwa',
            'Wojewodztwo' => 'Wojewodztwo',
            'Ulica' => 'Ulica',
            'NrDomu' => 'NrDomu',
            'NrLokalu' => 'NrLokalu',
            'KodPocztowy' => 'KodPocztowy',
            'Miejscowosc' => 'Miejscowosc',
            'Panstwo' => 'Panstwo',
            'NIP' => 'NIP',
            'NazwaPelna' => 'NazwaPelna',
            'OpiekunId' => 'OpiekunId',
            'Pole1' => 'Pole1',
            'Pole2' => 'Pole2',
            'Pole3' => 'Pole3',
            'Pole4' => 'Pole4',
            'Pole5' => 'Pole5',
            'Pole6' => 'Pole6',
            'Pole7' => 'Pole7',
            'Pole8' => 'Pole8',
            'KhSklepuInternetowego' => 'KhSklepuInternetowego',
            'Email' => 'Email',
        ];

        foreach ($map as $key => $prop) {
            if (Arr::has($data, $key)) {
                $Okh->$prop = $data[$key];
            }
        }

        /* if (!empty($data['Email'])) {
            $Okh->Emaile()->Dodaj($data['Email']);
        } */

        $deliveryFields = [
            'AdrDostNazwa',
            'AdrDostUlica',
            'AdrDostNrDomu',
            'AdrDostKodPocztowy',
            'AdrDostMiejscowosc',
            'AdrDostPanstwo',
        ];

        if (collect($deliveryFields)->some(fn($field) => Arr::has($data, $field))) {
            $Okh->AdresDostawy = true;
            foreach ($deliveryFields as $field) {
                if (Arr::has($data, $field)) {
                    $Okh->$field = $data[$field];
                }
            }
        }
    }

    /**
     * Creates a new product (towar) in Subiekt GT.
     *
     * @param Request $request
     * @return array
     */
    public function createTowar(Request $request): array
    {
        $fields = ['Symbol', 'Nazwa', 'Opis'];
        $data = $request->only($fields);

        try {
            $Sgt = $this->gt->Uruchom(0, 4);
            $Otw = $Sgt->TowaryManager->DodajTowar();
            $this->setTowarProperties($Otw, $data);
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
     * @param array  $data
     */
    private function setTowarProperties($Otw, array $data): void
    {
        $map = ['Symbol' => 'Symbol', 'Nazwa' => 'Nazwa', 'Opis' => 'Opis'];

        foreach ($map as $key => $prop) {
            if (Arr::has($data, $key)) {
                $Otw->$prop = $data[$key];
            }
        }
    }
}
