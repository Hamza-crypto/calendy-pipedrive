
<nav id="sidebar" class="sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="/">
            <span class="align-middle me-3">{{ env("APP_NAME") }}</span>
        </a>

        <ul class="sidebar-nav">
            <li class="sidebar-header">
                General
            </li>
            <li class="sidebar-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <a class="sidebar-link" href="#">
                    <i class="align-middle" data-feather="sliders"></i>
                    <span class="align-middle">Tasks</span>
                </a>
            </li>

        </ul>
    </div>
</nav>
