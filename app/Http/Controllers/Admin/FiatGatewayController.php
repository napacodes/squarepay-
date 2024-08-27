<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\FiatCurrency;
use App\Models\FiatGateway;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class FiatGatewayController extends Controller
{

    public function index()
    {
        $pageTitle    = 'All Fiat Gateways';
        $fiatGateways = FiatGateway::searchable(['name'])->latest()->paginate(getPaginate());
        $fiatCodes    = FiatCurrency::latest()->get(['id', 'code']);
        return view('admin.fiat.gateways', compact('pageTitle', 'fiatGateways', 'fiatCodes'));
    }

    public function store(Request $request, $id = 0)
    {
        $this->validation($request, $id);

        $fiatGateway = new FiatGateway();
        $notification = 'Fiat gateway added successfully';

        if ($id) {
            $fiatGateway = FiatGateway::findOrFail($id);
            $fiatGateway->status = $request->status ? Status::ENABLE : Status::DISABLE;
            $notification = 'Fiat gateway updated successfully';
        }

        if ($request->hasFile('image')) {
            $fileName = fileUploader($request->image, getFilePath('gateway'), getFileSize('gateway'), @$fiatGateway->image);
            $fiatGateway->image = $fileName;
        }

        $fiatGateway->name = $request->name;
        $fiatGateway->slug = $request->slug;
        $fiatGateway->code = $request->code;
        $fiatGateway->save();

        $notify[] = ['success', $notification];

        return back()->withNotify($notify);
    }

    protected function validation($request, $id)
    {
        $imageValidation = $id ? 'nullable' : 'required';

        $request->validate([
            'image'  => [$imageValidation, 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            'name'   => 'required|max:40',
            'slug'   => 'required|alpha_dash|max:255|unique:fiat_gateways,slug,' . $id,
            'code'   => 'required|array|min:1',
            'code.*' => 'required|exists:fiat_currencies,id'
        ], [
            'slug.alpha_dash' => 'Only alpha numeric value. No space or special character is allowed',
            'code.required'   => 'Supported currencies must not be empty',
        ]);
    }
}
