if (typeof(Chunk) == "undefined") var Chunk = {};

(function ($) {

    Chunk = {
        clickPagination: function (route) {
            $('.pagination a').click(function (e) {
                e.preventDefault();
                var pageNo = this.href.substr(this.href.indexOf('=') + 1);
                $('#form-filter').attr("action", route + pageNo);
                $('#form-filter').submit();
            });
        },
        submitFilter: function () {
            $('#form-filter').submit(function () {
                preventNavigation = false;
            });
        },
        toggleData: function (data) {
            $("#json-view").JSONView(data, {
                collapsed: true
            });

            $('#toggle-btn').on('click', function () {
                $('#json-view').JSONView('toggle');
            });
        },
        displayPicture: function () {
            function readURL(input) {

                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('#selected_picture').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#picture").change(function () {
                readURL(this);
            });
        },
        usernameGenerator: function () {
            $('.username').on('change keyup', function (e) {
                var username = $(this).val();
                if (userIdentifier.indexOf(username) == 0 && username.length <= userIdentifier.length) {
                    username = "";
                } else if (username.indexOf(userIdentifier) != 0) {
                    username = userIdentifier + username;
                }
                $(this).next('.login_username').val(username);
            });
        }, changeCountry: function () {
            var country = $('#country')
            country.change(function () {
                Chunk.filterAgency($(this).val());
            });
            var regAgency = $('#registration_agency').val();
            country.trigger('change');
            $('#registration_agency').val(regAgency).select2();
        },
        filterAgency: function (country) {
            var filteredAgencies = '<option value="" selected="selected">Select an Agency</option>';
            for (var i in agencies) {
                if (i.indexOf(country) == 0 || i.indexOf('XI') == 0 || i.indexOf('XM') == 0) {
                    filteredAgencies += '<option value="' + i + '">' + agencies[i] + '</option>';
                }
            }
            $('#registration_agency').html(filteredAgencies).select2();
        },
        updatePermission: function (user_id) {
            $('#permission').on('change', function (e) {
                var permission = $(this).val();
                var permission_text = $(':selected', this).text();
                $('#response').addClass('hidden');
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('[name="_token"]').val()},
                    url: 'organization-user/update-permission/' + user_id,
                    data: {permission: permission},
                    type: 'POST',
                    success: function (data) {
                        $('#response').removeClass('hidden');
                        $('#response').html(permission_text + ' level permission has been given to ' + username);
                    }
                });
            });
        },
        verifyPublisherAndApi: function () {
            $('#verify').on('click', function (e) {
                var publisherId = $('#publisher_id').val();
                var apiKey = $('#api_id').val();
                $('#error').addClass('hidden');
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('[name="_token"]').val()},
                    url: '/publishing-settings/verifyPublisherAndApi',
                    data: {publisherId: publisherId, apiKey: apiKey},
                    type: 'POST',
                    success: function (data) {
                        var publisher_response = data['publisher_id'];
                        var api_key = data['api_key'];
                        publisher_response = (publisher_response) ? "Verified" : "Not Verified";
                        api_key = (api_key) ? "Correct" : "Incorrect";
                        $('#publisher_id_status').val(publisher_response);
                        $('#api_id_status').val(api_key);
                    },
                    error: function (data) {
                        var response = data.responseJSON.publisher_id;
                        $('#error').removeClass('hidden');
                        $('#error').focus();
                        $('#error').html(response);
                    }
                });
            });
        },
        checkImport: function () {
            var importCheckboxes = $('#import-activities input[type="checkbox"]:not([disabled="disabled"])');
            var submitBtn = $('#import-activities .btn_confirm');
            importCheckboxes.click(function () {
                submitBtn.attr('disabled', 'disabled');
                importCheckboxes.each(function () {
                    if ($(this).prop('checked')) {
                        submitBtn.removeAttr('disabled');
                        return true;
                    }
                });
            });
        }
    }
})
(jQuery);