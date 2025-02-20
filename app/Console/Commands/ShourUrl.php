<?php

namespace App\Console\Commands;

use App\Services\ShortUrlService;
use Illuminate\Console\Command;

class ShourUrl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:shour-url';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(
        protected ShortUrlService $shortUrlService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $response = $this->shortUrlService->login();
        $data = $this->shortUrlService->getTotalPage();
        dd($data->shortUrls->toArray());
    }
}
