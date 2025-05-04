<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conta;
use Inertia\Inertia;

class ContaController extends Controller
{
    public function create()
    {
        return Inertia::render('Contas/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:dinheiro,conta_bancaria,cartao_credito',
            'saldo_inicial' => 'required|numeric',
        ]);

        Conta::create($request->all());
    }
}
