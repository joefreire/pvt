<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
use App\Models\QuadroMultiplo;
use App\Models\Cidades;
use App\Models\Coordenadores;
use App\Models\Implantacao;
use App\Models\Instituicoes;
use App\Models\Qualidade;
use App\Models\Analise;
use App\Models\Monitoramento;
use App\Models\Acoes;

class CadastroInicial implements FromView
{

	public $Ano;
	public $CodCidade;

    /**
     * ListaUnica constructor.
     * @param $dataImport
     */
    public function __construct($Ano, $CodCidade)
    {
    	set_time_limit(0);
    	$this->Ano = $Ano;
    	$this->CodCidade = $CodCidade;
    }
    public function view(): View
    {

        $Monitoramento =  Monitoramento::where('Ano', $this->Ano)->where('CodCidade',$this->CodCidade)->orderBy('id','desc')->limit(1)->first();
        $Coordenadores =  Coordenadores::where('Ano', $this->Ano)->where('CodCidade',$this->CodCidade)->orderBy('id','desc')->limit(1)->first();
        $Acoes         =  Acoes::where('Ano', $this->Ano)->where('CodCidade',$this->CodCidade)->orderBy('id','desc')->limit(1)->first();
        $Analise       =  Analise::where('Ano', $this->Ano)->where('CodCidade',$this->CodCidade)->orderBy('id','desc')->limit(1)->first();
        $Implantacao   =  Implantacao::where('Ano', $this->Ano)->where('CodCidade',$this->CodCidade)->orderBy('id','desc')->limit(1)->first();
        $Qualidade     =  Qualidade::where('Ano', $this->Ano)->where('CodCidade',$this->CodCidade)->orderBy('id','desc')->limit(1)->first();

        $cidade =  Cidades::where('codigo', $this->CodCidade)->first();

        return view('exports.cadastroInicial', [
            'Monitoramento' => $Monitoramento,
            'Coordenadores' => $Coordenadores,
            'Acoes' => $Acoes,
            'Analise' => $Analise,
            'Implantacao' => $Implantacao,
            'Qualidade' => $Qualidade,
            'Cidade' => $cidade,
            'Ano' => $this->Ano,
        ]);
    }
}
