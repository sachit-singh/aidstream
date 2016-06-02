@extends('app')

@section('title', 'Settings')

@section('content')
    <div class="container main-container">
        <div class="row">
            @include('includes.side_bar_menu')
            <div class="col-xs-9 col-lg-9 content-wrapper settings-wrapper">
                @include('includes.response')
                <div class="alert alert-success hidden" id="response"></div>
                <div class="panel panel-default">
                    <div class="element-panel-heading">
                        <div>
                            Settings
                        </div>
                    </div>
                    @include('includes.settings_menu')
                    <div class="panel panel-default panel-element-detail element-show">
                        @yield('panel-body')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
