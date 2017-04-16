<?php

namespace Zelf\Bow\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bow:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a shot across the bow';

    protected function getOptions()
    {
        return [
            ['with-dummy', null, InputOption::VALUE_NONE, 'Install with dummy data', null],
        ];
    }

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
     * @return mixed
     */
    public function handle()
    {
        $this->info('Publishing the Bow assets, database, and config files');
        $this->call('vendor:publish', ['--tag' => 'bow']);

        $this->info('Dumping the autoloaded files and reloading all new files');
        $process = new Process('composer dump-autoload');
        $process->setWorkingDirectory(base_path())->mustRun();

        $this->info('Migrating the database tables into your application');
        $this->call('migrate');

        $this->info('Seeding data into the database');
        $this->call('db:seed', ['--class' => 'BowDatabaseSeeder']);

        $this->info('Successfully installed the bow! To your ship amiral!');
    }
}
