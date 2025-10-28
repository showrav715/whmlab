<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConfigurableGroup;
use App\Models\ConfigurableGroupOption;
use App\Models\ConfigurableGroupSubOption;
use App\Models\Pricing;
use App\Models\Product;

class ConfigurableController extends Controller {

    public function groups() {
        $pageTitle = 'Configurable Groups';
        $products = Product::with('serviceCategory')->get(['id', 'name', 'category_id']);
        $groups = ConfigurableGroup::with('options', 'getProducts')->paginate(getPaginate());
        return view('admin.configurable.group', compact('pageTitle', 'groups', 'products'));
    }

    public function addGroup(Request $request) {

        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required|max:65000',
            'assigned_product' => 'sometimes|array',
            'assigned_product.*' => 'sometimes|exists:products,id',
        ]);

        $group = new ConfigurableGroup;
        $group->name = $request->name;
        $group->description = $request->description;
        $group->save();

        $group->products()->attach($request->assigned_product);

        $notify[] = ['success', 'Configuration group added successfully'];
        return back()->withNotify($notify);
    }

    public function updateGroup(Request $request) {

        $request->validate([
            'name' => 'required|max:255|',
            'description' => 'required|max:65000',
            'assigned_product' => 'sometimes|array',
            'assigned_product.*' => 'sometimes|exists:products,id',
        ]);

        $group = ConfigurableGroup::findOrFail($request->id);
        $group->name = $request->name;
        $group->description = $request->description;
        $group->save();

        $group->products()->sync($request->assigned_product);

        $notify[] = ['success', 'Configuration group updated successfully'];
        return back()->withNotify($notify);
    }

    public function options($id) {

        $group = ConfigurableGroup::with('options')->findOrFail($id);
        $options = $group->options()->orderBy('order')->with('subOptions')->paginate(getPaginate());
        $pageTitle = 'Configurable Options for ' . $group->name;

        return view('admin.configurable.group_option', compact('pageTitle', 'options', 'group'));
    }

    public function addOption(Request $request) {

        $request->validate([
            'name' => 'required|max:255',
            'group_id' => 'required|integer',
            'option_type' => 'required|in:1',
            'order' => 'required|integer|gte:0',
        ]);

        $group = ConfigurableGroup::findOrFail($request->group_id);

        $option = new ConfigurableGroupOption();
        $option->configurable_group_id = $group->id;
        $option->name = $request->name;
        $option->option_type = $request->option_type;
        $option->order = $request->order;
        $option->save();

        $notify[] = ['success', 'Configuration option added successfully'];
        return back()->withNotify($notify);
    }

    public function updateOption(Request $request) {

        $request->validate([
            'name' => 'required|max:255',
            'group_id' => 'required|integer',
            'id' => 'required|integer',
            'option_type' => 'required|in:1',
            'order' => 'required|integer|gte:0',
        ]);

        $option = ConfigurableGroupOption::where('id', $request->id)->where('configurable_group_id', $request->group_id)->firstOrFail();
        $option->name = $request->name;
        $option->option_type = $request->option_type;
        $option->order = $request->order;
        $option->save();

        $notify[] = ['success', 'Configuration option updated successfully'];
        return back()->withNotify($notify);
    }

    public function subOptions($groupId, $optionId) {

        $option = ConfigurableGroupOption::where('id', $optionId)->where('configurable_group_id', $groupId)->firstOrFail();
        $group = $option->group;
        $subOptions = $option->subOptions()->orderBy('order')->with('group', 'price')->paginate(getPaginate());
        $pageTitle = 'Configurable Sub Options for ' . $group->name . ' (' . $option->name . ')';

        return view('admin.configurable.group_sub_option', compact('pageTitle', 'subOptions', 'group', 'option'));
    }

    public function addSubOption(Request $request) {

        $request->validate([
            'name' => 'required|max:255',
            'group_id' => 'required|integer',
            'option_id' => 'required|integer',
            'order' => 'required|integer|gte:0',
            'monthly_setup_fee' => 'required|numeric',
            'monthly' => 'required|numeric',
            'quarterly_setup_fee' => 'required|numeric',
            'quarterly' => 'required|numeric',
            'semi_annually_setup_fee' => 'required|numeric',
            'semi_annually' => 'required|numeric',
            'annually_setup_fee' => 'required|numeric',
            'annually' => 'required|numeric',
            'biennially_setup_fee' => 'required|numeric',
            'biennially' => 'required|numeric',
            'triennially_setup_fee' => 'required|numeric',
            'triennially' => 'required|numeric'
        ]);

        $option = ConfigurableGroupOption::where('id', $request->option_id)->where('configurable_group_id', $request->group_id)->firstOrFail();

        $subOption = new ConfigurableGroupSubOption();
        $subOption->configurable_group_id = $option->configurable_group_id;
        $subOption->configurable_group_option_id = $option->id;

        $subOption->name = $request->name;
        $subOption->order = $request->order;
        $subOption->save();

        $pricing = new Pricing();

        $pricing->type = 'sub_config_options';
        $pricing->configurable_group_sub_option_id = $subOption->id;

        $pricing->monthly_setup_fee = $request->monthly_setup_fee;
        $pricing->monthly = $request->monthly;

        $pricing->quarterly_setup_fee = $request->quarterly_setup_fee;
        $pricing->quarterly = $request->quarterly;

        $pricing->semi_annually_setup_fee = $request->semi_annually_setup_fee;
        $pricing->semi_annually = $request->semi_annually;

        $pricing->annually_setup_fee = $request->annually_setup_fee;
        $pricing->annually = $request->annually;

        $pricing->biennially_setup_fee = $request->biennially_setup_fee;
        $pricing->biennially = $request->biennially;

        $pricing->triennially_setup_fee = $request->triennially_setup_fee;
        $pricing->triennially = $request->triennially;

        $pricing->save();

        $notify[] = ['success', 'Configuration sub option added successfully'];
        return back()->withNotify($notify);
    }

    public function updateSubOption(Request $request) {

        $request->validate([
            'name' => 'required|max:255',
            'order' => 'required|integer|gte:0',
            'monthly_setup_fee' => 'required|numeric',
            'monthly' => 'required|numeric',
            'quarterly_setup_fee' => 'required|numeric',
            'quarterly' => 'required|numeric',
            'semi_annually_setup_fee' => 'required|numeric',
            'semi_annually' => 'required|numeric',
            'annually_setup_fee' => 'required|numeric',
            'annually' => 'required|numeric',
            'biennially_setup_fee' => 'required|numeric',
            'biennially' => 'required|numeric',
            'triennially_setup_fee' => 'required|numeric',
            'triennially' => 'required|numeric'
        ]);

        $subOption = ConfigurableGroupSubOption::findOrFail($request->id);
        $subOption->name = $request->name;
        $subOption->order = $request->order;
        $subOption->save();

        $pricing = $subOption->price;

        $pricing->monthly_setup_fee = $request->monthly_setup_fee;
        $pricing->monthly = $request->monthly;

        $pricing->quarterly_setup_fee = $request->quarterly_setup_fee;
        $pricing->quarterly = $request->quarterly;

        $pricing->semi_annually_setup_fee = $request->semi_annually_setup_fee;
        $pricing->semi_annually = $request->semi_annually;

        $pricing->annually_setup_fee = $request->annually_setup_fee;
        $pricing->annually = $request->annually;

        $pricing->biennially_setup_fee = $request->biennially_setup_fee;
        $pricing->biennially = $request->biennially;

        $pricing->triennially_setup_fee = $request->triennially_setup_fee;
        $pricing->triennially = $request->triennially;

        $pricing->save();

        $notify[] = ['success', 'Configuration sub option updated successfully'];
        return back()->withNotify($notify);
    }

    public function groupStatus($id) {
        return ConfigurableGroup::changeStatus($id);
    }

    public function optionStatus($id) {
        return ConfigurableGroupOption::changeStatus($id);
    }

    public function subOptionStatus($id) {
        return ConfigurableGroupSubOption::changeStatus($id);
    }
}
