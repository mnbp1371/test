<?php

namespace App\Helpers;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Console\Input\StringInput;
use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Output\ConsoleOutput;

class ConsoleCommandHelper extends Command
{
    public $name = 'NONEXISTENT';
    public $hidden = true;

    public ConsoleOutput $outputSymfony;
    public OutputStyle $outputStyle;

    public string $logChanel = 'null';
    protected array $args = [];

    public function __construct(string $argInput = '')
    {
        parent::__construct();

        $this->input = new StringInput($argInput);
        $this->outputSymfony = new ConsoleOutput();
        $this->outputStyle = new OutputStyle($this->input, $this->outputSymfony);
        $this->output = $this->outputStyle;
    }

    public function setLogChannel($logChanel): ConsoleCommandHelper
    {
        $this->logChanel = $logChanel;

        return $this;
    }

    public function setErrorLog($message, $options = null)
    {
        $this->setLog($message, 'emergency', 'error', $options);
    }

    public function setLog($message, $level, $commandLevel, $options = null)
    {
        if ($options) {
            $options = collect($options)->toArray();
            $message .= ': ';

            foreach ($options as $key => $val) {
                if (!is_array($val)) {
                    $type = gettype($val);
                    $message .= " {$key} => (${type}) ${val} |";
                }
            }

            $message = substr_replace($message, '', -1);
        }
        $this->{$commandLevel}(Carbon::now() . ' ' . $message);
        Log::channel($this->logChanel)->{$level}($message);
    }

    public function validateInput(array $data, array $rules): bool
    {
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            $this->info('See error messages below:');

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return true;
        }

        return false;
    }

    public function startLog($className = null)
    {
        $className = $className ?? class_basename($this);
        $this->setInfoLog('------------------------------ ' . Str::kebab($className) . ' Execution Started ------------------------------');
    }

    public function endLog($className = null)
    {
        $className = $className ?? class_basename($this);
        $this->setInfoLog('------------------------------ ' . Str::kebab($className) . ' Execution Ended ------------------------------');
    }

    public function setInfoLog($message, $options = null)
    {
        $this->setLog($message, 'info', 'info', $options);
    }

    /**
     * @throws Exception
     */
    public function validateProviderResponse(array $rules, $data)
    {
        if (!is_array($data) || Validator::make($data, $rules)->fails()) {
            throw new Exception('invalid bank service api response');
        }
    }

    public function parseArgs(string $arg): string
    {
        return strtoupper(trim($arg));
    }

    public function setArgs(array $args): static
    {
        $this->args = $args;
        return $this;
    }

    #[Pure]
    public function getOptions(): array
    {
        $args = [];
        foreach ($this->args as $key => $value) {
            $args[$key] = is_string($value) ? $this->parseArgs($value) : $value;
        }

        return $args;
    }

    #[Pure]
    public function getOption(string $name = null): ?string
    {
        return !is_null($name) && !empty($this->args[$name]) ? $this->parseArgs($this->args[$name]) : null;
    }

    public function printArray(array $data){
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }

    public function customLine($className = null)
    {
        $this->comment('------------------------------------------------------------------' . Str::kebab($className) . '------------------------------------------------------------------');
    }
}
