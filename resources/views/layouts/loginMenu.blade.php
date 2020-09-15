            <div class="navbar-header col-md-9 col-9" id="login-logout">
                <ul class="nav navbar-nav pull-right">
                    <li class="dropdown pull-right">
                        <a href="#" class="glyphicon glyphicon-user dropdown-toggle" data-toggle="dropdown"><b> LOGIN</b> <span class="caret"></span></a>
                        <ul id="login-dp" class="dropdown-menu dropdown-left-manual">
                            <li>
                                <div class="row">
                                    <div class="col-md-12">   

                                        <form class="form" role="form" method="post" action="{{ route('login') }}"accept-charset="UTF-8" id="login-nav">
                                            @csrf
                                            <div class="form-group">
                                                <label class="sr-only" for="email">Usuário</label>
                                                <input id="email" type="text" class="form-control" name="email" placeholder="Email" required="">
                                            </div>
                                            <div class="form-group">
                                                <label class="sr-only" for="password">Password</label>
                                                <input type="password" class="form-control" name="password" autocomplete="off" placeholder="Senha" required="">{{-- 
                                                <div class="help-block text-right"><a href="{{ route('reset_password') }}">Esqueci minha senha ?</a></div> --}}
                                            </div>

                                            <button type="submit" name="login" value="login" class="btn btn-primary btn-block">Entrar</button>
                                            <br>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul> 
            </div> 