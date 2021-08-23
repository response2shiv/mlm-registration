var js_myjs = function () {

    var burl = $('#burl').text();
    var _tok = $('meta[name="csrf-token"]').attr('content');

    var h_2fa = function () {
        if ($('#dlg2FA').length) {
            $('#dlg2FA').modal('show');

            $('#btnSubmit2FA').click(function () {
                $('#div2FAError').hide();
                $('#div2FAResendMsg').hide();
                $(this).html('<i class="fa fa-spinner fa-spin"></i> Wait').prop('disabled', true);
                ajPost('c=' + $('#txt2FAcode').val(), '/sub-tfa', 'sub-tfa');
            });

            $('#btnResend2FA').click(function () {
                $('#div2FAError').hide();
                $('#div2FAResendMsg').hide();
                $(this).html('<i class="fa fa-spinner fa-spin"></i> Wait').prop('disabled', true);
                ajPost('', '/resend-tfa', 'resend-tfa');
            });
        }
    };

    function ajPost(da, u, ptt) {
        var rd;

        $.ajax({
            type: "POST", url: burl + u, data: da + "&_token=" + _tok, dataType: "JSON", cache: false,
            success: function (d) {
                if (d['error'] == 1) {
                    rd = d;
                    afterError(ptt, d);
                } else if (d['error'] == 0) {
                    rd = d;
                    afterSuccess(ptt, d);
                }
            },
            complete: function () {
                afterCom(ptt, rd);
            }
        });

        function afterError(ptt, d) {
        }

        function afterSuccess(ptt, d) {
        }

        function afterCom(ptt, rd) {
            if (rd['error'] == 0) {
                if ('url' in rd) {
                    if (rd['url'] == 'reload')
                        window.location.reload();
                    else {
                        if ('target_blank' in rd) {
                            window.open(rd['url']);
                        } else {
                            window.location.replace(rd['url']);
                        }
                    }
                }
            }

            if (ptt == 'sub-tfa') {
                if (rd['error'] == 1) {
                    $('#div2FAError').find('.alert').text(rd['msg']);
                    $('#div2FAError').show();

                    if (rd['failed_count'] >= 3) {
                        $('#frm2FA').hide();
                        $('#div2FAfailed').show();
                    }
                }

                $('#btnSubmit2FA').html('Submit').prop('disabled', false);
            } else if (ptt == "resend-tfa") {
                if (rd['error'] == 1) {
                    $('#div2FAError').find('.alert').text(rd['msg']);
                    $('#div2FAError').show();

                    if (rd['resent_count'] >= 3) {
                        $('#frm2FA').hide();
                        $('#div2FAfailed').show();
                    }
                } else {
                    $('#div2FAResendMsg').find('.alert').text(rd['msg']);
                    $('#div2FAResendMsg').show();
                }

                $('#btnResend2FA').html('Resend My Code').prop('disabled', false);
            }
        }
    }

    jQuery(document).ready(function () {
        var $username = $("[name='username']");
        var $firstName = $("[name='first_name']");
        var $lastName = $("[name='last_name']");
        var $search = $("#search");
        var $searchResults = $(".search-results");
        var $noSponsorsFound = $(".no-sponsors-found");
        var $sponsorsList = $('.sponsors-list');
        var $floatField = $('.float-container input');
        var $searchTitle = $('.enrollment-title .search-title');
        var $selectTitle = $('.enrollment-title .select-title');

        $floatField.on('focus', function(e) {
            $(e.target).parents('.float-container').addClass('active');
        });

        $floatField.on('blur', function(e) {
            var value = e.target.value;

            if (value) {
                $(e.target).parents('.float-container').addClass('active');
            } else {
                $(e.target).parents('.float-container').removeClass('active');
            }
        });

        $floatField.blur();

        $('.accr-head').click(function() {
            var $action = $("[name='action']");

            if (!$(this).hasClass('active')) {
                $('.accr-head').removeClass('active');
                $(this).addClass('active');
                $('.tab-content').hide();
                var id = $(this).attr('data-id');
                $(id).slideDown();

                if ($(this).hasClass('tab1')) {
                    $username.prop("required", false).val('');
                    $firstName.prop("required", true);
                    $lastName.prop("required", true);
                    $action.val("searchByName");
                }

                if ($(this).hasClass('tab2')) {
                    $username.prop("required", true);
                    $firstName.prop("required", false).val('');
                    $lastName.prop("required", false).val('');
                    $action.val("searchByUserName");
                }

                $noSponsorsFound.hide();
                $searchResults.hide();
            }
        });

        $('.new-search-btn').click(function() {
            $sponsorsList.empty();
            $username.val('');
            $firstName.val('');
            $lastName.val('');
            $searchResults.hide();
            $search.show();
            $searchTitle.show();
            $selectTitle.hide();
            $('.float-container input').blur();
        });

        $('.add-co-applicant').click(function() {
            $('.add-co-applicant').toggleClass('open');
            $('.co-applicant-info').toggleClass('open');
            $('.co-applicant-info input, .co-applicant-info select').prop('disabled', function(i, v) { return !v; });
        });

        $('.info-form input[name=is_billing_same]').on('change', function(e) {
            var isSameAsPrimary = e.target.value === 'yes';

            $('.info-form .billing-address').toggleClass('open', !isSameAsPrimary);
            $('.info-form .billing-address input').prop('disabled', function(i, v) { return !v; });
        });

        $('.info-form input[name=is_shipping_same]').on('change', function(e) {
            var isSameAsPrimary = e.target.value === 'yes';

            $('.info-form .shipping-address').toggleClass('open', !isSameAsPrimary);
            $('.info-form .shipping-address input').prop('disabled', function(i, v) { return !v; });
        });

        $('.info-form input[name=tax_information]').on('change', function(e) {
            var isBisuness = e.target.value === 'business';

            $('.info-form .tax-info-wrapper').toggleClass('open', isBisuness);
            $('.info-form .tax-info-wrapper input').prop('disabled', function(i, v) { return !v; });
        });

        $('.info-form #UserNameInput').on('change', function() {
            var username = $(this).val();
            $('.username-url').text(username);
        });

        $(".payment-method-select").msDropdown();

        $('#PaymentTypeInput').on('change', function() {
            toggleCreditCardFields($(this).val());
        });

        if ($('#PaymentTypeInput').length) {
            toggleCreditCardFields($('#PaymentTypeInput').val());
        }

        function toggleCreditCardFields(value) {
            // TODO: Check all credit cards id
            if (value == 1) {
            } else if (value == 9) {
                var isCreditCard = value == 1;
            }
            // var isCreditCard = value == 1;

            $('.credit-card-info-wrap').toggle(isCreditCard);
            $('.credit-card-info-wrap input').prop('disabled', !isCreditCard);
        }

        $search.validate({
            errorPlacement: function () {
                return false;
            }, submitHandler: function () {
                $('#loading').show();
                $noSponsorsFound.hide();

                $.ajax({
                    url: "sponsor/search",
                    type: "POST",
                    data: $search.serialize(),
                    cache: false,
                    processData: false,
                    success: function (response) {
                        $('#loading').hide();

                        if (response.status !== 1) return;

                        if (response.count === 0) {
                            $noSponsorsFound.show();
                        } else {
                            $searchResults.show();
                            $search.hide();
                            $searchTitle.hide();
                            $selectTitle.show();

                            response.sponsors.forEach(function(sponsor) {
                                var styles = sponsor.avatarUrl ? "background-image: url(" + sponsor.avatarUrl + ");" : "";

                                $sponsorsList.append(
                                    '<a href="' +sponsor.url + '" class="sponsor-wrapper">' +
                                        '<div class="d-flex"><div class="sponsor-avatar" style="' + styles + '"></div>' +
                                        '<div><div class="sponsor-name">' + sponsor.firstname + ' ' + sponsor.lastname + '</div><div class="sponsor-address">' + sponsor.city + ', ' + sponsor.stateabbr + ', ' + sponsor.countrycode + '</div></div></div>' +
                                        '<div class="sponsor-select-btn">SELECT</div><i class="fa fa-angle-right" aria-hidden="true"></i>' +
                                    '</a>'
                                )
                            });
                        }

                        $(".fa-spinner").hide();
                    }
                });
                return false;
            }
        });
    });

    return {
        init: function () {
            h_2fa();
        }
    };
}();
