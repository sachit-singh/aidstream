var Registration = Registration || {};

function slash(value) {
    return value.replace(/([\[\]])/g, '\\$1');
}

(function ($) {

    Registration = {
        // ajax request handler
        request: function (url, data, callback, type) {
            type = type || 'POST';
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('[name="_token"]').val()},
                type: type,
                url: url,
                data: data,
                success: function (data) {
                    if (typeof callback == 'function') callback(data);
                }
            });
        },
        // auto generates abbreviation from organization name
        abbrGenerator: function () {
            var ignoreList = ['and', 'of', 'the', 'an', 'a'];

            function getWordList(name) {
                var nameArray = name.split(/\ +/g);
                return nameArray.filter(function (value) {
                    return $.inArray(value.toLowerCase(), ignoreList) === -1;
                })
            }

            function getAbbr(wordList) {
                var abbr = '';
                for (var i in wordList) {
                    var word = wordList[i];
                    abbr += word.substr(0, 1);
                }
                return abbr.toLowerCase();
            }

            $('.organization_name').change(function () {
                if ($.trim($('.organization_name_abbr').val()) != "") {
                    return false;
                }
                var name = $(this).val();
                var wordList = getWordList(name);
                var abbr = getAbbr(wordList);
                $('.organization_name_abbr').val(abbr).trigger('keydown').trigger('change').valid();
            });
        },
        // checks availability for abbreviation to be used
        checkAbbrAvailability: function () {
            var checkElem = $('.organization_name_abbr');
            checkElem.on('change', checkAvailability);
            checkElem.on('keydown', function () {
                checkElem.siblings('.availability-check').html('').addClass('hidden').removeClass('text-success text-danger');
            });
            function checkAvailability() {
                var userIdentifier = $(this).val();
                if ($.trim(userIdentifier) == "") {
                    return false;
                }
                var callback = function (data) {
                    checkElem.siblings('.availability-check').removeClass('hidden').addClass('text-' + data.status).html(data.message);
                    $(this).parents('.has-error').removeClass('has-error');
                    $(this).siblings('.text-danger').remove();
                };
                Registration.request("/check-organization-user-identifier", {userIdentifier: $(this).val()}, callback);
            }
        },
        // filters registration agencies on country change
        changeCountry: function () {
            $('.country').change(function () {
                Registration.filterAgency($(this).val());
            });
            $('.country').trigger('change');
        },
        // filters registration agencies
        filterAgency: function (country) {
            var filteredAgencies = '<option value="" selected="selected">Select an Agency</option>';
            var selected = '';
            for (var i in agencies) {
                if (i.indexOf(country) == 0 || i.indexOf('XI') == 0 || i.indexOf('XM') == 0) {
                    filteredAgencies += '<option value="' + i + '">' + agencies[i] + '</option>';
                    if (i == $('.agencies').attr('data-agency')) {
                        selected = i;
                    }
                }
            }
            $('.organization_registration_agency').html(filteredAgencies).val(selected);
        },
        // generates organization identifier with registration number
        regNumber: function () {
            $('.country, .organization_registration_agency, .registration_number').on('keyup change', function () {
                var identifier = '';
                var value = '';
                if ($('.country').val() == '' || $('.organization_registration_agency').val() == '' || $('.registration_number').val() == '') {
                    identifier = '[Registration Agency]-[Registration Number]';
                    value = "";
                } else {
                    identifier = value = $('.organization_registration_agency').val() + '-' + $('.registration_number').val();
                }

                $('#org_identifier').html(identifier);
                $('.organization_identifier').val(value);
            });
            $('.registration_number').trigger('change');
        },
        // generates username
        usernameGenerator: function () {
            $('.user-blocks').delegate('.username', 'change keyup', function (e) {
                var userIdentifier = $('.organization_name_abbr').val() + '_';
                var username = $(this).val();
                if (userIdentifier.indexOf(username) == 0 && username.length <= userIdentifier.length) {
                    username = "";
                } else if (username.indexOf(userIdentifier) != 0) {
                    username = userIdentifier + username;
                }
                $(this).siblings('.login_username').val(username);
            });
        },
        // adds more user block
        addUser: function () {
            $('#add-user').click(function () {
                var index = 0;
                if ($('.user-blocks .user-block').length > 0) {
                    var name = $('.user-blocks .user-block:last-child .form-control:first').attr('name');
                    index = parseInt(name.match(/[\d]+/g)) + 1;
                }
                var template = $('#user_template').clone();
                var html = template.html();
                html = html.replace(/_index_/g, index);
                $('.user-blocks').append(html);
                $(this).html('Add Another User');
                // Registration.disableUsersSubmitButton();
                Registration.usersValidationRules(index);
            });
        },
        // removes user block
        removeUser: function () {
            $('.user-blocks').delegate('.delete', 'click', function (e) {
                e.preventDefault();
                var _this = $(this);

                if ($('#removeDialog').length === 0) {
                    $('body').append('' +
                        '<div class="modal" id="removeDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index: 9999">' +
                        '<div class="modal-dialog">' +
                        '<div class="modal-content">' +
                        '<div class="modal-header">' +
                        '<h4 class="modal-title" id="myModalLabel"></h4>' +
                        '</div>' +
                        '<div class="modal-body"></div>' +
                        '<div class="modal-footer"></div>' +
                        '</div>' +
                        '</div>' +
                        '</div>');
                }

                var removeDialog = $('#removeDialog');

                var buttons = '' +
                    '<button class="btn btn-primary btn_remove" type="button">Yes</button>' +
                    '<button class="btn btn-default" type="button"  data-dismiss="modal">No</button>';

                $('.modal-header .modal-title', removeDialog).html('Remove Confirmation');
                $('.modal-body', removeDialog).html('Are you sure you want to remove this block?');
                $('.modal-footer', removeDialog).html(buttons);

                $('body').undelegate('.btn_remove', 'click').delegate('.btn_remove', 'click', function () {
                    _this.parent('.user-block').remove();
                    if ($('.user-blocks .user-block').length == 0) {
                        $('#add-user').html('Add a User');
                    }
                    removeDialog.modal('hide');
                    Registration.disableUsersSubmitButton();
                });

                removeDialog.modal('show');
            });
        },
        // disables organization submit button
        disableOrgSubmitButton: function () {
            var fieldList = [
                '#organization_name',
                '#organization_name_abbr',
                '#organization_type',
                '#organization_address',
                '#country',
                '#organization_registration_agency',
                '#registration_number',
                '#organization_identifier'
            ];
            Registration.disableSubmit(fieldList, '#organization-form');
        },
        // disables users submit button
        disableUsersSubmitButton: function () {
            var fieldList = [
                '#first_name',
                '#last_name',
                '#email',
                '#password',
                '#confirm_password',
                '#secondary_contact',
                '[name$="[username]"]',
                '[name$="[email]"]',
                '[name$="[first_name]"]',
                '[name$="[last_name]"]',
                '[name$="[role]"]'
            ];
            Registration.disableSubmit(fieldList, '#users-form');
        },
        // disables submit button of given form
        disableSubmit: function (fieldList, form) {
            var fields = $(fieldList.join(', '), form);
            fields.on('change', function () {
                var check = true;
                fields.each(function () {
                    if ($(this).val() == "") {
                        check = false;
                        return false;
                    }
                });
                if (check) {
                    $('button[type="submit"]', form).removeAttr('disabled');
                } else {
                    $('button[type="submit"]', form).attr('disabled', 'disabled');
                }
            });
            fields.eq(0).trigger('change');
        },
        // lists similar organizations
        filterSimilarOrg: function () {
            $('.search_org').keydown(function (e) {
                if (e.keyCode === 13) {
                    $('.btn-search').trigger('click');
                    return false;
                }
            });

            $('.btn-search').click(function () {
                var value = $('.search_org').val();
                if (value != '') {
                    $('body').append('<div class="loader">.....</div>');
                    $.ajax({
                        type: 'get',
                        url: '/similar-organizations/' + value,
                        success: function (data) {
                            var list = '<p>Please click on the organisation name if it is your organisation.</p>';
                            for (var i in data) {
                                list += '<li><label><input name="similar_organization" type="checkbox" value="' + i + '"> ' + data[i] + '</label></li>';
                            }
                            list += '<li><label><input name="similar_organization" type="checkbox" value=""> None of these are my organisation.</label></li>';
                            $('ul.organization-list').html(list);
                        },
                        complete: function () {
                            $('body > .loader').addClass('hidden').remove();
                        }
                    });
                } else {
                    $('ul.organization-list').html('');
                }
            });

            $('form').delegate('[name="similar_organization"]', 'click', function () {
                $('[name="similar_organization"]').not(this).prop('checked', false);
            });

            $('.btn-search').trigger('click');

            // $('#similar-org-form').submit(function (e) {
            //     var type = $('[name="type"]', this).val();
            //     var similarOrg = $('[name="similar_organization"]', this).val();
            //     if (similarOrg != '' && (type == '' || type == 'user')) {
            //         e.preventDefault();
            //
            //     }
            // });
        },
        // addition of registration agency
        addRegAgency: function () {
            var modal = $('#reg_agency');
            $('.add_agency').click(function () {
                modal.modal('show');
            });

            modal.on('show.bs.modal', function () {
                var country = $('.country').val();
                if (country == "") {
                    $('.messages', modal).removeClass('hidden').html('Please select a Country to add Registration Agency.');
                    $('button[type="submit"]', this).addClass('hidden');
                } else {
                    $('.form-container', modal).removeClass('hidden');
                    $('button[type="submit"]', this).removeClass('hidden');
                }
            });

            modal.on('hidden.bs.modal', function () {
                $('.messages, .form-container', '#reg_agency').addClass('hidden');
            });

            $.validator.addMethod("abbr", function (value, element, param) {
                if (this.optional(element)) {
                    return true;
                }
                return /^[A-Z]+$/.test(value);
            });

            $.validator.addMethod("abbr_exists", function (value, element, param) {
                var regAgency = $('.country').val() + '-' + value;
                return !(regAgency in agencies);
            });

            var form = $('#reg-agency-form');
            form.validate({
                submitHandler: function () {
                    var country = $('.country').val();
                    var name = $('#name', form).val();
                    var shortForm = $('#short_form', form).val();
                    var website = $('#website', form).val();
                    var regAgency = $('.organization_registration_agency');
                    var agencyData = JSON.parse($('.agencies').val());
                    var agencyCode = country + '-' + shortForm;
                    agencyData[agencyCode] = name;
                    agencies = agencyData;
                    $('#agency_name').val(name);
                    $('#agency_website').val(website);
                    $('.agencies').val(JSON.stringify(agencyData));
                    modal.modal('hide');
                    $('.country').trigger('change');
                    regAgency.val(agencyCode).trigger('change');

                    var newAgencies = $('.new_agencies').val();
                    newAgencies = newAgencies == '' ? {} : JSON.parse(newAgencies);
                    newAgencies[agencyCode] = {name: name, short_form: shortForm, website: website};
                    $('.new_agencies').val(JSON.stringify(newAgencies));
                }
            });
            form.submit(function () {
                $('button[type="submit"]', this).removeAttr('disabled');
            });
            $('#name', form).rules('add', {required: true, messages: {required: 'Name is required.'}});
            $('#short_form', form).rules('add', {
                required: true,
                abbr: true,
                abbr_exists: true,
                messages: {
                    required: 'Short Form is required.',
                    abbr: 'Short Form should be alphabetic uppercase characters.',
                    abbr_exists: 'Registration Agency with this short form already exists.'
                }
            });
            $('#website', form).rules('add', {required: true, url: true, messages: {required: 'Website is required.', url: 'Website is not a valid URL.'}});

        },
        // validations for registration form
        validation: function () {
            $.validator.addMethod("email", function (value, element) {
                return this.optional(element) || /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+\@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])\.[.a-zA-Z0-9](?:[.a-zA-Z0-9-]{0,61}[a-zA-Z0-9])*$/.test($.trim(value));
            });

            var form = $('#from-registration');
            var validation = form.validate({
                errorClass: 'text-danger',
                highlight: function (element, errorClass, validClass) {
                    $(element).parents('.form-group').eq(0).addClass('has-error');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).parents('.form-group').eq(0).removeClass('has-error');
                },
                invalidHandler: function () {
                    $('button[type="submit"]', form).removeAttr('disabled');
                }
            });
            form.submit(function () {
                preventNavigation = false;
            });

            /* organization validation rules */
            $(slash('#organization[organization_name]'), form).rules('add', {required: true, messages: {required: 'Organization Name is required.'}});
            $(slash('#organization[organization_name_abbr]'), form).rules('add', {required: true, messages: {required: 'Organization Name Abbreviation is required.'}});
            $(slash('#organization[organization_type]'), form).rules('add', {required: true, messages: {required: 'Organization Type is required.'}});
            $(slash('#organization[organization_address]'), form).rules('add', {required: true, messages: {required: 'Address is required.'}});
            $(slash('#organization[country]'), form).rules('add', {required: true, messages: {required: 'Country is required.'}});
            $(slash('#organization[organization_registration_agency]'), form).rules('add', {required: true, messages: {required: 'Organization Registration Agency is required.'}});
            $(slash('#organization[registration_number]'), form).rules('add', {
                required: true,
                alphanumeric: true,
                messages: {required: 'Registration Number is required.', alphanumeric: 'Only letters and numbers are allowed.'}
            });

            /* users validation rules */
            $(slash('#users[first_name]'), form).rules('add', {required: true, messages: {required: 'First Name is required.'}});
            $(slash('#users[last_name]'), form).rules('add', {required: true, messages: {required: 'Last Name is required.'}});
            $(slash('#users[email]'), form).rules('add', {required: true, email: true, messages: {required: 'Email is required.'}});
            $(slash('#users[password]'), form).rules('add', {required: true, minlength: 6, messages: {required: 'Password is required.'}});
            $(slash('#users[confirm_password]'), form).rules('add', {required: true, equalTo: $(slash('#users[password]'), form), messages: {required: 'Confirm Password is required.'}});
            $(slash('#users[secondary_contact]'), form).rules('add', {required: true, email: true, messages: {required: 'Secondary Contact is required.'}});
            $('.user-blocks .user-block').each(function () {
                Registration.usersValidationRules($(this).index());
            });

            return validation;
        },
        // user validations
        usersValidationRules: function (index) {
            var form = $('#from-registration');
            $(slash('#users[user][' + index + '][username]'), form).rules('add', {required: true, messages: {required: 'Username is required.'}});
            $(slash('#users[user][' + index + '][email]'), form).rules('add', {required: true, email: true, messages: {required: 'E-mail Address is required.'}});
            $(slash('#users[user][' + index + '][first_name]'), form).rules('add', {required: true, messages: {required: 'First Name is required.'}});
            $(slash('#users[user][' + index + '][last_name]'), form).rules('add', {required: true, messages: {required: 'Last Name is required.'}});
            $(slash('#users[user][' + index + '][role]'), form).rules('add', {required: true, messages: {required: 'Permission Role is required.'}});
        },
        // handles registration tabs
        tabs: function () {

            if ($('.organization_name_abbr').parents('.form-group').eq(0).hasClass('has-error')) {
                $('.organization_name_abbr').trigger('change');
            }

            $('#from-registration input').keyup(function (e) {
                if (e.keyCode === 13) {
                    var nextTab = $(this).parents('.tab-pane').next('.tab-pane').attr('id');
                    if (nextTab != undefined) {
                        $('a[href="#' + nextTab + '"]').tab('show');
                    }
                }
            });

            var firstInvalidElem = $('.form-group.has-error').eq(0).find('.form-control');
            var tabId = firstInvalidElem.parents('.tab-pane').eq(0).attr('id');
            if (tabId != undefined) {
                $('a[href="#' + tabId + '"]').tab('show');
                firstInvalidElem.focus().trigger("focusin");
            }

            var validation = Registration.validation();
            $('[data-tab-trigger]').click(function () {
                $('a[href="' + $(this).attr('data-tab-trigger') + '"]').trigger('click');
            });
            $('[data-toggle="tab"]').click(function () {
                $(this).tab('show');
                return false;
            });
            $('a[data-toggle="tab"]').on('hide.bs.tab', function (e) {
                if ($(e.target).parent('li').index() < $(e.relatedTarget).parent('li').index()) {
                    var isValid = $('input, select', '#from-registration').valid();
                    if (!isValid) {
                        validation.focusInvalid();
                        return false;
                    }
                    if ($(e.relatedTarget).attr('href') == '#tab-users') {
                        setIdentifier();
                        Registration.checkOrgIdentifier();
                    }
                }
            });
            function setIdentifier() {
                var userIdentifier = $('.organization_name_abbr').val();
                $('#user-identifier').attr('data-id', userIdentifier);
                $('#username').html(userIdentifier + '_admin');
                $('.username').trigger('change').prev('.input-group-addon').html(userIdentifier + '_');
            }

            setIdentifier();
        },
        checkOrgIdentifier: function () {
            var orgIdentifier = $('.organization_identifier').val();
            if (orgIdentifier == '') {
                return false;
            }
            var callback = function (data) {
                if (!$.isEmptyObject(data)) {
                    Registration.orgData = data;
                    $('a[href="#tab-organization"]').tab('show');
                    $('#org-identifier-modal').modal('show');
                }
            };
            Registration.request("/check-org-identifier", {org_identifier: orgIdentifier}, callback);
        },
        // handles same organization identifier
        sameIdentifier: function () {
            $('#org-identifier-modal').on('show.bs.modal', function () {
                preventNavigation = false;
                $('.org-name').html(Registration.orgData.org_name);
                $('.admin-name').html(Registration.orgData.admin_name);
            });

            $('.confirm-organization').click(function () {
                $('#org-identifier-modal').modal('hide');
                $('#org-identifier-confirmation-modal').modal('show');
            });

            $('.need-new-user').click(function () {
                $('#org-identifier-confirmation-modal').modal('hide');
                $('#contact-admin-modal').modal('show');
            });
        },
        // handles similar orginazations
        similarOrgs: function () {
            var checkElem = $('.organization_name');
            checkElem.on('change', checkAvailability);
            checkElem.on('keydown', function () {
                checkElem.siblings('.availability-check').html('').addClass('hidden').removeClass('text-warning');
            });
            function checkAvailability() {
                var orgName = $(this).val();
                if ($.trim(orgName) == "") {
                    return false;
                }
                var callback = function (data) {
                    if ($.isEmptyObject(data)) {
                        checkElem.siblings('.availability-check').html('').addClass('hidden').removeClass('text-warning');
                    } else {
                        checkElem.siblings('.availability-check').html('It seems there are account(s) on Aidstream with same/similar organisation name. <a href="/find-similar-organizations" class="check_similar_org">Click here</a> to check if your organisation is already there.').removeClass('hidden').addClass('text-warning');
                    }
                };
                Registration.request('/similar-organizations/' + orgName, {}, callback, 'GET');
            }

            var form = $('#from-registration');

            form.delegate('.check_similar_org', 'click', function (e) {
                e.preventDefault();
                form.attr('action', '/find-similar-organizations');
                form.validate().destroy();
                form.submit();
            });
        }
    }

})(jQuery);
