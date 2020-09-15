<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'Controller@index')->name('home');
Route::get('/teste-linkagem', 'SimController@teste');
// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->name('login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('/usuario/novo', 'Auth\RegisterController@showNewUserForm')->name('user.novo');
Route::post('register', 'Auth\RegisterController@register')->name('register');

Route::get('/usuario/edit/{id?}', 'Auth\RegisterController@showRegistrationForm')->name('user.edit');
Route::get('/usuario/reset/{id?}', 'Auth\RegisterController@resetPassword')->name('user.reset');

// Password Reset Routes...
Route::get('password/reset', 'Auth\LoginController@resetPassword')->name('reset_password');
Route::post('password/reset/salva', 'Auth\LoginController@salvaPassword')->name('salvaPassword');

Route::get('password/novo','Auth\ChangePasswordController@showNewPassword')->name('showNewPassword');
Route::post('password/novo','Auth\ChangePasswordController@newPassword')->name('newPassword');

Route::group(['middleware' => 'auth:web', 'prefix' => '/quadro-multiplo'], function() {
	Route::get('/', 'QuadroMultiploController@index')->name('quadroMultiplo');
	Route::post('/get-data', 'QuadroMultiploController@dataQuadroMultiplo')->name('dataQuadroMultiplo');
	Route::post('/check', 'QuadroMultiploController@checkQuadroMultiplo')->name('checkQuadroMultiplo');
	Route::post('/deletar-vitima', 'QuadroMultiploController@deletaVitima')->name('delete.vitima');
	Route::post('/deletar-acidente', 'QuadroMultiploController@deletaAcidente')->name('delete.acidente');
	Route::post('/salvar', 'QuadroMultiploController@saveQuadroMultiplo')->name('store.quadro');
	Route::get('/relatorio', 'QuadroMultiploController@relatorio')->name('quadro.relatorio');
	Route::post('/relatorio/data', 'QuadroMultiploController@relatorioData')->name('quadro.relatorio.data');
});
Route::group(['middleware' => 'auth:web', 'prefix' => '/plano'], function() {
	Route::get('/', 'PlanoController@index')->name('plano');
	Route::any('/programas', 'PlanoController@BuscaProgramas')->name('plano.BuscaProgramas');
	Route::any('/projetos', 'PlanoController@BuscaProjetos')->name('plano.BuscaProjetos');
	Route::post('/programa/gravar', 'PlanoController@gravaPlano')->name('plano.gravar');
	Route::post('/projeto/gravar', 'PlanoController@gravaProjeto')->name('projeto.gravar');
	Route::post('/peso-total', 'PlanoController@TotalPlano')->name('plano.PesoTotal');
	Route::post('/projeto/peso-total', 'PlanoController@PesoTotalProjeto')->name('projeto.PesoTotal');
	Route::post('/projeto/peso-por-projeto', 'PlanoController@PesoPorProjeto')->name('projeto.PesoPorProjeto');
	Route::post('/projeto/remover', 'PlanoController@removerProjeto')->name('projeto.remover');
	Route::post('/programa/remover', 'PlanoController@removerPlano')->name('plano.remover');

});
Route::group(['middleware' => 'auth:web', 'prefix' => '/lista-unica'], function() {
	Route::get('/', 'ListaUnicaController@index')->name('listaUnica');
	Route::post('/check', 'ListaUnicaController@checkListaUnica')->name('checkListaUnica');
	Route::post('/pendencias', 'ListaUnicaController@pendeciasListaUnica')->name('getPendeciasListaUnica');
	Route::post('/check-pendencias', 'ListaUnicaController@checkPendencias')->name('checkPendencias');
	Route::post('/data-pedencias', 'ListaUnicaController@dataPendenciasListaUnica')->name('dataPendencias');
	Route::post('/grava', 'ListaUnicaController@gravaLista')->name('gravaLista');
	Route::post('/grande', 'ListaUnicaController@listaUnicaGrande')->name('listaUnicaGrande');
	Route::get('/relatorio', 'ListaUnicaController@relatorio')->name('listaUnica.relatorio');
	Route::post('/relatorio/data', 'ListaUnicaController@relatorioData')->name('listaUnica.relatorio.data');
	Route::post('/delete', 'ListaUnicaController@deleteDados')->name('deleteDados');
});
Route::group(['middleware' => 'auth:web', 'prefix' => '/sim'], function() {
	Route::get('/', 'SimController@index')->name('sim');
	Route::get('/relatorio', 'SimController@relatorio')->name('sim.relatorio');
	Route::post('/relatorio/data', 'SimController@dataSim')->name('sim.relatorio.data');
	Route::get('/pares', 'SimController@pares')->name('sim.pares');
	Route::post('/check', 'SimController@checkSim')->name('checkSim');
	Route::post('/grava', 'SimController@gravaSim')->name('gravaSim');
	Route::post('/data', 'SimController@dataLinkagem')->name('sim.data');
	Route::post('/salva-pares', 'SimController@salvaPares')->name('sim.salvaPares');
	Route::get('/relatorio/pares', 'SimController@relatorioPares')->name('sim.relatorio.pares');
	Route::post('/relatorio/pares/data', 'SimController@dataParesSim')->name('sim.relatorio.pares.data');
	Route::post('/delete', 'SimController@deleteDados')->name('sim.deleteDados');
});
Route::group(['middleware' => 'auth:web', 'prefix' => '/relatorios'], function() {
	Route::get('/geral', 'Controller@relatorioGeral')->name('resultado.geral');
	Route::post('/geral/data', 'Controller@relatorioGeralData')->name('resultado.geral.data');
	Route::get('/indicadores', 'Controller@relatorioIndicadores')->name('resultado.indicadores');
	Route::post('/indicadores/data', 'Controller@relatorioIndicadoresData')->name('resultado.indicadores.data');
});

