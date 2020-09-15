
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- jQuery -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <!-- Bootstrap-->
    <link rel="stylesheet" href="{{asset('libraries/css/bootstrap.min.css')}}">      
    <link rel="stylesheet" href="{{asset('libraries/css/estilo.css')}}">  
    <link rel="stylesheet" type="text/css" href="{{asset('libraries/css/alertify.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('libraries/css/bootstrap_alertify.css')}}">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.0/bootstrap-table.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.1/css/bootstrap-datepicker.min.css">
    {{-- <link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css"> --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs-3.3.7/jqc-1.12.4/dt-1.10.20/datatables.min.css"/>
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css">

    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    <style>
        .isloading-show{
            position: relative;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-fluid  visible-xs visible-sm">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar" style="
            background-color: #888;
            "></span>
            <span class="icon-bar" style="
            background-color: #888;
            "></span>
            <span class="icon-bar" style="
            background-color: #888;
            "></span>
        </button>
        <a class="navbar-brand" href="#">Projeto Vida no Trânsito</a>
    </div>
    <div id="navbar" class="navbar-collapse collapse" aria-expanded="false" style="height: 1px;">
        <ul class="nav navbar-nav pull-left">
            <li>
                <a href="{{route('situacao')}}"> Cadastro e Situação do PVT </a>

            </li>
            @include('layouts.linkagemMenu')
            @include('layouts.monitoramentoMenu')
            @include('layouts.relatoriosMenu')

        </ul> 
    </div>
</div>
<div id="container">
    <div id="header">

      <nav id="navbar-primary" class="navbar visible-lg visible-md" role="navigation">
        <div class="container-fluid" id="menu-top" 
        style="padding-left: 0px;">
        @if(Auth::guest())
        <div class="navbar-header col-md-3">
            @else
            <div class="navbar-header col-md-2">
                @endif
                <ul class="nav navbar-nav">
                    <li class="navbar-logo pull-left">
                        <a href="/"><img id="logo-navbar-middle" src="{{asset('libraries/img/logo-azul.gif')}}" alt="Projeto Vida no Trânsito"></a>
                    </li>
                    @if(Auth::guest()) 
{{--                     <li class="hidden-xs hidden-sm hidden-md pull-right titulo-site"> 
                        <a class="navbar-title" href="index.php" alt="Projeto Vida no Trânsito" title="Inicio">PROGRAMA VIDA NO TRÂNSITO</a>
                    </li> --}}
                    @endif
                </ul> 
            </div>
            @if(!Auth::guest())
            <div class="navbar-header col-sm-8" id="login-logout">
                <ul class="nav navbar-nav pull-left">
                    <li>
                        <a href="{{route('situacao')}}"> Cadastro e Situação do PVT </a>

                    </li>
                    @include('layouts.linkagemMenu')
                    @include('layouts.monitoramentoMenu')
                    @include('layouts.relatoriosMenu')

                </ul> 
            </div>
            @endif
            @if(Auth::guest())
            @include('layouts.loginMenu')
            @else
            <div class="navbar-header col-md-2 email-login" id="login-logout">
                <div class="row pull-right">
                    <ul class="nav navbar-nav" style="width: 50%;">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bell"></i><span class="label label-danger">{{ $processos->where('updated_at','>',\Carbon\carbon::now()->subMinutes(60))->count() }}</span></a>
                            <ul class="dropdown-menu notify-drop">
                                <div class="notify-drop-title">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-6">Notificações (<b>{{ $processos->where('updated_at','>',\Carbon\carbon::now()->subMinutes(60))->count() }}</b>)</div>
                                        <div class="col-md-6 col-sm-6 col-xs-6 text-right"><a href="" class="rIcon allRead" data-tooltip="tooltip" data-placement="bottom" title="Notificação"><i class="fa fa-dot-circle-o"></i></a></div>
                                    </div>
                                </div>
                                <!-- end notify title -->
                                <!-- notify content -->
                                <div class="drop-content">
                                    @php
                                    if($processos->where('Log','Na fila para processamento')->count() > 0){
                                       $processosAguardando = \App\Models\Processo::where('Log', 'Na fila para processamento')->get();
                                   }

                                   @endphp

                                   @foreach($processos as $processo)
                                   <li>
                                    <div class="col-md-2">
                                        <div class="notify-img">
                                            #{{ $processo->id }}
                                        </div>
                                    </div> 
                                    <div class="col-md-10 notification">
                                        {{ !empty($processo->Cidade)?$processo->Cidade->municipio:'' }} - {{ $processo->Ano }}/{{ $processo->Trimestre }}   
                                        <BR>
                                        {{ $processo->Log }}    
                                        @if($processo->Log == 'Na fila para processamento')
                                        <BR>
                                        @php                                      

                                        $posicoes = $processosAguardando->where('id',$processo->id);
                                        foreach($posicoes as $key => $position){
                                            $posicaoProcesso = $key+1;
                                        }

                                        @endphp
                                        Esse processo é o {{ $posicaoProcesso }}º na fila
                                        @endif
                                        <BR>
                                        Inicio: {{ $processo->created_at->format('d/m/Y H:i:s') }}    
                                        <BR>
                                        @if($processo->updated_at != $processo->created_at )
                                        Última Atualização: {{ $processo->updated_at->format('d/m/Y H:i:s') }} 
                                        @endif   
                                        <hr>
                                        <span class="label label-primary">{{ $processo->Processo }}</span>
                                        @if($processo->Status == 1)
                                        <span class="label label-success">Finalizado</span>
                                        @elseif($processo->Status == 0)
                                        <span class="label label-info">Iniciado</span>
                                        @elseif($processo->Status == 2)
                                        <span class="label label-warming">Cancelado</span>
                                        @elseif($processo->Status == 3)
                                        <span class="label label-danger">Erro</span>
                                        @endif

                                    </div>
                                </li>
                                @endforeach

                            </div>
                            <div class="notify-drop-footer text-center">
                               {{--  <a href=""><i class="fa fa-eye"></i> </a> --}}
                           </div>
                       </ul>
                   </li>
               </ul>
               <ul class="nav navbar-nav pull-right" style="width: 50%;">
                 <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="glyphicon glyphicon-user"></i></a>
                    <ul id="login-dp" class="dropdown-menu dropdown-left-manual" style="min-width: 140px; padding: 14px 0px 0;">
                        <li><p class="text-center"><b>{{Auth::user()->nome}}</b></p></li>
                        <li class="divider"></li>
                        <li><a href="{{ route('user.edit') }}"><i class="glyphicon glyphicon-user"></i> Perfil</a></li>
                        <li><a href="{{route('showNewPassword')}}"><i class="glyphicon glyphicon-transfer"></i> Trocar Senha</a> </li>
                        @if(Auth::user()->tipo <= 3)
                        <li><a href="{{route('getUsuarios')}}"><i class="glyphicon glyphicon-cog"></i> Usuários</a> </li>
                        @endif
                        <li class="divider"></li>
                        <li>                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-sign-out fa-lg" aria-hidden="true"></i><span>Sair</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                    <br>
                </ul>
            </li> 
        </ul> 
    </div>
</div>
@endif



</div>
</nav>


</div>
<div id="body">
    @include('layouts.errors')
    <div id="main" class="container-fluid clear-top">  
        <span class="h6">&nbsp;</span>
        @yield('content')
        <div class="clearfix">
        </div>
    </div>

    <div id="footer">
        <section class="t3-copyright">
            <div class="container">
                <div class="row">

                    <div class="pull-right">
                        <p><img src="{{asset('libraries/img/logos2.png')}}" alt="Projeto Vida no Trânsito" style=""></p>
                        <br>
                    </div>     
                </div>

            </div>

        </section>
    </div>
</div>
<script src="//code.jquery.com/jquery-2.2.3.min.js" type="text/javascript"></script>
<script src="{{asset('libraries/js/bootstrap.min.js')}}" type="text/javascript"></script> 
<script src="{{asset('libraries/js/cidades-estados-1-4.js')}}" type="text/javascript"></script> 
<script src="{{asset('libraries/js/jquery.mask.js')}}" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.0/bootstrap-table.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.0/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.1/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.1/locales/bootstrap-datepicker.pt-BR.min.js"></script>
{{-- 
    <script src="{{ asset('libraries/js/bootstrap-table-toolbar.js')}}" type="text/javascript"></script> --}}
    <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script> 
    <script src="//cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script> 
    <script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script> 
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css">
    <script src="{{ asset('libraries/js/alertify.min.js')}}"></script> 
    <script src="{{ asset('libraries/js/jquery.isloading.min.js') }}"></script>
    <script type="text/javascript">

        alertify.defaults.transition = "slide";
        alertify.defaults.theme.ok = "btn btn-primary";
        alertify.defaults.theme.cancel = "btn btn-danger";
        alertify.defaults.theme.input = "form-control";
        alertify.defaults.title = "Alerta";
    </script>

    @yield('scripts')
    @if(!Auth::guest())
    <link rel="manifest" href="/manifest.json" />
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
    <script>
      var OneSignal = window.OneSignal || [];
      OneSignal.push(function() {
        OneSignal.sendTags({user_id: '{{Auth::id()}}'});

        OneSignal.init({
          appId: "91dbe9da-dcf2-4c42-b47d-2756d2d98237",
          autoResubscribe: true,
      });
    });
</script>
@endif


</body>
</html>


