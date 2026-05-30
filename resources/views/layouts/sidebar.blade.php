<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="{{ request()->routeIs('/') ? 'active' : '' }}">
                    <a href="{{ route('home') }}"><img src="{{ asset('assets/assets/img/icons/dashboard.svg') }}"
                            alt="img"><span>
                            Dashboard</span> </a>
                </li>
                @can('menu-master-data')
                    <li class="submenu">
                        <a href="javascript:void(0);"><img src="{{ asset('assets/assets/img/icons/users1.svg') }}"
                                alt="img"><span>
                                Master Data</span> <span class="menu-arrow"></span></a>
                        <ul>
                            @can('master-data-barang')
                                <li>
                                    <a href="{{ route('barang.index') }}"
                                        class="{{ request()->routeIs('barang.*') ? 'active' : '' }}">Barang</a>
                                </li>
                            @endcan
                            @can('master-data-stok')
                                @can('stok-opname')
                                    <li>
                                        <a href="{{ route('stok-opname.index') }}"
                                            class="{{ request()->routeIs('stok-opname.index') ? 'active' : '' }}">stok opname</a>
                                    </li>
                                @endcan
                                @can('stok-opname-log')
                                    <li>
                                        <a href="{{ route('stok-opname.logs') }}"
                                            class="{{ request()->routeIs('stok-opname.logs') ? 'active' : '' }}">history stok
                                            opname</a>
                                    </li>
                                @endcan
                                @can('stok-keluar-masuk')
                                    <li>
                                        <a href="{{ route('stok-transaksi.index') }}"
                                            class="{{ request()->routeIs('stok-transaksi.index') ? 'active' : '' }}">keluar masuk
                                            barang</a>
                                    </li>
                                @endcan
                            @endcan
                            @can('master-data-pembelian')
                                <li>
                                    <a href="{{ route('pembelian.index') }}"
                                        class="{{ request()->routeIs('pembelian.index') ? 'active' : '' }}">pembelian
                                        barang</a>
                                </li>
                            @endcan
                            @can('master-data-jasa')
                                <li>
                                    <a href="{{ route('jasa.index') }}"
                                        class="{{ request()->routeIs('jasa.*') ? 'active' : '' }}">Jasa</a>
                                </li>
                            @endcan
                            {{-- @can('master-data-mekanik')
                                <li>
                                    <a href="{{ route('mechanics.index') }}"
                                        class="{{ request()->routeIs('mechanics.*') ? 'active' : '' }}">Mekanik</a>
                                </li>
                            @endcan --}}
                        </ul>
                    </li>
                @endcan

                <li class="menu-title">Transaksi</li>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fa-solid fa-shop me-1"></i><span>
                            Penjualan Barang</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li>
                            <a href="{{ route('penjualan-platform.create') }}"
                                class="{{ request()->routeIs('penjualan-platform.create') ? 'active' : '' }}">
                                Tambah Penjualan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('penjualan-platform.index') }}"
                                class="{{ request()->routeIs('penjualan-platform.index') ? 'active' : '' }}">
                                Daftar Penjualan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('penjualan-platform.laporan') }}"
                                class="{{ request()->routeIs('penjualan-platform.laporan') ? 'active' : '' }}">
                                Laporan
                            </a>
                        </li>
                    </ul>
                </li>
                 <li class="submenu">
                    <a href="javascript:void(0);"><i class="fa-solid fa-motorcycle"></i><span>
                            Transaksi Service</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li>
                            <a href="{{ route('transaksi-motor.create') }}"
                                class="{{ request()->routeIs('transaksi-motor.create') ? 'active' : '' }}">
                                Buat Transaksi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('transaksi-motor.index') }}"
                                class="{{ request()->routeIs('transaksi-motor.index') ? 'active' : '' }}">
                                Daftar Transaksi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('transaksi-motor.laporan') }}"
                                class="{{ request()->routeIs('transaksi-motor.laporan') ? 'active' : '' }}">
                                Laporan
                            </a>
                        </li>
                    </ul>
                </li>
                  <li class="menu-title">Laporan Keuangan</li>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fa-solid fa-book-open me-1"></i><span>
                            Akuntansi</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li>
                            <a href="{{ route('pengeluaran.index') }}"
                                class="{{ request()->routeIs('pengeluaran.*') ? 'active' : '' }}">
                                Pengeluaran
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('akuntansi.laporan') }}"
                                class="{{ request()->routeIs('akuntansi.laporan') ? 'active' : '' }}">
                                Laporan Keuangan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('akuntansi.modal-barang') }}"
                                class="{{ request()->routeIs('akuntansi.modal-barang') ? 'active' : '' }}">
                                Modal Barang
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="menu-title">Manajemen User</li>

                @can('menu-user')
                    <li class="submenu">
                        <a href="javascript:void(0);"><img src="{{ asset('assets/assets/img/icons/users1.svg') }}"
                                alt="img"><span>
                                Users</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('users.index') }}"
                                    class="{{ request()->routeIs('users.*') ? 'active' : '' }}">Users List</a></li>
                            <li><a href="{{ route('roles.index') }}"
                                    class="{{ request()->routeIs('roles.*') ? 'active' : '' }}">Roles List</a></li>
                            <li><a href="{{ route('permissions.index') }}"
                                    class="{{ request()->routeIs('permissions.*') ? 'active' : '' }}">Permission</a></li>
                        </ul>
                    </li>
                @endcan
        </div>
    </div>
</div>
