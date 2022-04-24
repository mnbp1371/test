<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CallBackController extends Controller
{
    public function __invoke(Request $request)
    {
        echo $request->method();
        echo "<hr>";
        echo "check callback data";
        echo "<br>";
        $validate = $this->validateCustom($request->toArray(), $this->returnMethodData()[$request->method()]);
        echo "<hr>";

        if ($validate && $request->get('status') !== 10) {
            return 0;
        }

        foreach ($this->data() as $datum) {
            echo $datum['title'];
            echo "br";
            $request = Request::create(route('curl'), 'POST', $datum['request']);
            $response = app()->handle($request);
            $response = !empty($response->getContent()) ? $response->toArray() : ['چواب سرویس خای است'];
            $this->validateCustom($datum['rules'], $response);
            dd($response);
            echo "<hr>";
        }
    }

    public function returnMethodData()
    {
        return [
            'GET' => [
                "id" => "string|required",
                "order_id" => "string|required",
                "status" => "string|required",
                "track_id" => "string|required",
            ],
            'POST' => [
                "id" => "string|required",
                "order_id" => "string|required",
                "status" => "string|required",
                "track_id" => "string|required",
            ],
        ];
    }

    private function validateCustom($data, $rules): bool
    {
        if (!is_array($data) || Validator::make($data, $rules)->fails()) {
            $messages = Validator::make($data, $rules);

            foreach ($messages->errors()->toArray() as $message) {
                echo "<br>";
                echo $message[0];
            }
        }

        return Validator::make($data, $rules)->fails();
    }

    public function data(): array
    {
        return [
            [
                'title' => 'process verify',
                'request' => [
                    "data" => [
                        "id" => \request('id'),
                        "order_id" => \request('order_id'),
                    ],
                    "api_key" => 'b54c6492-d3cd-473b-9ce9-cbf164cf8ad8',
                    "url" => 'http://api.idpay.lab/v1.1' . '/payment/verify'
                ],
                'rules' => [
                    "status" => 'required|integer|in:100,101',
                    "track_id" => "required|string",
                    "id" => 'required|string|in:' . \request('id'),
                    "order_id" => 'required|string|in:' . \request('order_id'),
                    "amount" => "required|string",
                    "date" => "required|string",
                    "payment.track_id" => "required|string",
                    "payment.amount" => "required|string",
                    "payment.card_no" => "required|string",
                    "payment.hashed_card_no" => "required|string",
                    "payment.date" => 'required|integer',
                    "verify.date" => "required|integer",
                    "settlement.amount" => "required|integer",
                    "settlement.account.id" => "required|integer",
                    "settlement.account.iban" => "required|integer",
                    "settlement.account.name" => "required|integer",
                    "settlement.account.number" => "required|integer",
                    "settlement.account.bank.id" => "required|integer",
                    "settlement.account.bank.title" => "required|integer"
                ],
            ],
            [
                'title' => 'process inquiry',
                'request' => [
                    "data" => [
                        "id" => \request('id'),
                        "order_id" => \request('order_id'),
                    ],
                    "api_key" => 'b54c6492-d3cd-473b-9ce9-cbf164cf8ad8',
                    "url" => 'http://api.idpay.lab/v1.1' . '/payment/inquiry'
                ],
                'rules' => [
                    "status" => 'required|integer|in:100,101',
                    "track_id" => "required|string",
                    "id" => 'required|string|in:' . \request('id'),
                    "order_id" => 'required|string|in:' . \request('order_id'),
                    "amount" => "required|string",
                    "wage.by" => "required|string",
                    "wage.type" => "required|string",
                    "wage.amount" => "required|string",
                    "date" => "required|string",
                    "payer.name" => "required|string",
                    "payer.mail" => "required|string",
                    "payer.desc" => "required|string",
                    "payment.track_id" => "required|string",
                    "payment.amount" => "required|string",
                    "payment.card_no" => "required|string",
                    "payment.hashed_card_no" => "required|string",
                    "payment.date" => 'required|integer',
                    "verify.date" => 'required|integer',
                    "settlement.amount" => "required|integer",
                    "settlement.account.id" => "required|integer",
                    "settlement.account.iban" => "required|integer",
                    "settlement.account.name" => "required|integer",
                    "settlement.account.number" => "required|integer",
                    "settlement.account.bank.id" => "required|integer",
                    "settlement.account.bank.title" => "required|integer"
                ],
            ],
        ];
    }
}
