<!-- WordPress top header -->
    <div class="wordpress-top-header">
        <a href="{{ url('../../../../wp-admin') }}"><i class="icon-wordpress"></i> WordPress</a>
        <div class="right-bar pull-right">
            <span class="user-group">
                <a href="{{ get_edit_user_link() }}" class="avatar">
                    {{ wp_get_current_user()->user_login }} {!! get_avatar(get_current_user_id(), 18) !!}
                </a>
                <div class="more_user_box">
                    <div class="row">
                        <div class="col-md-4">
                            {!! get_avatar(wp_get_current_user()->email, 80) !!}
                        </div>
                        <div class="col-md-8">
                            <ul>
                                <li>
                                    <a href="{{ get_edit_user_link() }}" class="avatar">
                                        {{ wp_get_current_user()->user_login }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ get_edit_user_link() }}" class="avatar">
                                        {{ __( 'Edit My Profile' ) }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ wp_logout_url() }}" class="avatar">
                                        {{ __( 'Log Out' ) }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </span>
        </div>
    </div>
<!-- /wordPress top header -->