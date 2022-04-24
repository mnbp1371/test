<?php

namespace App\Console\Commands;

use App\Helpers\ConsoleCommandHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentProcessTest extends Test
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'web_service:payment_process {url} {api_key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle(ConsoleCommandHelper $commandHelper)
    {
        $request = Request::create(route('curl'), 'POST', $this->data()['request']);
        $response = app()->handle($request);

        $responsePrepare = !empty($response->getContent()) ? $response->toArray() : ['چواب سرویس خای است'];
        if (! $response->isOk()) {
            dd($responsePrepare);
        }

        $failed = $this->validateProviderResponse($this->data()['rules'], $responsePrepare);
        $commandHelper->customLine();

        if (!$failed) {
            $commandHelper->setInfoLog('response', $responsePrepare);
        }
    }

    public function data(): array
    {
        return [
            'request' => [
                "data" => [
                    "order_id" => 1,
                    "amount" => "10000",
                    "name" => "Ali Jazayeri",
                    "phone" => "091223125684",
                    "mail" => "my@site.com",
                    "desc" => "توضیحات پرداخت کننده",
                    "callback" => route('callback')
                ],
                "api_key" => $this->argument('api_key'),
                "url" => $this->argument('url') . '/payment'
            ],
            'rules' => [
                "id" => "required|string",
                "link" => "required|string"
            ],
        ];
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
}
