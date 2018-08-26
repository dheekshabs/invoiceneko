@extends("layouts/default")

@section("head")
    <title>{{ config('app.name') }} | Account | Reset Password</title>
    <style>
    </style>
@stop

@section("content")
    <div class="wrap v-center">
        <div class="container content-main-authentication">
            <div class="login-container z-depth-1">
                <div id="output"></div>
                <div class="avatar">
                    <img src="{{ asset('assets/img/avatar.png') }}" width="150" height="150">
                </div>
                <hr><br>
                <div class="form-box">
                    <form id="reset" method="post">
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="email" name="email" type="email" data-parsley-required="true" data-parsley-trigger="change">
                                <label for="email" class="label-validation">Email</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="password" name="password" type="password" data-parsley-required="true" data-parsley-trigger="change">
                                <label for="password" class="label-validation">Password</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="password_confirmation" name="password_confirmation" type="password" data-parsley-required="true" data-parsley-trigger="change">
                                <label for="password" class="label-validation">Password Confirmation</label>
                            </div>
                        </div>
                        <input type="hidden" name="token" value="{{ $token }}">
                        {{ csrf_field() }}
                        <button class="btn btn-theme waves-effect waves-light full-width" type="submit" name="action">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section("scripts")
    <script>
        "use strict";
        $(function() {
            $('#reset').parsley({
                successClass: 'valid',
                errorClass: 'invalid',
                errorsContainer: function (velem) {
                    var $errelem = velem.$element.siblings('label');
                    $errelem.attr('data-error', window.Parsley.getErrorMessage(velem.validationResult[0].assert));
                    return true;
                },
                errorsWrapper: '',
                errorTemplate: ''
            })
                .on('field:validated', function(velem) {

                })
                .on('field:success', function(velem) {

                })
                .on('field:error', function(velem) {

                });
        });
    </script>
@stop