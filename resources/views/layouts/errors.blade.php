
@if(Session::has('error'))
<div class="container" style="width: 100%">
  <div class="alert alert-danger" style="margin-top: 15px;"> 
    <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
    {!! Session::get('error') !!}
  </div>
</div>
@endif

@if(Session::has('alerta'))
<div class="container" style="width: 100%">
  <div class="alert alert-warning" style="margin-top: 15px;"> 
    <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
    {!! Session::get('alerta') !!}
  </div>
</div>
@endif

@if(Session::has('success'))
<div class="alert alert-success" style="margin: 15px;"> 
  <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
  {!! Session::get('success') !!}
</div>
@endif

@if($errors->any())
<div class="container" style="width: 100%">
  <div class="alert alert-danger" style="margin: 15px;"> 
    <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </div>
</div>
@endif