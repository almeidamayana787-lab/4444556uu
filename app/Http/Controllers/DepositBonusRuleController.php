<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepositBonusRuleController extends Controller
{
    public function index()
    {
        return response()->json(\App\Models\DepositBonusRule::orderBy('min_amount')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|gt:min_amount',
            'bonus_amount' => 'required|numeric|min:0',
        ]);

        $rule = \App\Models\DepositBonusRule::create($request->all());
        return response()->json($rule);
    }

    public function update(Request $request, $id)
    {
        $rule = \App\Models\DepositBonusRule::findOrFail($id);
        $request->validate([
            'name' => 'required|string',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|gt:min_amount',
            'bonus_amount' => 'required|numeric|min:0',
        ]);

        $rule->update($request->all());
        return response()->json($rule);
    }

    public function destroy($id)
    {
        $rule = \App\Models\DepositBonusRule::findOrFail($id);
        $rule->delete();
        return response()->json(['status' => 'success']);
    }
}
