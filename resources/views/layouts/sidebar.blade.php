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
                             @can('master-data-mekanik')
                                 <li>
                                     <a href="{{ route('mechanics.index') }}"
                                         class="{{ request()->routeIs('mechanics.*') ? 'active' : '' }}">Mekanik</a>
                                 </li>
                             @endcan
                         </ul>
                     </li>
                 @endcan
                 @can('menu-client')
                     <li class="{{ request()->routeIs('client.*') ? 'active' : '' }}">
                         <a href="{{ route('client.index') }}"><img
                                 src="{{ asset('assets/assets/img/icons/users1.svg') }}" alt="img"><span>
                                 Clients</span> </a>
                     </li>
                 @endcan
                 @can('menu-kendaraan')
                     <li class="{{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                         <a href="{{ route('vehicles.index') }}"><i class="fa fa-car"></i><span>
                                 Kendaraan Client</span> </a>
                     </li>
                 @endcan
                 @can('menu-service')
                     <li class="submenu">
                         <a href="javascript:void(0);"><i class="fa-solid fa-car-side"></i><span>
                                 Service</span> <span class="menu-arrow"></span></a>
                         <ul>
                            <li>
                                <a href="{{ route('services.fastServices') }}" class="{{ request()->routeIs('services.fastServices') ? 'active' : '' }}">Tambah Data Service</a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('services.create') }}" class="{{ request()->routeIs('services.create') ? 'active' : '' }}">Tambah Data Service</a>
                            </li> --}}
                            </li>
                               <li>
                                <a href="{{ route('services.index') }}" class="{{ request()->routeIs('services.index') ? 'active' : '' }}">Data Service</a>
                            </li>
                             <li>
                                <a href="{{ route('oil_services.index') }}" class="{{ request()->routeIs('oil_services.index') ? 'active' : '' }}">Kartu Service Oli</a>
                            </li>
                         </ul>
                     </li>
                 @endcan
                 @can('menu-penjualan')
                     {{-- <li class="{{ request()->routeIs('sales.*') ? 'active' : '' }}">
                         <a href="{{ route('sales.index') }}"><i class="fa-solid fa-cart-plus"></i><span>
                                 Penjualan Barang</span> </a>
                     </li> --}}
                     <li class="submenu">
                         <a href="javascript:void(0);"><i class="fa-solid fa-cart-plus"></i><span>
                                 Penjualan Barang</span> <span class="menu-arrow"></span></a>
                         <ul>
                                 <li>
                                <a href="{{ route('sales.create') }}" class="{{ request()->routeIs('sales.create') ? 'active' : '' }}">Tambah Data Penjualan</a>
                            </li>
                               <li>
                                <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.index') ? 'active' : '' }}">Data Penjualan</a>
                            </li>
                         </ul>
                     </li>
                 @endcan
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
                 @can('menu-laporan')
                     {{-- <li class="submenu">
                         <a href="javascript:void(0);"><i class="fa-solid fa-book"></i><span>
                                 Laporan Service</span> <span class="menu-arrow"></span></a>
                         <ul>

                             <li><a href="{{ route('laporan.service') }}"
                                     class="{{ request()->routeIs('laporan.service') ? 'active' : '' }}">Service</a></li>
                         </ul>
                     </li> --}}
                     {{-- <li class="submenu">
                         <a href="javascript:void(0);"><i class="fa-solid fa-book"></i><span>
                                 report</span> <span class="menu-arrow"></span></a>
                         <ul>
                             <li><a href="{{ route('laporan.index') }}"
                                     class="{{ request()->routeIs('laporan.index') ? 'active' : '' }}">Laporan</a></li>
                             <li><a href="{{ route('services.report.service') }}"
                                     class="{{ request()->routeIs('services.report.service') ? 'active' : '' }}">Laporan
                                     Service</a></li>
                             <li><a href="{{ route('services.report.jobs') }}"
                                     class="{{ request()->routeIs('services.report.jobs') ? 'active' : '' }}">Laporan
                                     Pekerjaan</a></li>

                             <li><a href="{{ route('services.report.mechanics') }}"
                                     class="{{ request()->routeIs('services.report.mechanics') ? 'active' : '' }}">Laporan
                                     Mekanik</a>
                             </li>
                         </ul>
                     </li> --}}
                     <li class="submenu">
                         <a href="javascript:void(0);"><i class="fa-solid fa-book"></i><span>
                                 report</span> <span class="menu-arrow"></span></a>
                         <ul>
                             <li><a href="{{ route('reports.service') }}"
                                     class="{{ request()->routeIs('reports.service') ? 'active' : '' }}">
                                     Report service</a>
                             </li>
                             <li><a href="{{ route('reports.sale') }}"
                                     class="{{ request()->routeIs('reports.sale') ? 'active' : '' }}">Report penjualan</a>
                             </li>
                             <li><a href="{{ route('reports.Gabungan') }}"
                                     class="{{ request()->routeIs('reports.Gabungan') ? 'active' : '' }}">Report
                                     Gabungan</a>
                             </li>
                             <li><a href="{{ route('pembayaran-service.index') }}"
                                     class="{{ request()->routeIs('pembayaran-service.index') ? 'active' : '' }}">pembayaran
                                     service</a>
                             </li>
                             <li><a href="{{ route('sales-payments.index') }}"
                                     class="{{ request()->routeIs('sales-payments.index') ? 'active' : '' }}">pembayaran
                                     penjualan barang</a>
                             </li>
                             <li><a href="{{ route('services.report.spareparts') }}"
                                     class="{{ request()->routeIs('services.report.spareparts') ? 'active' : '' }}">
                                     Spareparts terpakai service</a>
                             </li>
                             <li><a href="{{ route('reports.sold-items') }}"
                                     class="{{ request()->routeIs('reports.sold-items') ? 'active' : '' }}">
                                     Report Penjualan Barang</a>
                             </li>
                             <li><a href="{{ route('reports.mekanik') }}"
                                     class="{{ request()->routeIs('reports.mekanik') ? 'active' : '' }}">
                                     Report Mekanik</a>
                             </li>
                             <li><a href="{{ route('laporan.kasir.index') }}"
                                     class="{{ request()->routeIs('laporan.kasir.index') ? 'active' : '' }}">
                                     Report kasir</a>
                             </li>
                         </ul>
                     </li>
                 @endcan
                 {{-- <li class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                         <a href="{{ route('products.index') }}"><img
                                 src="{{ asset('assets/assets/img/icons/product.svg') }}" alt="img"><span>Data
                                 Product</span></a>
                     </li> --}}
                 {{-- <li class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
                         <a href="{{ route('customers.index') }}"><img
                                 src="{{ asset('assets/assets/img/icons/users1.svg') }}" alt="img"><span>
                                 Customer</span> </a>
                     </li> --}}
                 {{-- @can('data-promo')
                 <li class="{{ request()->routeIs('tampilanPromo') ? 'active' : '' }}">
                    <a href="{{ route('tampilanPromo') }}"><i data-feather="layers"></i><span>Setting Promo</span></a>
                </li>
                <li class="{{ request()->routeIs('barang.*') ? 'active' : '' }}">
                         <a href="{{ route('barang.index') }}"><img
                                 src="{{ asset('assets/assets/img/icons/product.svg') }}" alt="img"><span>Data
                                 Barang</span></a>
                     </li>
                @endcan --}}



                 {{-- <li class="submenu">

                     <a href="javascript:void(0);"><img
                             src="{{ asset('assets/assets/img/icons/sales1.svg') }}" alt="img"><span>
                             Transaksi</span> <span class="menu-arrow"></span></a>
                     <ul>
                         @can('data-transaction')
                         <li><a href="{{ route('transactions.daftarTransaksi') }}" class="{{ request()->routeIs('transactions.daftarTransaksi') ? 'active' : '' }}">Daftar Transaksi</a></li>
                          @endcan
                           @can('data-pos')
                         <li><a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.index') ? 'active' : '' }}">POS</a></li>
                          @endcan
                     </ul>
                 </li> --}}
                 {{-- @can('data-report')
                  <li class="{{ request()->routeIs('reports.omzet') ? 'active' : '' }}">
                    <a href="{{ route('reports.omzet') }}"><img
                             src="{{ asset('assets/assets/img/icons/purchase1.svg') }}" alt="img"><span>
                             Laporan</span> </a>
                </li>
                 @endcan --}}

             </ul>
         </div>
     </div>
 </div>
