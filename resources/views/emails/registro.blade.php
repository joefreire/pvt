<html>
    <body>
        <p>Olá {{ $user->name }}!</p>
        <p></p>
        <p>Você foi cadastrado no Programa vida no trânsito.</p>
        <p>para acessar o programa entre em <a href="{{ config('app.url') }}">{{ config('app.url') }}</a></p>
        <p>Utilize seu email: {{ $user->email }} e sua senha para acesso <b>{{$password}}</b></p>
        <p></p>
        
        <p>Att, <br>
        {{ config('app.name') }} !</p>
    </body>
</html>