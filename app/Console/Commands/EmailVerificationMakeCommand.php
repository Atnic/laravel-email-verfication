<?php

namespace Atnic\EmailVerification\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Support\Composer;

class EmailVerificationMakeCommand extends Command
{
    use DetectsApplicationNamespace;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:email-verification
                    {--views : Only scaffold the authentication views}
                    {--force : Overwrite existing views by default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold email verification controller, views and routes';

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [
        'auth/verify_email/resend.stub' => 'auth/verify_email/resend.blade.php',
        'mails/auth/email_verification.stub' => 'mails/auth/email_verification.blade.php',
    ];

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    public function __construct(Composer $composer)
    {
        parent::__construct();
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->createDirectories();

        $this->exportViews();

        if (! $this->option('views')) {
            file_put_contents(
                app_path('Http/Controllers/Auth/VerifyEmailController.php'),
                $this->compileControllerStub()
            );

            file_put_contents(
                base_path('database/migrations/' . now()->format('Y_m_d_his') . '_add_email_verified_column_to_users_table.php'),
                file_get_contents(__DIR__.'/stubs/migrations/add_email_verified_column_to_users_table.stub')
            );

            file_put_contents(
                base_path('routes/web.php'),
                file_get_contents(__DIR__.'/stubs/routes.stub'),
                FILE_APPEND
            );
        }

        $this->info('Email Verification scaffolding generated successfully.');
        if (! $this->option('views')) {
            $this->comment('Running composer dumpautoload, will take few minutes...');
            $this->composer->dumpAutoloads();
        }
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        if (! is_dir($directory = resource_path('views/auth/verify_email'))) {
            mkdir($directory, 0755, true);
        }
        if (! is_dir($directory = resource_path('views/mails/auth'))) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportViews()
    {
        foreach ($this->views as $key => $value) {
            if (file_exists($view = resource_path('views/'.$value)) && ! $this->option('force')) {
                if (! $this->confirm("The [{$value}] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                __DIR__.'/stubs/views/'.$key,
                $view
            );
        }
    }

    /**
     * Compiles the HomeController stub.
     *
     * @return string
     */
    protected function compileControllerStub()
    {
        return str_replace(
            '{{namespace}}',
            $this->getAppNamespace(),
            file_get_contents(__DIR__.'/stubs/controllers/auth/VerifyEmailController.stub')
        );
    }
}
