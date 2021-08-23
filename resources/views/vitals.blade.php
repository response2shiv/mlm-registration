@extends('layout')
@section('title')
    ncrease - Vitals
@endsection
@section('back-link')
   {{ url('/enrollment/sponsor') }}
@endsection

@section('content')
    <section class="enrollment-content-wrapper">
        <div class="wrapper content-wrapper">
            <div class="row your-sponsor-info">
                <div class="col-12 your-sponsor-title">{!! __('lang.YOUR_SPONSOR') !!}</div>
                <div class="col-12 your-sponsor-name">{{ session('sponsor_name') }}</div>
                <div class="col-12">{{session('sponsor_city')}}, {{session('sponsor_state')}}</div>
                <div class="col-12">{{session('mobile_number')}}</div>
                <div class="col-12">{{session('sponsor_email')}}</div>
            </div>
            <div class="enrollment-title">{!! __('lang.LETS_GET_SOME_INFORMATION_TO_GET') !!}</div>
            {{--<div class="col-lg-7 col-12 errors-wrapper">
                @if($errors->has('country'))
                    <div class="small alert alert-danger">{{ $errors->first('country') }}</div>
                @endif
                @if($errors->has('language'))
                    <div class="small alert alert-danger">{{ $errors->first('language') }}</div>
                @endif
                @if($errors->has('first_name'))
                    <div class="small alert alert-danger">{{ $errors->first('first_name') }}</div>
                @endif
                @if($errors->has('last_name'))
                    <div class="small alert alert-danger">{{ $errors->first('last_name') }}</div>
                @endif
                @if($errors->has('email'))
                    <div class="small alert alert-danger">{{ $errors->first('email') }}</div>
                @endif
                @if($errors->has('mobile_number'))
                    <div class="small alert alert-danger">{{ $errors->first('mobile_number') }}</div>
                @endif
            </div>--}}
            <div class="row">
                <div class="col-lg-7 col-12 mx-auto">
                    <div class="col-lg-6 col-12 mx-auto">
                        @if (Session::has('message'))
                            <div class="small alert alert-danger">
                                {{ Session::get('message') }}
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="small alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <form class="vitals-form" method="POST" action="{{url('vitals')}}" autocomplete="off">
                        <div class="row">
                            {{--TODO: Change phone code and clear phone number when changing country--}}
                            <div class="col-12">
                                <div class="float-container select-wrapper">
                                    <label for="countryInput">{!! __('lang.COUNTRY_UCF') !!} *</label>
                                    <select id="countryInput" name="country">
                                        <option value="" {{(old('country') == ''?'selected':'')}} disabled>
                                            -- Select --
                                        </option>
                                        @foreach($countries as $country)
                                            <option value="{{$country->id}}" {{ old('country',session('vitals')['country']) == $country->id ? 'selected': ''}} >{{$country->country}}</option>
                                        @endforeach
                                    </select>
                                    <i class="fa fa-angle-down arrow-icon"></i>
                                </div>
                            </div>
                            {{--TODO: Add a list of languages--}}
                            <div class="col-12">
                                <div class="float-container select-wrapper">
                                    <label for="languageInput">{!! __('lang.LANG_LABEL') !!} *</label>
                                    <select id="languageInput" name="language">
                                        <option value="" {{(old('language') == ''?'selected':'')}} disabled>
                                            -- Select --
                                        </option>
                                        <option value="en" {{old('country',session('vitals')['language']) == "en" ?'selected': ''}}>English</option>
                                        <option value="fr" {{old('country',session('vitals')['language']) == "fr" ?'selected': ''}}>French</option>
                                        <option value="de" {{old('country',session('vitals')['language']) == "de" ?'selected': ''}}>German</option>
                                        <option value="it" {{old('country',session('vitals')['language']) == "it" ?'selected': ''}}>Italian</option>
                                        <option value="ko" {{old('country',session('vitals')['language']) == "ko" ?'selected': ''}}>Korean</option>
                                        <option value="pt" {{old('country',session('vitals')['language']) == "pt" ?'selected': ''}}>Portuguese</option>
                                        <option value="es" {{old('country',session('vitals')['language']) == "es" ?'selected': ''}}>Spanish</option>
                                        <option value="tr" {{old('country',session('vitals')['language']) == "tr" ?'selected': ''}}>Turkish</option>
                                    </select>
                                    <i class="fa fa-angle-down arrow-icon"></i>
                                </div>
                            </div>
                            <div class="col-6" style="padding-right: 0;">
                                <div class="float-container">
                                    <label for="firstNameInput">{!! __('lang.FIRST_NAME') !!} *</label>
                                    <input id="firstNameInput" name="firstname" type="text"
                                           value="{{old('firstname',session('vitals')['firstname']??'')}}"
                                           maxlength="255">
                                </div>
                            </div>
                            <div class="col-6" style="padding-left: 0;">
                                <div class="float-container" style="border-left: none;">
                                    <label for="lastNameInput">{!! __('lang.LAST_NAME') !!} *</label>
                                    <input id="lastNameInput" name="lastname" type="text"
                                           value="{{old('lastname',session('vitals')['lastname']??'')}}"
                                           maxlength="255">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="float-container">
                                    <label for="emailInput">{!! __('lang.EMAIL') !!} *</label>
                                    <input id="emailInput" name="email" type="email"
                                           value="{{old('email',session('vitals')['email']??'')}}"
                                           minlength="3" maxlength="255">
                                </div>
                            </div>
                            <div class="col-4" style="padding-right: 0;">
                                <div class="float-container last select-wrapper">
                                    <label for="countryCodeInput">{!! __('lang.COUNTRY_CODE') !!} *</label>
                                    <select id="countryCodeInput" name="country_code">
                                        <option value="" {{(old('country_code') == ''?'selected':'')}} disabled>
                                            ----
                                        </option>
                                        @foreach($dial_code as $d)
                                            <option value="{{$d}}" {{(old('country_code') == $d?'selected': (session('vitals')['country_code'] ==$d?'selected':'')  )}}>{{$d}}</option>
                                        @endforeach
                                    </select>
                                    <i class="fa fa-angle-down arrow-icon"></i>
                                </div>
                            </div>
                            {{--TODO: If a user used mobile number that already exists in the system, the system display the error message: This number is already in use. Please try a different one.--}}
                            <div class="col-8" style="padding-left: 0;">
                                <div class="float-container last" style="border-left: none;">
                                    <label for="telInput">{!! __('lang.MOBILE_NUMBER') !!} *</label>
                                    <input id="telInput" name="mobile_number" type="text"
                                           value="{{old('mobile_number',session('vitals')['mobile_number']??'')}}"
                                           oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mt-3 mb-3">
                                <div class="checkbox">
                                    <input type="checkbox"  name="updates_subscribe" class="form-check-input"
                                           id="updatesSubscribe" value="1">
                                    <label class="form-check-label" for="updatesSubscribe">{!! __('lang.I_AGREE_TO_RECEIVING_IMPORTANT_INFORMATION') !!}*</label>
                                </div>
                            </div>
                            <div class="col-12 mb-4 form-notes">
                                <p>
                                    *{!! __('lang.WE_WILL_NOT_SELL_SHARE_OR_GIVE_YOUR') !!} <a
                                            href="https://www.ncrease.com/index.php/privacy-policy/"
                                            target="_blank">{!! __('lang.HERE') !!}</a>.
                                </p>
                            </div>
                        </div>
                        <div class="form-field-button">
                            @csrf
                            <button id="vitals_start_btn" type="submit" class="input-btn">
                                {!! __('lang.START_YOUR_ENROLLMENT') !!}
                                <i id="loading" class="fa fa-spinner fa-spin" style="display: none"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    @if($show_2fa_modal == 1)
        <div class="modal fade" tabindex="-1" role="dialog" id="dlg2FA">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content text-center">
                    <div class="modal-header">
                        <h5 class="modal-title">Verification</h5>
                    </div>
                    <div class="modal-body">
                        <div id="frm2FA">
                            <div class="col-md-12">A 7 digit confirmation code has been sent via SMS / text to the
                                mobile number you provided. Please enter it here.
                            </div>
                            <div class="col-md-12 mt-2" style="display:none;" id="div2FAResendMsg">
                                <div class="alert alert-success">
                                </div>
                            </div>
                            <div class="col-md-12 mt-2" style="display:none;" id="div2FAError">
                                <div class="alert alert-danger">
                                </div>
                            </div>
                            <div class="col-md-6 offset-md-3 mt-3">
                                <input type="text" class="form-control" id="txt2FAcode" maxlength="7"
                                       oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                            </div>
                            <div class="col-md-12 mt-4">
                                <button type="button" id="btnSubmit2FA" class="btn btn-orange mb-2">Submit</button>
                                <button type="button" data-dismiss="modal" class="btn btn-grey mb-2">Cancel</button>
                            </div>
                            <div class="col-md-12 mt-3">
                                <span id="btnResend2FA" class="span-link">Resend My Code</span>
                            </div>
                        </div>
                        <div id="div2FAfailed" style="display:none;">
                            <div>The code you entered is not working. <br>Please contact our support team at</div>
                            <div class="support-link">
                                <a href="mailto:support@ncrease.com">support@ncrease.com</a>
                            </div>
                            <button type="button" data-dismiss="modal" class="btn btn-orange">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('custom-js')
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"
            integrity="sha384-B1miHxuCmAMGc0405Cm/zSXCc38EP5hNOy2bblqF6yiX01Svinm1mWMwJDdNDBGr"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.js"
            integrity="sha384-F1bF/FGKNfDfwYyFH6q7sJ50hMoqgL53b0p1HciOITyOzvuSSRzoBlPVe54KtRWB"
            crossorigin="anonymous"></script>
    <script>
        $(document).ready(function () {
            var _token = $('meta[name="csrf-token"]').attr('content');
            $("#countryInput").change(function () {
                var country_code = $(this).val();
                $.ajax({
                    url: '/phone-code',
                    type: 'POST',
                    data: '_token=' + _token + '&code=' + country_code,
                    success: function (data) {
                        if (data.error == 0) {
                            $("#countryCodeInput").val(data.select_code);
                        }
                    }
                });

            });
        })
    </script>
@endsection

