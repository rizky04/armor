    <div class="header">

            <div class="header-left active">
                {{-- <a href="/" class="logo">
                    <img src="{{asset('assets/assets/img/1234.png')}}" width="30" height="30">
                </a>
                <a href="/" class="logo-small">
                    <img src="{{asset('assets/assets/img/1234.png')}}" width="30" height="30">
                </a> --}}
<a href="/" class="logo">
    {{-- <img src="{{ asset('assets/assets/img/1234.png') }}"
         alt="Logo"
         class="logo-img rounded-2xl"> --}}
         <h6 style="color: black">ARMOR MOTOR</h6>
</a>

<a href="/" class="logo-small">
    <img src="{{ asset('assets/assets/img/1234.png') }}"
         alt="Logo"
         class="logo-img-small">
</a>

                <a id="toggle_btn" href="javascript:void(0);">
                </a>
            </div>

            <a id="mobile_btn" class="mobile_btn" href="#sidebar">
                <span class="bar-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </a>

            <ul class="nav user-menu">
                <li class="nav-item dropdown has-arrow main-drop">
                    <a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
                        <span class="user-img"><img src="{{asset('assets/assets/img/1234.png')}}" alt="">
                            <span class="status online"></span></span>
                    </a>
                    <div class="dropdown-menu menu-drop-user">
                        <div class="profilename">
                            <div class="profileset">
                                <span class="user-img"><img src="{{asset('assets/assets/img/1234.png')}}" alt="">
                                    <span class="status online"></span></span>
                                <div class="profilesets">
                                    <h6>{{ Auth::user()->name }}</h6>
                                    {{-- <h5>Admin</h5> --}}
                                </div>
                            </div>
                            {{-- <hr class="m-0"> --}}
                            {{-- <a class="dropdown-item" href="profile.html"> <i class="me-2" data-feather="user"></i> My
                                Profile</a>
                            <a class="dropdown-item" href="generalsettings.html"><i class="me-2"
                                    data-feather="settings"></i>Settings</a> --}}
                            <hr class="m-0">
                            <a class="dropdown-item logout pb-0" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <img src="{{asset('assets/assets/img/icons/log-out.svg')}}" class="me-2" alt="img">Logout
                            </a>
                             <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                        </div>
                    </div>
                </li>
            </ul>


            <div class="dropdown mobile-user-menu">
                <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                    aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
 <a class="dropdown-item logout pb-0" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <img src="{{asset('assets/assets/img/icons/log-out.svg')}}" class="me-2" alt="img">Logout
                            </a>
                             <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                    {{-- <a class="dropdown-item" href="{{ route('logout') }}">Logout</a> --}}
                </div>
            </div>

        </div>
