@extends('layout')
@section('title')
    ncrease - {!! __('lang.SPONSOR_SEARCH') !!}
@endsection
@section('back-link')
   {{ url('/') }}
@endsection

@section('content')
    <section class="enrollment-content-wrapper">
        <div class="wrapper content-wrapper">
            <div class="find-sponsor">
                <div class="enrollment-title">{!! __('lang.CONGRATULATIONS_ON_JOINING') !!} <br><span class="search-title">{!! __('lang.ENTER_YOUR_SPONSORS_NAME_BELOW') !!}</span><span class="select-title">{!! __('lang.SELECT_YOUR_SPONSOR_BELOW') !!}</span></div>
                <div class="two-column">
                    <div class="layout-column">
                        <div class="column-accr">
                            <form id="search">
                                <div class="tab-panel">
                                    <div class="accr-head active tab1" data-id="#tab-01">
                                        <h4>{!! __('lang.SEARCH_BY_NAME') !!}</h4>
                                    </div>
                                    <div id="tab-01" class="tab-content" style="display: block">
                                        <div class="row">
                                            <div class="col-6" style="padding-right: 0;">
                                                <div class="float-container">
                                                    <label for="firstNameInput">{!! __('lang.FIRST_NAME') !!} *</label>
                                                    <input id="firstNameInput" name="first_name" type="text" maxlength="255" required>
                                                </div>
                                            </div>
                                            <div class="col-6" style="padding-left: 0;">
                                                <div class="float-container" style="border-left: none;">
                                                    <label for="lastNameInput">{!! __('lang.LAST_NAME') !!} *</label>
                                                    <input id="lastNameInput" name="last_name" type="text" maxlength="255" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-panel">
                                    <div class="accr-head tab2" data-id="#tab-02">
                                        <h4>{!! __('lang.SEARCH_BY_USERNAME') !!}</h4>
                                    </div>
                                    <div id="tab-02" class="tab-content">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="float-container">
                                                    <label for="UserNameInput">{!! __('lang.USERNAME') !!} *</label>
                                                    <input id="UserNameInput" name="username" type="text" maxlength="255">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-field-button">
                                    <input type="hidden" name="action" value="searchByName">
                                    @csrf
                                    <button id="searchBtn" name="" class="input-btn" value="Find My Sponsor">
                                        <i class="fa fa-search"></i>
                                        {!! __('lang.FIND_MY_SPONSOR') !!}
                                        <i id="loading" class="fa fa-spinner fa-spin" style="display: none"></i>
                                    </button>
                                </div>
                            </form>
                            <div class="search-results">
                                <div class="select-sponsor-title">
                                    <h4>{!! __('lang.SELECT_YOUR_SPONSOR') !!}</h4>
                                </div>
                                <div class="sponsors-list"></div>
                                <div class="form-field-button">
                                    <button class="input-btn new-search-btn" value="Start new search">
                                        <i class="fa fa-search"></i>
                                        {!! __('lang.START_NEW_SEARCH') !!}
                                        <i id="loading" class="fa fa-spinner fa-spin" style="display: none"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="no-sponsors-found">
                                <div class="card card--tight-top card--stroked">
                                    <h2 class="mb-2">{!! __('lang.NO_SPONSORS_FOUND') !!}.</h2>
                                    <p class="note">{!! __('lang.SORRY_WE_COULDNT_FIND_ANY_RESULTS') !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layout-column">
                        <div class="card">
                            <p>
                                {!! __('lang.IF_YOU_HAVE_QUESTIONS_PLEASE_CONTACT_OUR') !!} <a href="mailto:support@ncrease.com">support@ncrease.com</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
