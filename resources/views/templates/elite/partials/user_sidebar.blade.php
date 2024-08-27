<div class="sidebar-menu">
    <span class="sidebar-menu__close d-lg-none d-block"><i class="las la-times"></i></span>
    <ul class="sidebar-menu-list">
        <li class="sidebar-menu-list__item {{ menuActive('user.home') }}">
            <a class="sidebar-menu-list__link" href="{{ route('user.home') }}">
                <span class="icon"><i class="las la-home"></i></span>
                <span class="text">@lang('Dashboard')</span>
            </a>
        </li>
        <li class="sidebar-menu-list__item {{ menuActive('user.profile.setting') }}">
            <a class="sidebar-menu-list__link" href="{{ route('user.profile.setting') }}">
                <span class="icon"><i class="las la-user-cog"></i></span>
                <span class="text">@lang('Account Setting')</span>
            </a>
        </li>
        <li class="sidebar-menu-list__item {{ menuActive('user.referral.users') }}">
            <a class="sidebar-menu-list__link" href="{{ route('user.referral.users') }}">
                <span class="icon"><i class="las la-tree"></i></span>
                <span class="text">@lang('Referral')</span>
            </a>
        </li>
        <li class="sidebar-menu-list__item {{ menuActive('user.advertisement.index') }}">
            <a class="sidebar-menu-list__link" href="{{ route('user.advertisement.index') }}">
                <span class="icon"><i class="lab la-adversal"></i></span>
                <span class="text">@lang('Advertisements')</span>
            </a>
        </li>
        <li class="sidebar-menu-list__item {{ menuActive('user.withdraw.history') }}">
            <a class="sidebar-menu-list__link" href="{{ route('user.withdraw.history') }}">
                <span class="icon"><i class="las la-hand-holding-usd"></i></span>
                <span class="text">@lang('Withdrawals History')</span>
            </a>
        </li>
        <li class="sidebar-menu-list__item {{ menuActive('user.deposit.history') }}">
            <a class="sidebar-menu-list__link" href="{{ route('user.deposit.history') }}">
                <span class="icon"><i class="las la-wallet"></i></span>
                <span class="text">@lang('Deposits History')</span>
            </a>
        </li>
        <li class="sidebar-menu-list__item {{ menuActive('user.transactions') }}">
            <a class="sidebar-menu-list__link" href="{{ route('user.transactions') }}">
                <span class="icon"><i class="las la-money-bill"></i></span>
                <span class="text">@lang('Transactions')</span>
            </a>
        </li>
        <li class="sidebar-menu-list__item {{ menuActive('ticket.index') }}">
            <a class="sidebar-menu-list__link" href="{{ route('ticket.index') }}">
                <span class="icon"><i class="la la-ticket-alt"></i></span>
                <span class="text">@lang('Support Tickets')</span>
            </a>
        </li>
        <li class="sidebar-menu-list__item {{ menuActive('user.twofactor') }}">
            <a class="sidebar-menu-list__link" href="{{ route('user.twofactor') }}">
                <span class="icon"><i class="las la-lock"></i></span>
                <span class="text">@lang('2FA Security')</span>
            </a>
        </li>
        <li class="sidebar-menu-list__item {{ menuActive('user.change.password') }}">
            <a class="sidebar-menu-list__link" href="{{ route('user.change.password') }}">
                <span class="icon"><i class="la la-key"></i></span>
                <span class="text">@lang('Change Password')</span>
            </a>
        </li>
        <li class="sidebar-menu-list__item">
            <a class="sidebar-menu-list__link" href="{{ route('user.logout') }}">
                <span class="icon"><i class="la la-sign-out-alt"></i></span>
                <span class="text">@lang('Logout') </span>
            </a>
        </li>
    </ul>
</div>
