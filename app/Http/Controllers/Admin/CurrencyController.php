<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;

class CurrencyController extends Controller
{
    public function index()
    {
        $pageTitle = 'Manage Currencies';
        $currencies = Currency::ordered()->paginate(gs('paginate_number'));
        
        return view('admin.currency.index', compact('pageTitle', 'currencies'));
    }

    public function create()
    {
        $pageTitle = 'Add New Currency';
        return view('admin.currency.create', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'code' => 'required|string|max:10|unique:currencies,code',
            'symbol' => 'required|string|max:10',
            'rate' => 'required|numeric|min:0.00000001',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $currency = Currency::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'symbol' => $request->symbol,
            'rate' => $request->rate,
            'status' => $request->has('status') ? 1 : 0,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        if ($request->has('is_default')) {
            $currency->setAsDefault();
            $this->updateGeneralSettings($currency);
        }

        $notify[] = ['success', 'Currency added successfully'];
        return to_route('admin.currency.index')->withNotify($notify);
    }

    public function edit(Currency $currency)
    {
        $pageTitle = 'Edit Currency';
        return view('admin.currency.edit', compact('pageTitle', 'currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'code' => ['required', 'string', 'max:10', Rule::unique('currencies', 'code')->ignore($currency->id)],
            'symbol' => 'required|string|max:10',
            'rate' => 'required|numeric|min:0.00000001',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $currency->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'symbol' => $request->symbol,
            'rate' => $request->rate,
            'status' => $request->has('status') ? 1 : 0,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        if ($request->has('is_default')) {
            $currency->setAsDefault();
            $this->updateGeneralSettings($currency);
        }

        $notify[] = ['success', 'Currency updated successfully'];
        return to_route('admin.currency.index')->withNotify($notify);
    }

    public function destroy(Currency $currency)
    {
        if ($currency->is_default) {
            $notify[] = ['error', 'Default currency cannot be deleted'];
            return back()->withNotify($notify);
        }

        $currency->delete();
        
        $notify[] = ['success', 'Currency deleted successfully'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        $currency = Currency::findOrFail($id);
        
        if ($currency->is_default && $currency->status) {
            $notify[] = ['error', 'Default currency cannot be disabled'];
            return back()->withNotify($notify);
        }
        
        $currency->status = !$currency->status;
        $currency->save();
        
        $message = $currency->status ? 'enabled' : 'disabled';
        $notify[] = ['success', 'Currency ' . $message . ' successfully'];
        return back()->withNotify($notify);
    }

    public function setDefault($id)
    {
        $currency = Currency::findOrFail($id);
        
        if (!$currency->status) {
            $notify[] = ['error', 'Inactive currency cannot be set as default'];
            return back()->withNotify($notify);
        }
        
        $currency->setAsDefault();
        $this->updateGeneralSettings($currency);
        
        $notify[] = ['success', 'Default currency updated successfully'];
        return back()->withNotify($notify);
    }

    public function updateRates()
    {
        $pageTitle = 'Update Exchange Rates';
        $currencies = Currency::active()->where('is_default', false)->get();
        
        return view('admin.currency.update_rates', compact('pageTitle', 'currencies'));
    }

    public function storeRates(Request $request)
    {
        $request->validate([
            'rates' => 'required|array',
            'rates.*' => 'required|numeric|min:0.00000001',
        ]);

        foreach ($request->rates as $id => $rate) {
            Currency::findOrFail($id)->update(['rate' => $rate]);
        }

        $notify[] = ['success', 'Exchange rates updated successfully'];
        return back()->withNotify($notify);
    }

    /**
     * Update general settings when default currency changes
     */
    private function updateGeneralSettings(Currency $currency)
    {
        $general = gs();
        $general->cur_text = $currency->code;
        $general->cur_sym = $currency->symbol;
        $general->save();
        
        // Clear cache
        Cache::forget('GeneralSetting');
    }
}