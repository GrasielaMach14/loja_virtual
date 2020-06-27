<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/layout.css">
    <link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.8/css/materialize.min.css" media="screen,projection">
    <title>Minha Loja - @yield('titulo')</title>
</head>
<body>   
    <header id="mainHeader">
        <nav id="mainNav">
            <div class="nav-wrapper row" style="background-color:#555551;">
                <a href="{{ route('index') }}" class="brand-logo col offset-l1">Loja virtual</a>
                <a href="#" data-activates="mobile-menu" class="button-collapse"><i class="material-icons">menu</i></a>
                <ul id="mainUl">
                    <li><a href="{{ route('carrinho.compras') }}">Minhas compras</a></li>
                    <li><a href="{{ route('carrinho.index') }}">Carrinho</a></li>
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">Entrar</a></li>
                        <li><a href="{{ url('/register') }}">Cadastre-se</a></li>
                    @else
                        <li>
                            <a class="dropdown-button" href="#!" data-activates="dropdown-user">
                                Olá {{ Auth::user()->name }}!<i class="material-icons right">arrow_drop_down</i>
                            </a>
                            <ul id="dropdown-user" class="dropdown-content">
                                <li class="divider"></li>
                                <li>
                                    <a href="{{ url('/logout') }}"
                                        onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                                        Sair
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>

        </nav>
    </header>
    <main id="main2">
        @yield('conteudo')

        @if(!Auth::guest())
            <form id="logout-form" action="{{ url('/logout')  }}" method="POST" class="hide">
                {{ csrf_field() }}
            </form>
        @endif
    </main>
    <footer class="page-footer" style="background-color:#555551;">
        <div class="footer-copyright">
            <div class="container">
                Desenvolvido o carrinho de compras com Laravel
            </div>
        </div>
    </footer>
    <script type="text/javascript" src="//code.jquery.com/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.8/js/materialize.min.js"></script>    
    @stack('scripts')
    <script type="text/javascript">
        $(document).ready(function(){
            $(".button-collapse").sideNav();
            $('select').material_select();
        });
    </script>
</body>
</html>