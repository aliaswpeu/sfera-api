<?php

namespace Aliaswpeu\SferaApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Aliaswpeu\SferaApi\Services\SubiektGTService;

class SubiektGTController extends Controller
{
    protected $subiektGTService;

    public function __construct(SubiektGTService $subiektGTService)
    {
        $this->subiektGTService = $subiektGTService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
//         $request['Symbol'];
// $request['Nazwa'];
// $request['Email']);
// $request['Pole1'];
// $request['AdrDostNazwa'];
// $request['AdrDostUlica'];
// $request['AdrDostNrDomu'];
// $request['AdrDostKodPocztowy'];
// $request['AdrDostMiejscowosc'];
// $request['AdrDostPanstwo'];
        $data = $this->subiektGTService->createKontrahent($request->all());
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
