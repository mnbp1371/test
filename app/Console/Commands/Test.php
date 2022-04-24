<?php

namespace App\Console\Commands;

use App\Helpers\ConsoleCommandHelper;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class Test extends Command
{

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
                $commandHelper->setInfoLog('request', $prepareData);
                $commandHelper->setInfoLog('headers', [
                    'api-key' => $datum['request']['api_key'] ?? '',
                    'url' => $datum['request']['url'] ?? '',
                ]);
                $commandHelper->setInfoLog('services response', $response);
                $commandHelper->setInfoLog('expected response', $datum['response']);
                $commandHelper->setErrorLog('diffs', $diffs);
                $commandHelper->customLine();
            }
        }
    }

    public function whatCompareIsEqualArray(array $array1, array $array2)
    {
        $array1DotItems = Arr::dot($array1);
        $array2DotItems = Arr::dot($array2);

        $diffs = [];
        foreach ($array1DotItems as $array1DotItemKey => $array1DotItemValue) {
            if (empty($array2DotItems[$array1DotItemKey])
                || !Str::is($array2DotItems[$array1DotItemKey], $array1DotItemValue)
                || gettype($array2DotItems[$array1DotItemKey]) !== gettype($array1DotItemValue)
            ) {

                if (!empty($array2DotItems[$array1DotItemKey]) && $array2DotItems[$array1DotItemKey] == 'integer,*') {

                    $prepare = $array2DotItems[$array1DotItemKey] . ',';
                    [$type, $pattern] = explode(',', $prepare);

                    if (gettype($array1DotItemValue) == $type and Str::is($pattern, $array1DotItemValue)) {
                        continue;
                    }
                }
                $diffs[$array1DotItemKey] = $array1DotItemValue;
            }
        }

        return $diffs;
    }

    abstract public function data(): array;
}
