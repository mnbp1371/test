<?php

namespace App\Console\Commands;

use App\Helpers\ConsoleCommandHelper;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PaymentTest extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'web_service:test {url} {api_key} {callback}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    public function handle(ConsoleCommandHelper $commandHelper)
    {
        foreach ($this->data() as $datum) {

            $request = Request::create(route('curl'), 'POST', $datum['request']);

            $response = app()->handle($request);


            $response = !empty($response->getContent()) ? $response->toArray() : ['چواب سرویس خای است'];

            $diffs = $this->whatCompareIsEqualArray($response, $datum['response']);

            if (!empty($diffs)) {
                $prepareData = $datum['request']['data'];
                \App\Helpers\Helper::convertToString($prepareData);

                $commandHelper->setInfoLog('compare');
                $commandHelper->setInfoLog('request' , $prepareData);
                $commandHelper->setInfoLog('headers' , [
                    'api-key' => $datum['request']['api_key'] ?? '',
                    'url' => $datum['request']['url'] ?? '',
                ]);
                $commandHelper->setInfoLog('services response' , $response);
                $commandHelper->setInfoLog('expected response' , $datum['response']);
                $commandHelper->setInfoLog('diffs' , $diffs);
                $commandHelper->customLine();
            }
        }

        return 0;
    }

    private function whatCompareIsEqualArray(array $array1, array $array2)
    {
        $array1DotItems = Arr::dot($array1);
        $array2DotItems = Arr::dot($array2);

        $diffs = [];
        foreach ($array1DotItems as $array1DotItemKey => $array1DotItemValue) {
            if (empty($array2DotItems[$array1DotItemKey])
                || !Str::is($array2DotItems[$array1DotItemKey], $array1DotItemValue)
                || gettype($array2DotItems[$array1DotItemKey]) !== gettype($array1DotItemValue)
            ) {
                $diffs[$array1DotItemKey] = $array1DotItemValue;
            }
        }

        return $diffs;
    }

    public function data(): array
    {
        return [
            [
                'request' => [
                    "data" => [
                        "order_id" => 1,
                        "amount" => "10000",
                        "name" => "Ali Jazayeri",
                        "phone" => "091223125684",
                        "mail" => "my@site.com",
                        "desc" => "توضیحات پرداخت کننده",
                        "callback" => $this->argument('callback')
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment'
                ],
                'response' => [
                    "id" => "*",
                    "link" => "*"

                ],
            ],
            [
                'request' => [
                    "data" => [
                        "order_id" => 1,
                        "amount" => "10000",
                        "name" => "Ali Jazayeri",
                        "phone" => "091223125684",
                        "mail" => "my@site.com",
                        "desc" => "توضیحات پرداخت کننده",
                        "callback" => "کال بک اشتباه"
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment'
                ],
                'response' => [
                    'error_code' => 38,
                    "error_message" => "درخواست شما از آدرس :// ارسال شده است. دامنه آدرس بازگشت `callback` با آدرس ثبت شده در وب سرویس همخوانی ندارد."
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "order_id" => 1,
                        "amount" => "10000",
                        "name" => "Ali Jazayeri",
                        "phone" => "091223125684",
                        "mail" => "my@site.com",
                        "desc" => "توضیحات پرداخت کننده",
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment'
                ],
                'response' => [
                    "error_code" => 37,
                    "error_message" => "آدرس بازگشت `callback` نباید خالی باشد و باید کمتر از 2048 کاراکتر باشد."

                ],
            ],
            [
                'request' => [
                    "data" => [
                        "order_id" => 1,
                        "amount" => "10000",
                        "name" => "Ali Jazayeri",
                        "phone" => "091223125684",
                        "mail" => "my@site.com",
                        "desc" => "توضیحات پرداخت کننده",
                        "callback" => $this->argument('callback')
                    ],
                    "api_key" => "invalid api_key",
                    "url" => $this->argument('url') . '/payment'
                ],
                'response' => [
                    "error_code" => '12',
                    "error_message" => "API Key یافت نشد."
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "order_id" => 1,
                        "name" => "Ali Jazayeri",
                        "phone" => "091223125684",
                        "mail" => "my@site.com",
                        "desc" => "توضیحات پرداخت کننده",
                        "callback" => $this->argument('callback')
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment'
                ],
                'response' => [
                    "error_code" => 33,
                    "error_message" => "مبلغ `amount` نباید خالی باشد."
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "order_id" => 1,
                        "amount" => "1",
                        "name" => "Ali Jazayeri",
                        "phone" => "091223125684",
                        "mail" => "my@site.com",
                        "desc" => "توضیحات پرداخت کننده",
                        "callback" => $this->argument('callback')
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment'
                ],
                'response' => [
                    "error_code" => 34,
                    "error_message" => "مبلغ `amount` باید بیشتر از 1,000 ریال باشد."
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "order_id" => 1,
                        "amount" => "1000000000000000000000",
                        "name" => "Ali Jazayeri",
                        "phone" => "091223125684",
                        "mail" => "my@site.com",
                        "desc" => "توضیحات پرداخت کننده",
                        "callback" => $this->argument('callback')
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment'
                ],
                'response' => [
                    "error_code" => 35,
                    "error_message" => "مبلغ `amount` باید کمتر از * ریال باشد."
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "order_id" => 1,
                        "amount" => 0,
                        "name" => "Ali Jazayeri",
                        "phone" => "091223125684",
                        "mail" => "my@site.com",
                        "desc" => "توضیحات پرداخت کننده",
                        "callback" => $this->argument('callback')
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment'
                ],
                'response' => [
                    "error_code" => 33,
                    "error_message" => "مبلغ `amount` نباید خالی باشد."
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "order_id" => 1,
                        "amount" => 0,
                        "name" => "Ali Jazayeri",
                        "phone" => "091223125684",
                        "mail" => "my@site.com",
                        "desc" => "توضیحات پرداخت کننده",
                        "callback" => $this->argument('callback')
                    ],
                    "url" => $this->argument('url') . '/payment'
                ],
                'response' => [
                    "error_code" => '12',
                    "error_message" => "API Key یافت نشد."
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "amount" => "10000",
                        "name" => "Ali Jazayeri",
                        "phone" => "091223125684",
                        "mail" => "my@site.com",
                        "desc" => "توضیحات پرداخت کننده",
                        "callback" => $this->argument('callback')
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment'
                ],
                'response' => [
                    "error_code" => 32,
                    "error_message" => "شماره سفارش `order_id` نباید خالی باشد."
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "amount" => "10000",
                        "order_id" => 0,
                        "name" => "Ali Jazayeri",
                        "phone" => "091223125684",
                        "mail" => "my@site.com",
                        "desc" => "توضیحات پرداخت کننده",
                        "callback" => $this->argument('callback')
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment'
                ],
                'response' => [
                    "error_code" => 32,
                    "error_message" => "شماره سفارش `order_id` نباید خالی باشد."
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "amount" => "10000",
                        "order_id" => 0,
                        "name" => "Ali Jazayeri",
                        "phone" => "091223125684",
                        "mail" => "my@site.com",
                        "desc" => "توضیحات پرداخت کننده",
                        "callback" => $this->argument('callback')
                    ],
                    "api_key" => "invalid web service",
                    "url" => $this->argument('url') . '/payment'
                ],
                'response' => [
                    "error_code" => '12',
                    "error_message" => "API Key یافت نشد."
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "id" => "نامعتبر",
                        "order_id" => "1"
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment/inquiry'
                ],
                'response' => [
                    "error_code" => 52,
                    "error_message" => "استعلام نتیجه ای نداشت."
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "id" => "li9tcuchz6nkp97br7vqjjghhupnvuyc",
                        "order_id" => "نامعتبر"
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment/inquiry'
                ],
                'response' => [
                    "error_code" => 52,
                    "error_message" => "استعلام نتیجه ای نداشت."
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "id" => "li9tcuchz6nkp97br7vqjjghhupnvuyc",
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment/inquiry'
                ],
                'response' => [
                    "Missing required argument order_id"
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "order_id" => "1"
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment/inquiry'
                ],
                'response' => [
                    "Missing required argument id"
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "id" => "",
                        "order_id" => "1"
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment/inquiry'
                ],
                'response' => [
                    "error_code" => 31,
                    "error_message" => "کد تراکنش `id` نباید خالی باشد."
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "id" => "li9tcuchz6nkp97br7vqjjghhupnvuyc",
                        "order_id" => ""
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment/inquiry'
                ],
                'response' => [
                    "error_code" => 32,
                    "error_message" => "شماره سفارش `order_id` نباید خالی باشد."
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "id" => "invalid",
                        "order_id" => "1"
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment/verify'
                ],
                'response' => [
                    "error_code" => 53,
                    "error_message" => "تایید پرداخت امکان پذیر نیست."
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "id" => "yzd4ynht61tdgumxfwgg2ym5rzcqqth0",
                        "order_id" => "invalid"
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment/verify'
                ],
                'response' => [
                    "error_code" => 53,
                    "error_message" => "تایید پرداخت امکان پذیر نیست."
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "order_id" => "invalid"
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment/verify'
                ],
                'response' => [
                    "Missing required argument id"
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "order_id" => "1"
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment/verify'
                ],
                'response' => [
                    "Missing required argument id"
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "id" => "",
                        "order_id" => "1"
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment/verify'
                ],
                'response' => [
                    "error_code" => 31,
                    "error_message" => "کد تراکنش `id` نباید خالی باشد."
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "id" => "yzd4ynht61tdgumxfwgg2ym5rzcqqth0",
                        "order_id" => ""
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment/verify'
                ],
                'response' => [
                    "error_code" => 32,
                    "error_message" => "شماره سفارش `order_id` نباید خالی باشد."
                ],
            ],
        ];
    }
}
