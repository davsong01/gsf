<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">
                <x-logo width="auto" height="70px"/>
            </a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="{{ route('home.index') }}">Home</a></li>
                <li class="dropdown">
                    <a href="{{ route('directory.index') }}" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        Directory <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('directory.index') }}">Directory Home</a></li>
                        <li><a href="{{ route('people.campuses') }}">Campuses</a></li>
                        <li><a href="{{ route('people.alumni') }}">Alumni</a></li>
                        <li><a href="{{ route('people.students') }}">Members</a></li>
                        <li><a href="{{ route('campus.tracker') }}">Campus Tracker</a></li>
                    </ul>
                </li>
                <li><a href="{{ route('people.programs') }}">Programs</a></li>
                @guest
                <li class="button-navbar"><a href="{{ route('login') }}"><i class="fa fa-user"></i> Login</a></li> 
                <li class="button-navbar"><a href="{{ route('newalumni') }}"><i class="fa fa-plus"></i></a></li> 
                @endguest
                @auth
                <li class="button-navbar"><a href="{{ route('account') }}"><i class="fa fa-user-circle-o"></i> My Account</a></li>
                @endauth
            </ul>
        </div><!--/.nav-collapse -->
    </div><!--/.container-fluid -->
</nav>