Route::group(['middleware' => 'auth:web', 'prefix' => '/sih'], function() {
	Route::get('/', 'SihController@index')->name('sih');
	Route::get('/relatorio', 'SihController@relatorio')->name('sih.relatorio');
	Route::post('/relatorio/data', 'SihController@dataSih')->name('sih.relatorio.data');
	Route::get('/pares', 'SihController@pares')->name('sih.pares');
	Route::post('/check', 'SihController@checkSih')->name('checkSih');
	Route::post('/grava', 'SihController@gravaSih')->name('gravaSih');
	Route::post('/data', 'SihController@dataLinkagem')->name('sih.data');
	Route::post('/salva-pares', 'SihController@salvaPares')->name('sih.salvaPares');
	Route::get('/relatorio/pares', 'SihController@relatorioPares')->name('sih.relatorio.pares');
	Route::post('/relatorio/pares/data', 'SihController@dataParesSih')->name('sih.relatorio.pares.data');
	Route::post('/delete', 'SihController@deleteDados')->name('sih.deleteDados');
});
Route::group(['middleware' => 'auth:web'], function() {
	Route::get('/situacao', 'Controller@situacao')->name('situacao');
	Route::post('/situacao/status', 'Controller@situacaoStatus')->name('situacao.status');
	Route::post('/situacao/copia', 'Controller@copiaDados')->name('situacao.copia');

	Route::post('/situacao/coordenadores/grava', 'Controller@coordenadores')->name('situacao.coordenadores.grava');
	Route::post('/situacao/coordenadores/get', 'Controller@buscaCoordenadores')->name('situacao.coordenadores.get');

	Route::post('/situacao/implantacao/grava', 'Controller@implantacao')->name('situacao.implantacao.grava');
	Route::post('/situacao/implantacao/get', 'Controller@buscaImplantacao')->name('situacao.implantacao.get');

	Route::post('/situacao/qualidade/grava', 'Controller@qualidade')->name('situacao.qualidade.grava');
	Route::post('/situacao/qualidade/get', 'Controller@buscaQualidade')->name('situacao.qualidade.get');

	Route::post('/situacao/analise/grava', 'Controller@analise')->name('situacao.analise.grava');
	Route::post('/situacao/analise/get', 'Controller@buscaAnalise')->name('situacao.analise.get');
	
	Route::post('/situacao/acoes/grava', 'Controller@acoes')->name('situacao.acoes.grava');
	Route::post('/situacao/acoes/get', 'Controller@buscaAcoes')->name('situacao.acoes.get');
		
	Route::post('/situacao/monitoramento/grava', 'Controller@monitoramento')->name('situacao.monitoramento.grava');
	Route::post('/situacao/monitoramento/get', 'Controller@buscaMonitoramento')->name('situacao.monitoramento.get');
	
	Route::post('/situacao/exporta', 'Controller@exportarCadastroInicial')->name('situacao.exportar');
	
	Route::post('/mun_ibge', 'AdminController@getCidades')->name('getCidades');

});
Route::group(['middleware' => 'auth:web'], function() {
	Route::get('/usuarios', 'AdminController@getUsuarios')->name('getUsuarios');
	Route::post('/usuarios', 'AdminController@getUsuarios')->name('getUsuarios');
	Route::get('/admin', 'AdminController@auditoria')->name('auditoria');
	Route::post('/admin', 'AdminController@auditoria')->name('auditoria');	
	Route::post('/getCoordenada', 'AdminController@getCoordenada')->name('getCoordenada');
});

// Route::get('/teste/{id}', function ($id) {
// 	\OneSignal::sendNotificationUsingTags(
//             "Teste de notificacao",
//             array(
//                 ["field" => "tag",
//                 "key" => "user_id",
//                 "relation" => "=", 
//                 "value" => $id]
//             ),
//             $url = null,
//             $data = null,
//             $buttons = null,
//             $schedule = null
//         );
// });
Route::get('/teste-data', function () {
	$data = '16/0s6/1978';
	return validaData($data);
});