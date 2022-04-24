<?php

namespace App\Console\Commands;

use App\Helpers\ConsoleCommandHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuccessTest extends Test
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'web_service:success {url} {api_key} {callback} {id} {order_id}';

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
            $this->validateProviderResponse($datum['rules'], $response);
            $commandHelper->customLine();
        }
    }

    protected function validateProviderResponse(array $rules, $data)
    {
        /*** @var ConsoleCommandHelper $commandHelper */
        $commandHelper = app(ConsoleCommandHelper::class);

        if (!is_array($data) || Validator::make($data, $rules)->fails()) {
            $messages = Validator::make($data, $rules);

            foreach ($messages->errors()->toArray() as $message) {
                $commandHelper->setInfoLog($message[0]);
            }
        }
    }

    public function data(): array
    {
        return [
            [
                'request' => [
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment/transactions'
                ],
                'rules' => [
                    "attachment.timestamp" => 'required|int',
                    "attachment.total_count" => 'required|int',
                    "attachment.page_count" => 'required|int',
                    "attachment.current_page" => 'required|int|in:0',
                    "attachment.total_amount" => 'required|int',
                    "attachment.page_amount" => 'required|int',
                    "records.*.status" => 'required|int',
                    "records.*.track_id" => "required|string",
                    "records.*.id" => "required|string",
                    "records.*.order_id" => "required|string",
                    "records.*.amount" => "required|string",
                    "records.*.wage.by" => 'required|string',
                    "records.*.wage.type" => 'required|string',
                    "records.*.wage.amount" => 'required|string',
                    "records.*.date" => "required|string",
                    "records.*.payer.name" => "present|string",
                    "records.*.payer.mail" => "present|string",
                    "records.*.payer.desc" => "present|string",
                    "records.*.verify.date" => 'present|string',
                    "records.*.settlement.amount" => "required|string",
                    "records.*.settlement.account.id" => "required|string",
                    "records.*.settlement.account.iban" => "required|string",
                    "records.*.settlement.account.name" => "present|string",
                    "records.*.settlement.account.number" => "present|string",
                    "records.*.settlement.account.bank.id" => "present|string",
                    "records.*.settlement.account.bank.title" => "present|string"

                ],
            ],
            [
                'request' => [
                    "data" => [
                        "order_id" => $this->argument('order_id'),
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
                'rules' => [
                    "id" => "required|string",
                    "link" => "required|string"
                ],
            ],
            [
                'request' => [
                    "data" => [
                        "id" => $this->argument('id'),
                        "order_id" => $this->argument('order_id'),
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment/verify'
                ],
                'rules' => [
                    "status" => 'required|integer|in:100,101',
                    "track_id" => "required|string",
                    "id" => 'required|string|in:' . $this->argument('id'),
                    "order_id" => 'required|string|in:' . $this->argument('order_id'),
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
                'request' => [
                    "data" => [
                        "id" => $this->argument('id'),
                        "order_id" => $this->argument('order_id'),
                    ],
                    "api_key" => $this->argument('api_key'),
                    "url" => $this->argument('url') . '/payment/inquiry'
                ],
                'rules' => [
                    "status" => 'required|integer|in:100,101',
                    "track_id" => "required|string",
                    "id" => 'required|string|in:' . $this->argument('id'),
                    "order_id" => 'required|string|in:' . $this->argument('order_id'),
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
