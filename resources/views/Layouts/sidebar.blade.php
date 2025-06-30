<div id="sidebar">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    <a href="#">Admin</a>
                </div>
                <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                    <div class="form-check form-switch fs-6">
                        <input class="form-check-input  me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                        <label class="form-check-label"></label>
                    </div>
                </div>
                <div class="sidebar-toggler  x">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu</li>
                    <li class="sidebar-item has-sub">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-receipt"></i>
                            <span>Billing</span>
                        </a>
                        <ul class="submenu ">
                            <li class="submenu-item  ">
                                <a href="{{ url('billing/wapu') }}" class="submenu-link">Billing SBS</a>
                            </li>
                        </ul>
                    </li>
                {{-- <li class="sidebar-item  ">
                    <a href="{{ url('promo') }}" class='sidebar-link'>
                        <i class="bi bi-tag"></i>
                        <span>Promo</span>
                    </a>
                </li> --}}
                {{-- <li
                    class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Produk</span>
                    </a>
                    <ul class="submenu ">
                        <li class="submenu-item  ">
                            <a href="{{ url('ulasan') }}" class="submenu-link">Ulasan & Rating</a>
                        </li>
                    </ul>
                </li> --}}
            </ul>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
