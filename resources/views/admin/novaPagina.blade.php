@extends('layouts.admin')
@section('styles')
@endsection
@section('content')
<div class="box">
	<div class="box-header">
		<h3 class="box-title">{{isset($id) ? "Editar Pagina" : "Nova Pagina"}}</h3>
		<div class="box-body">
			<form class="form-horizontal" method="POST" action="{{ route('salvaPagina') }}" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="id" value="{{ old('id') }}">

				<div class="form-group">
					<label for="submenu" class="col-lg-2 control-label">Categoria</label>

					<div class="col-md-4">
						<select name="categoria_id" id="categoria_id" class="form-control{{ $errors->has('categoria_id') ? ' is-invalid' : '' }}" required>
							<option value="" {{ old('categoria_id') == '' ? 'selected' : '' }}></option>
							@foreach(\App\Categoria::all() as $value)
							<option value="{{$value->id}}" {{ old('categoria_id') == $value->id ? 'selected' : '' }}>{{$value->nome}}</option>
							@endforeach
						</select>

					</div>
				</div>
				<div class="form-group">
					<label for="submenu" class="col-lg-2 control-label">Submenu</label>

					<div class="col-md-4">
						<select name="submenu" id="submenu" class="form-control{{ $errors->has('submenu') ? ' is-invalid' : '' }}" required>
							<option value="" {{ old('submenu') == '' ? 'selected' : '' }}></option>
							<option value="1" {{ old('submenu') == '1' ? 'selected' : '' }}>Sim</option>
							<option value="0" {{ old('submenu') == '0' ? 'selected' : '' }}>Não</option>
						</select>

					</div>
				</div>
				<div class="form-group">
					<label for="descricao" class="col-lg-2 control-label">Titulo:</label>
					<div class="col-lg-4">
						<input type="text" class="form-control input-sm" name="titulo" id="titulo" value="{{ old('titulo') }}"> 
					</div>
				</div>
				<div class="form-group">
					<label for="descricao" class="col-lg-2 control-label">Descrição:</label>
					<div class="col-lg-4">
						<input type="text" class="form-control input-sm" name="descricao" id="descricao" value="{{ old('descricao') }}"> 
					</div>
				</div>
				<br/>
				<div class="form-group">
					<div class="col-lg-12">
						<textarea name="conteudo" id="conteudo" class="ckeditor">{!! old('conteudo') !!}</textarea>  
					</div>
				</div>						

				<div class="form-group">
					<div class="col-lg-2 col-lg-offset-10">
						<button type="submit" class="btn btn-success btn-flat">Confirmar</button>
						<a href="{{ route('Pagina') }}" type="reset" class="btn btn-danger btn-flat">Cancelar</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script>
	var route_prefix = "{{ url(config('lfm.url_prefix')) }}";
</script>
<!-- CKEditor init -->
<script src="//cdnjs.cloudflare.com/ajax/libs/ckeditor/4.5.11/ckeditor.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/ckeditor/4.5.11/adapters/jquery.js"></script>
<script>
	$('textarea[name=conteudo]').ckeditor({
		height: 400,
		filebrowserImageBrowseUrl: route_prefix + '?type=Images',
		filebrowserImageUploadUrl: route_prefix + '/upload?type=Images&_token={{csrf_token()}}',
		filebrowserBrowseUrl: route_prefix + '?type=Files',
		filebrowserUploadUrl: route_prefix + '/upload?type=Files&_token={{csrf_token()}}'
	});
</script>
@endsection