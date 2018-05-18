<?php


namespace Cblink\BearychatException;


use Carbon\Carbon;
use ElfSundae\BearyChat\Laravel\BearyChat;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class BearychatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * @var
     */
    private $message;
    /**
     * @var
     */
    private $code;
    /**
     * @var
     */
    private $file;
    /**
     * @var
     */
    private $line;
    /**
     * @var
     */
    private $url;
    /**
     * @var
     */
    private $trace;
    /**
     * @var
     */
    private $exception;

    /**
     * Create a new job instance.
     *
     * @param $url
     * @param $exception
     * @param $message
     * @param $code
     * @param $file
     * @param $line
     * @param $trace
     */
    public function __construct($url, $exception, $message, $code, $file, $line, $trace)
    {
        $this->message = $message;
        $this->code = $code;
        $this->file = $file;
        $this->line = $line;
        $this->url = $url;
        $this->trace = $trace;
        $this->exception = $exception;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = [
            'Time:' . Carbon::now()->toDateTimeString(),
            'Environment:' . config('app.env'),
            'Project Name:' . config('app.name'),
            'Url:' . $this->url,
            'Exception:' . " $this->exception(code:$this->code): $this->message at $this->file:$this->line",
            'Exception Trace:' . $this->trace,
        ];

        try {
            BearyChat::send(implode(PHP_EOL, $message));
        } catch (\Exception $exception) {
            logger($exception->getMessage());
        }
    }
}
