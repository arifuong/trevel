<?php

namespace App\Console\Commands;

use Database\Seeders\JamaahResetSeeder;
use Illuminate\Console\Command;

class ResetJamaahAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jamaah:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus seluruh akun jamaah dan buat tepat 1 akun jamaah baru (jamaah@example.com)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai proses reset akun Jamaah...');

        $seeder = new JamaahResetSeeder();
        $seeder->run();

        $this->info('Akun Jamaah berhasil di-reset!');
        $this->table(
            ['Nama', 'Email', 'Role', 'Status', 'Password'],
            [['Jamaah', 'jamaah@example.com', 'jamaah', 'Aktif', 'Jamaah123!']]
        );

        return Command::SUCCESS;
    }
}
