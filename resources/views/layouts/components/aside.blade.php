<div class="aside aside-left  aside-fixed  d-flex flex-column flex-row-auto"  id="kt_aside">
    <div class="brand flex-column-auto " id="kt_brand">
		<a href="{{ url('admin') }}" class="brand-logo">
            <img alt="Logo" src="{{ asset('assets/media/logo.png') }}" class="max-h-75px" style="margin-left: -16px"/>
        </a>
    </div>

    <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
        <div id="kt_aside_menu" class="aside-menu my-4 " data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500" >
            <ul class="menu-nav ">
                <li class="menu-item {{ request()->is('admin') ? 'menu-item-active' : '' }}"  aria-haspopup="true">
                    <a  href="{{ url('admin') }}" class="menu-link ">
                        <span class="menu-icon la la-dashboard icon-xl"></span>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>

                <li class="menu-section ">
                    <h4 class="menu-text">Tipster Section</h4>
                    <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
                </li>

                <li class="menu-item {{ (request()->is('admin/tipster/user-balances') || request()->is('admin/tipster/user-balances/*')) ? 'menu-item-active' : '' }}"  aria-haspopup="true">
                    <a  href="{{ url('admin/tipster/user-balances') }}" class="menu-link ">
                        <span class="menu-icon la la-balance-scale-left icon-xl"></span>
                        <span class="menu-text">User Balance</span>
                    </a>
                </li>

                 <li class="menu-item {{ (request()->is('admin/tipster/match-bet') || request()->is('admin/tipster/maatch-bet/*')) ? 'menu-item-active' : '' }}"  aria-haspopup="true">
                    <a  href="{{ url('admin/tipster/match-bet') }}" class="menu-link ">
                        <span class="menu-icon la la-money-bill-wave-alt icon-xl"></span>
                        <span class="menu-text">Match Bet</span>
                    </a>
                </li>

                <li class="menu-item {{ (request()->is('admin/tipster/season') || request()->is('admin/tipster/season/*')) ? 'menu-item-active' : '' }}"  aria-haspopup="true">
                    <a  href="{{ url('admin/tipster/season') }}" class="menu-link ">
                        <span class="menu-icon la la-history icon-xl"></span>
                        <span class="menu-text">Season</span>
                    </a>
                </li>

                <li class="menu-item {{ (request()->is('admin/tipster/transaction') || request()->is('admin/tipster/transaction/*')) ? 'menu-item-active' : '' }}"  aria-haspopup="true">
                    <a  href="{{ url('admin/tipster/transaction') }}" class="menu-link ">
                        <span class="menu-icon la la-money-bill icon-xl"></span>
                        <span class="menu-text">Transaction</span>
                    </a>
                </li>

                <li class="menu-item {{ (request()->is('admin/tipster/transaction-cancel') || request()->is('admin/tipster/transaction-cancel/*')) ? 'menu-item-active' : '' }}"  aria-haspopup="true">
                    <a  href="{{ url('admin/tipster/transaction-cancel') }}" class="menu-link ">
                        <span class="menu-icon la la-window-close icon-xl"></span>
                        <span class="menu-text">Transaction Cancel</span>
                    </a>
                </li>

                <li class="menu-section ">
                    <h4 class="menu-text">Settings</h4>
                    <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
                </li>

                <li class="menu-item {{ (request()->is('admin/setting/users') || request()->is('admin/setting/users/*')) ? 'menu-item-active' : '' }}"  aria-haspopup="true">
                    <a  href="{{ url('admin/setting/users') }}" class="menu-link ">
                        <span class="menu-icon la la-user icon-xl"></span>
                        <span class="menu-text">Users</span>
                    </a>
                </li>

                <li class="menu-item {{ (request()->is('admin/setting/config') || request()->is('admin/setting/config/*')) ? 'menu-item-active' : '' }}"  aria-haspopup="true">
                    <a  href="{{ url('admin/setting/config') }}" class="menu-link ">
                        <span class="menu-icon la la-gears icon-xl"></span>
                        <span class="menu-text">Config</span>
                    </a>
                </li>


                <li class="menu-item {{ (request()->is('admin/setting/static-content') || request()->is('admin/setting/static-content/*')) ? 'menu-item-active' : '' }}"  aria-haspopup="true">
                    <a  href="{{ url('admin/setting/static-content') }}" class="menu-link ">
                        <span class="menu-icon la la-newspaper icon-xl"></span>
                        <span class="menu-text">Static Content</span>
                    </a>
                </li>


                <li class="menu-item {{ (request()->is('admin/setting/upcoming-footbal-match') || request()->is('admin/tipster/maatch-bet/*')) ? 'menu-item-active' : '' }}"  aria-haspopup="true">
                    <a  href="{{ url('admin/setting/upcoming-football-match') }}" class="menu-link ">
                        <span class="menu-icon la la-list-ul icon-xl"></span>
                        <span class="menu-text">Upcoming Football Match</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
