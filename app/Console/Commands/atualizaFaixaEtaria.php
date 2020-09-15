<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class atualizaFaixaEtaria extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'atualizaFaixaEtaria';

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
        $vitimas = \App\Models\Vitimas::all();
        foreach ($vitimas as $vitima) {
            $idade = calculaIdade($vitima->DataNascimento, $vitima->DataAcidente);
            $FaixaEtaria = calculaFaixaEtaria($idade);
            $vitima->Idade = $idade;
            $vitima->FaixaEtaria = $FaixaEtaria;
            $vitima->save();
        }
    }
}
