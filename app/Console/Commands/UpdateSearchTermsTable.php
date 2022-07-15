<?php

namespace App\Console\Commands;

use App\Helpers\UpdateSearchTerms;
use Illuminate\Console\Command;

class UpdateSearchTermsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateSearchTermsTable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert or update search terms with partner details';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        (new UpdateSearchTerms())->updateSearchTerms();
    }
}
