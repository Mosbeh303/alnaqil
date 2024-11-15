<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rules;
use App\Models\User;
use App\Models\Setting;
use App\Models\City;
use App\Models\District;
use Illuminate\Support\Facades\Redirect;


class SettingController extends Controller
{

    public function index(){
        $this->authorize('isEmployeeHasPermission', 'settings');
        return Inertia::render('Settings/General', [
            'vat' => Setting::where('option' , 'vat')->exists() ? json_decode(Setting::where('option' , 'vat')->first()->value): null,
        ]);
    }




    public function cities(){
        $this->authorize('isEmployeeHasPermission', 'settings');
        return Inertia::render('Settings/Cities', [
            'registerCities' => City::where('type' , 'register')->get() ?? null,
            'shippingDistricts'=>  District::with('cities')->get(),
            'shippingNeighborhood'=> Setting::where('option' , 'shipping_neighborhood')->exists() ? json_decode(Setting::where('option' , 'shipping_neighborhood')->first()->value): null,
            // 'operatorCities'=> Setting::where('option' , 'register_cities')->exists() ? json_decode(Setting::where('option' , 'register_cities')->first()->value): null,
            // 'employeeCities'=> Setting::where('option' , 'employee_cities')->exists() ? json_decode(Setting::where('option' , 'employee_cities')->first()->value): null
        ]);
    }

    public function storeArray(Request $request, $option){
        $this->authorize('isEmployeeHasPermission', 'settings');
        $request->validate([
            'value' => 'required',
        ],
        [
            'value.required' => trans('Field_value_is_required')
        ]);

        $setting = Setting::where('option', $option)->first();
        $array = [];

        if ($setting){
            $array = json_decode($setting->value);
            $exist = array_search($request->value, $array);

            if($exist !== false)
                return redirect::back()->with('error', trans('option_exist'));

            array_push($array, $request->value);
            $setting->update([
                'value'  => json_encode($array),
            ]);


        }else {
            array_push($array, $request->value);
            Setting::create([
                'option' => $option,
                'value'  => json_encode($array),
            ]);
        }

        return redirect::back()->with('success', trans('Added_successfully'));
    }

    public function removeFromArray(Request $request, $option, $value){
        $this->authorize('isEmployeeHasPermission', 'settings');
        $setting = Setting::where('option', $option)->first();
        $array = json_decode($setting->value);

        if (in_array($value, $array)) {
            array_splice($array, array_search($value,$array), 1);
        }

        $setting->update([
            'value'  => json_encode($array),
        ]);

        return redirect::back()->with('success', trans('deleted_successfully'));
    }

    public function messages(){
        $this->authorize('isEmployeeHasPermission', 'settings');
        return Inertia::render('Settings/Messages', [
            'whatsappMessage' => Setting::where('option' , 'whatsapp_message')->exists() ? Setting::where('option' , 'whatsapp_message')->first()->value : null,
            'receiving_shipment_message' => Setting::where('option' , 'receiving_shipment_message')->exists() ? Setting::where('option' , 'receiving_shipment_message')->first()->value : null,
            'creating_shipment_message' => Setting::where('option' , 'creating_shipment_message')->exists() ? Setting::where('option' , 'creating_shipment_message')->first()->value : null,
            'whatsappStoreMessage' => Setting::where('option' , 'whatsapp_store_message')->exists() ? Setting::where('option' , 'whatsapp_store_message')->first()->value : null,
        ]);
    }

    public function store(Request $request, $option){
        $this->authorize('isEmployeeHasPermission', 'settings');
        $request->validate([
            'value' => 'required',
        ],
        [
            'value.required' => trans('Field_value_is_required')
        ]);

        $setting = Setting::where('option', $option)->first();

        if ($setting){
            $setting->update([
                'value'  => $request->value,
            ]);
        }else {
            Setting::create([
                'option' => $option,
                'value'  => $request->value,
            ]);
        }

        return redirect::back()->with('success', trans('Modifications_saved_successfully'));
    }

    public function notices(){
        $this->authorize('isEmployeeHasPermission', 'settings');
        return Inertia::render('Settings/Notices', [
            'notices' => Setting::where('option' , 'failed_notices')->exists() ? json_decode(Setting::where('option' , 'failed_notices')->first()->value): null,

        ]);
    }



}
