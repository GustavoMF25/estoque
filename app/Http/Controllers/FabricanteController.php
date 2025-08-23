<?php

namespace App\Http\Controllers;

use App\Models\Fabricante;
use Illuminate\Http\Request;

class FabricanteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fabricantes = Fabricante::orderBy('nome')->paginate(10);
        return view('fabricantes.index', compact('fabricantes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('fabricantes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:150',
            'site' => 'nullable|string|max:255',
        ]);

        Fabricante::create($request->only(['nome', 'site']));

        return redirect()->route('fabricantes.index')
            ->with('success', 'Fabricante criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
    public function destroy(Fabricante $fabricante)
    {
        $fabricante->delete();

        return redirect()->route('fabricantes.index')->with('success', 'Fabricante excluído com sucesso!');
    }
}
