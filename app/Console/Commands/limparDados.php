<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class limparDados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'limpardados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        exit;
        dd('tchau');
        $delete = \App\Models\Vitimas::truncate();
        $delete = \App\Models\Sim::truncate();
        $delete = \App\Models\Sih::truncate();
        $delete = \App\Models\Acoes::truncate();
        $delete = \App\Models\Analise::truncate();
        $delete = \App\Models\Audits::truncate();
        $delete = \App\Models\FatoresRisco::truncate();
        $delete = \App\Models\Implantacao::truncate();
        $delete = \App\Models\Instituicoes::truncate();
        $delete = \App\Models\LinkagemSim::truncate();
        $delete = \App\Models\LinkagemSih::truncate();
        $delete = \App\Models\ListaUnicaPendencias::truncate();
        $delete = \App\Models\Monitoramento::truncate();
        $delete = \App\Models\Plano::truncate();
        $delete = \App\Models\Processo::truncate();
        $delete = \App\Models\Projeto::truncate();
        $delete = \App\Models\QuadroMultiplo::truncate();
        $delete = \App\Models\Qualidade::truncate();

    }
}
