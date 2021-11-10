<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use Exception;
use App\Models\Processo;
use App\Imports\ListaUnica;
use OneSignal;
use DB;

class ProcessaListaUnica implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $dataImport;
    public $file;
    public $processo;
    public $timeout = 0;
    public $tries = 1;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $dataImport, Processo $processo, $file)
    {
        set_time_limit(0);
        $this->file        = $file;
        $this->dataImport        = $dataImport;
        $this->user        = $user;
        $this->processo        = $processo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->processo->Status = 0;
        $this->processo->Log = "Em processamento";
        $this->processo->save();
        try {
            $file = Storage::disk('listas')->getAdapter()->getPathPrefix().$this->file;
            $excel = Excel::import(new ListaUnica($this->dataImport), $file);
            if($excel){
             \Log::alert('Sucesso ao importar Lista Unica '.$this->dataImport['CodCidade']. ' ANO : '.$this->dataImport['Ano']. ' Trimestre : '.$this->dataImport['Trimestre']);
         }else{
            $this->processo->Status = 3;
            $this->processo->Log = "Erro ao processar Lista Unica";
            $this->processo->save();
            \Log::alert('Erro ao importar Lista Unica '.$this->dataImport['CodCidade']. ' ANO : '.$this->dataImport['Ano']. ' Trimestre : '.$this->dataImport['Trimestre']);
        }

    } catch (Exception $e) {
        $this->processo->Status = 3;
        $this->processo->Log = "Erro ao processar Lista Unica";
        $this->processo->save();
        \Log::alert('Erro ao importar Lista Unica : '.$e->getMessage());
    }
    // OneSignal::sendNotificationUsingTags(
    //     "O processo da Lista unica".$this->processo->id." terminou",
    //     array(
    //         ["field" => "tag",
    //         "key" => "user_id",
    //         "relation" => "=", 
    //         "value" => $this->user->id]
    //     ),
    //     $url = null,
    //     $data = null,
    //     $buttons = null,
    //     $schedule = null
    // );
}
    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {

        $this->processo->Status = 3;
        $this->processo->Log = "Erro ao processar Lista Unica";
        $this->processo->save();
        \Log::alert('Erro ao importar Lista Unica : '.$exception->getMessage());
        // OneSignal::sendNotificationUsingTags(
        //     "O processo da Lista unica ".$this->processo->id." terminou com falha",
        //     array(
        //         ["field" => "tag",
        //         "key" => "user_id",
        //         "relation" => "=", 
        //         "value" => $this->user->id]
        //     ),
        //     $url = null,
        //     $data = null,
        //     $buttons = null,
        //     $schedule = null
        // );
    }

}
