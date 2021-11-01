<?php

namespace App\Http\Controllers;

use App\Laravue\JsonResponse;
use App\Models\TokenTransaction;
use Illuminate\Http\Request;

class TokenTransactionController extends Controller
{
    public function getTotalTokens(){
        $transactions = TokenTransaction::pipe();
        if(get_parent_class($transactions) === 'Illuminate\Pagination\AbstractPaginator'){
            $transactions = $transactions->getCollection();
        }

        return response()->json(new JsonResponse([
            'total' => $transactions->sum('token_amount')
        ]));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = TokenTransaction::pipe();
        if(get_parent_class($transactions) === 'Illuminate\Pagination\AbstractPaginator'){
            $transactions = $transactions->getCollection();
        }

        return response()->json(new JsonResponse([
            'items' => $transactions,
            'total' => TokenTransaction::pipeCount()
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TokenTransaction  $tokenTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(TokenTransaction $tokenTransaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TokenTransaction  $tokenTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(TokenTransaction $tokenTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TokenTransaction  $tokenTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TokenTransaction $tokenTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TokenTransaction  $tokenTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(TokenTransaction $tokenTransaction)
    {
        //
    }
}
