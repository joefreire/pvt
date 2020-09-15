@extends('layouts.app')

@section('content')
<div class="col-md-12">

  <form method="post" action="{{ route('register') }}"  name="registerform">
    @csrf
    @if(isset($id))
    <input type="hidden" name="id" value="{{ $id }}">
    @endif
    <div class="row">
      <!-- Text input-->
      <div class="form-group col-md-6">
        <label for="nome">Nome:</label>  
        <input  value="{{ old('nome') }}" id="nome" name="nome" type="text" class="form-control" required="">
      </div>
    </div>    

    <div class="row">
      <!-- Text input-->
      <div class="form-group col-md-4">
        <label for="instituicao">Instituição:</label>  
        <input id="instituicao" value="{{ old('instituicao') }}" name="instituicao" type="text" class="form-control" required="">
      </div>
      <!-- Text input-->
      <div class="form-group col-md-4">
        <label for="formacao">Formação:</label>  
        <input id="formacao" value="{{ old('formacao') }}" name="formacao" type="text" class="form-control" required="">
      </div>        
      <!-- Text input-->
      <div class="form-group col-md-4">
        <label for="telefone">Telefone:</label>  
        <input id="telefone" value="{{ old('telefone') }}" name="telefone" type="text" class="form-control" required="">

      </div>
    </div>      

    <div class="row">
      <!-- Text input-->
      <div class="form-group col-md-4">
        <label for="atividade">Atividade Executada:</label>  
        <input id="atividade" value="{{ old('atividade') }}" name="atividade" type="text" class="form-control" required="">
      </div>
      <!-- Text input-->
      @if(Auth::user()->tipo == 1)
      <div class="form-group col-md-4">
        <label for="Estado">Estado:</label>  
        <select id="uf" name="uf" class="form-control"required=""> </select>
      </div> 
      @endif       
      <!-- Text input-->
      @if(Auth::user()->tipo <= 2)
      <div class="form-group col-md-4">
        <label for="cidade">Cidade:</label>  
        <select id="cidade" name="cidade" class="form-control "required=""> 
          <option value="">Selecione uma cidade</option>
          @if(Auth::user()->tipo == 2)
          @foreach(\App\Models\Cidades::where('uf',Auth::user()->cidade->uf)->get() as $cidade)
          <option value="{{ $cidade->codigo }}">{{ $cidade->municipio }}</option>
          @endforeach
          @endif
        </select>

      </div>
      @endif
    </div>      

    <div class="row">
      <div class="form-group col-md-4">
        <label for="email">Email</label>
        <input id="email"  value="{{ old('email') }}" type="email" class="form-control" name="email" required />
      </div>
      <div class="form-group col-md-4">
        <label for="tipo">Tipo de usuário:</label>
        <select id="tipo" name="tipo" class="form-control">
          @if(Auth::user()->tipo == 1)
          <option value="1">Administrador</option>
          @endif
          @if(Auth::user()->tipo <= 2)
          <option value="2">Administrador Estado</option>
          @endif
          @if(Auth::user()->tipo <= 3)
          <option value="3">Administrador Municipio</option>
          @endif
          <option value="4">Usuário</option>
        </select>
      </div>
      <div class="form-group col-md-4">
        <br>
        <input type="submit" class="form-control" name="register" value="Salvar" />
      </div>

    </div> 




  </div> 
</form>

@endsection


@section('scripts')
@if(Auth::user()->tipo == 1)
<script language="JavaScript" type="text/javascript" charset="utf-8">
  new dgCidadesEstados({
    cidade: document.getElementById('cidade'),
    estado: document.getElementById('uf')
  })
</script>
@endif
<script type="text/javascript">
  var SPMaskBehavior = function (val) {
    return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
  },
  spOptions = {
    onKeyPress: function(val, e, field, options) {
      field.mask(SPMaskBehavior.apply({}, arguments), options);
    }
  };

  $('#telefone').mask(SPMaskBehavior, spOptions);
  $(document).ready(function(){
    @if(isset(old('cidade')['uf']))
    $("#uf").val("{{old('cidade')['uf']}}").trigger("change");
    @endif
    @if(!empty(old('cidade')))
    $("#cidade").val("{{ old('cidade')['municipio'] }}")
    @endif
    @if(!empty(old('atividade')))
    $("#atividade").val("{{ old('atividade')}}")
    @endif
    @if(!empty(old('tipo')))
    $("#tipo").val("{{ old('tipo')}}")
    @endif
  });
</script>
@endsection