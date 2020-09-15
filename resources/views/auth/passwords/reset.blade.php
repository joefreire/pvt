@extends('layouts.app')

@section('content')
<div class="container">
    <form method="POST" action="{{ route('salvaPassword') }}">
        @csrf
        <input name="email" type="hidden" value="{{\Session::get('email')}}" required>
        <div class="form-group row">
            <label for="password" class="col-md-4 col-form-label text-md-right">Nova senha</label>

            <div class="col-md-6">
                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                @if ($errors->has('password'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Confirme a nova senha</label>

            <div class="col-md-6">
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary">
                    Trocar
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
