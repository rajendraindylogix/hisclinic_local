var $ = jQuery;
var  count = 0;
window.dataLayer = window.dataLayer || [];
var clinic = (function(window, document) {
	var $ = jQuery;
	var self = {};

    self.busy = false;

    self.move_to = function(target, offset) {
        if (!$(target).length) {
            return false;
        }

        if (offset == undefined) {
            offset = $('.main-header').height();
        }

        var top = $(target).offset().top - offset;

        $('html, body').animate({
            scrollTop: top
        });
    };

    self.init_rotating_text = function() {
        if (!$('.rotating-text').length) {
            return false;
        }

        $.each($('.rotating-text'), function(k, v) {
            var $inner = $(v).find('.inner');
            var $text = $(v).find('.text');

            $inner.append($text.clone());
            $inner.append($text.clone());

            var x = 0;

            function move() {
                $inner.css({
                    'transform': 'translate3d(' + x + 'px, 0, 0)'
                });

                x -= 1;

                if (x <= ($text.outerWidth() * -1)) {
                    x = 0;
                }

                requestAnimationFrame(move);
            }

            move();
        });
    };

    self.init_medical_form = function() {
        if (!$('.medical-form').length) {
            return false;
        }

        var step = $('.steps-control a.active').data('step');

        function load_step(new_step) {
            var $current = $('#step-' + step);

            $('.steps-control .active').removeClass('active');
            $('.steps-control .passed').removeClass('passed');
            $('.steps-control [data-step=' + new_step + ']').addClass('active');

            for (var i = 0; i < new_step; i++) {
                $('.steps-control [data-step=' + i + ']').addClass('passed');
            }

            var id = '#step-' + new_step;

            $current.fadeOut(300, function() {
                $(id).fadeIn(300, function() {
                    step = new_step;
                    self.busy = false;
                });
            });

            $('html, body').animate({ scrollTop: 0 });

            if (new_step == 1) {
                $('.steps-control .back').hide();
            } else {
                $('.steps-control .back').show();
            }

            // Only show Menu on Step #1
            $('.main-header .col-xs-9').fadeOut('fast');
        }

        $('.steps-control a').click(function (e) {
            e.preventDefault();

            if (!$(this).hasClass('passed') || self.busy) {
                return false;
            }

            self.busy = true;

            if (!$(this).hasClass('active')) {
                load_step($(this).data('step'));
            }
        });

        $('.steps-control .back').click(function(e) {
            load_step(step - 1);
        });

        $('#medical-form-form').validate({
            rules: {
                'first-name': {
                    required: true
                },
                'last-name': {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                'date-of-birth': {
                    required: true,
                    australianDate: true
                },
                gender: {
                    required: true
                },
                'symptoms-of-ed': {
                    required: true
                },
                'advised-not-to-use': {
                    required: true
                },
                'do-you-get-angina': {
                    required: true
                },
                'had-a-heart-attack': {
                    required: true
                },
                'had-a-stroke-or-tia': {
                    required: true
                },
                'taking-any-nitrate-medications': {
                    required: true
                },
                password: {
                    required: true,
                    pwcheck: true
                },
                password2: {
                    equalTo: "#password"
                }
            },
            submitHandler: function(form) {
                return false;
            }
        });


        $('.medical-form .continue').click(function(event) {
            event.preventDefault();

            switch (step) {
                case 1:
                    load_step(2);

					dataLayer.push({
						event: 'eligibililtyStart',
					});

                    break;

                case 2:
                    var to_validate = ['first-name', 'last-name', 'email', 'date-of-birth', 'gender'];
                    var valid = true;

                    $.each(to_validate, function (k, v) {
                        if (!$('.medical-form [name="' + v + '"]').valid()) {
                            valid = false;
                        }
                    });

                    if ($('[name=gender]:checked').length) {
                        if ($('[name=gender]:checked').val().toLowerCase() == 'female') {
                            $('.gender-note').slideDown();
                            valid = false;
                        } else {
                            $('.gender-note').slideUp();
                        }
                    }

                    if (valid) {
                        $('#step-4 .name-text').text($('[name="first-name"]').val() + ' ' + $('[name="last-name"]').val());
                        $('#step-4 .email-text').text($('[name="email"]').val());

                        load_step(3);

						dataLayer.push({
							event: 'eligibililtyAbout',
						});
                    }

                    break;

                case 3:
                    var to_validate = ['symptoms-of-ed', 'advised-not-to-use', 'do-you-get-angina', 'had-a-heart-attack', 'had-a-stroke-or-tia', 'taking-any-nitrate-medications'];
                    var to_approve = ['advised-not-to-use', 'do-you-get-angina', 'had-a-heart-attack', 'had-a-stroke-or-tia', 'taking-any-nitrate-medications'];

                    var valid = true;
                    var approved = true;

                    $.each(to_validate, function (k, v) {
                        if (!$('.medical-form [name="' + v + '"]').valid()) {
                            valid = false;
                        }
                    });

					$.each(to_approve, function (k, v) {
                        if ($('.medical-form [name="' + v + '"]:checked').val().toLowerCase() == 'yes') {
                            approved = false;
                        }
                    });

                    if (valid) {
                        if (approved) {
                            $('#step-4 .approved').show();
                            $('#step-4 .not-approved').hide();
                        } else {
                            $('#step-4 .approved').hide();
                            $('#step-4 .not-approved').show();
                        }

                        load_step(4);

						dataLayer.push({
							event: 'eligibililtyHealth',
						});
                    }

                    break;

                case 4:
                    var to_validate = ['password', 'password2'];
                    var valid = true;

                    $.each(to_validate, function (k, v) {
                        if (!$('.medical-form [name="' + v + '"]').valid()) {
                            valid = false;
                        }
                    });

                    if (valid) {
                        var params = $('#medical-form-form').serializeArray();
                        params.push({ name: 'action', value: 'process_medical_form' });

                        $.post(wp_paths.admin, params, function (data, textStatus, jqXHR) {
                            if (data.success) {
                                $('#step-4 .inner').slideUp();
                                $('#step-4 .success').slideDown();

								dataLayer.push({
									event: 'eligibililtyAccount',
								});

								$('#step-4 .success .approved').click(function() {
									dataLayer.push({
										event: 'eligibililtyBrowse',
									});
								});
                            } else {
                                alert(data.message);
                            }
                        }, 'json');
                    }

                    break;

                default:
                    break;
            }
        });

        $('.dob').mask('00/00/00');
    };

    self.init_login = function() {
        $('.woocommerce-form.login').validate({
            rules: {
                'username': {
                    required: true,
                    email: true
                },
                'password': {
                    required: true
                },
            }
        });
    };

    self.init_faq = function() {
        if (!$('.faq-filter').length) {
            return false;
        }

        $('.faq-filter a').click(function(e) {
            e.preventDefault();

            if (!$(this).hasClass('active')) {
                $('.faq-filter a').removeClass('active');
                $(this).addClass('active');

                var id = $(this).attr('href');

                if (id == '#all') {
                    $('.faq').show();
                } else {
                    $('.faq').hide();
                    $(id).show();
                }
            }
        });

        $('.faq-filter a').first().click();

        $('.elementor-tab-title.elementor-active').click();

        setTimeout(function() {
            $('.elementor-tab-title.elementor-active').removeClass('elementor-active');
            $('.elementor-tab-content.elementor-active')
                .removeClass('elementor-active')
                .hide();
        }, 500);
    };

    self.init_blog = function() {
        var posts = {
            page: 2, // Start at page 2 because page 1 is printed with php
            category: null
        };

        posts.load = function(clear) {
            var params = {
                action: 'fetch_posts',
                category: posts.category,
                page: posts.page
            };

            $.post(wp_paths.admin, params, function (data, textStatus, jqXHR) {
                if (data.success == true) {
                    if (clear == true) {
                        $('.posts').html('');
                    }

                    $('.posts').append(data.html);

                    if (data.more) {
                        $('.load-more').show();
                    } else {
                        $('.load-more').hide();
                    }

                    posts.page++;
                }

                self.busy = false;
            }, 'json');
        };

        $('.posts-filter a').click(function (e) {
            e.preventDefault();

            if (self.busy) {
                return false;
            }
            self.busy = true;

            $('.posts-filter a').removeClass('active');
            $(this).addClass('active');

            var c = $(this).data('category');

            posts.page = 1;
            posts.category = c;
            posts.load(true);
        });

        $('.load-more').click(function(e) {
            e.preventDefault();

            if (self.busy) {
                return false;
            }
            self.busy = true;

            posts.load();
        });

        if( $('.related-posts .posts').length ){
            $('.related-posts .posts').slick({
                slidesToShow: 3,
                slidesToScroll: 1,
                dots: false,
                arrows: true,
                responsive: [
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1,
                            dots: true,
                            arrows: false
                        }
                    }
                ]
            });
        }

    };

    self.init_product = function() {
        if (!$('.single-product')) {
            return false;
        }

        var product = {};

        product.get_product_attributes_names = function() {
            // Collecting variations names
            var attributes_names = [];

            $.each($('.variations input[type="radio"]'), function(index, val) {
                var name = $(val).prop('name');

                if (attributes_names.indexOf(name) == -1) {
                    attributes_names.push(name);
                }
            });

            return attributes_names;
        };

        product.get_variations = function() {
            var attributes_names = product.get_product_attributes_names();
            var variations = {};
            var valid = true;

            // Collecting values to find variation id
            $.each(attributes_names, function(index, name) {
                var item = $('.variations [name*="' + name + '"]');
                var field_value;

                if ($(item).is('input[type=radio]')) {
                    field_value = $('.variations [name*="' + name + '"]:checked').val();
                } else {
                    field_value = item.val();
                }

                if (field_value == undefined || field_value == '') {
                    valid = false;
                }

                variations[name] = field_value;
            });

            return (valid) ? variations : false;
        };

        product.get_variation_id = function() {
            var variations = product.get_variations();
            var variation_id = null;

            if (variations) {
                // Finding variation id, woocommerce only works with selects...
                $.each($('.variations_form').data('product_variations'), function(index, v) {
                    var found = true;

                    $.each(variations, function(variation_key, variation_value) {
                        var name = variation_key.toLowerCase(); // case sensitive!

                        if (variation_value != v.attributes[name]) {
                            found = false;
                        }
                    });

                    if (found) {
                        variation_id = v.variation_id;
                    }
                });
            }

            return variation_id;
        }

        // Calculating price based on quantity and price
        product.update_price = function() {
            if (product.current_price) {
                var price = (product.current_price * $('.variations .qty').val()).toFixed(2);
                $('.woocommerce-variation-price .amount').html('$' + price);
            }
        }

        product.current_price = null;
        $('.variations [name^="attribute_pa"]').on('change', function(event) {
            var variation_id = product.get_variation_id();

            if (variation_id == null) {
                return;
            }

            $.each($('.variations_form').data('product_variations'), function(index, v) {
                if (v.variation_id == variation_id) {
                    if (v.is_in_stock) {
                        $('.add_to_cart.btn').removeProp('disabled');
                    } else {
                        $('.add_to_cart.btn').prop('disabled', 'disabled');
                    }

                    // Update page price
                    if (v.price_html) {
                        product.current_price = v.display_price;
                        $('.woocommerce-variation-price').html(v.price_html);

                        product.update_price();
                    }

                    $('.variation_id').val(variation_id);

                    return false;
                }
            });
        });
        $('.variations [name^="attribute_pa"]').change();

        setTimeout(function() {
            $('.variations [name^="attribute_pa"]').change();
        }, 1000);

        $('.variations .qty').on('keyup change', function (e) {
            product.update_price();
        });
    };

    self.init_account = function() {
        if (!$('.woocommerce-account').length) {
            return false;
        }

        $('.accordion .title').click(function(e) {
            var $parent = $(this).parents('.accordion');

            if ($parent.hasClass('active')) {
                $parent.find('.box').slideUp(function() {
                    $parent.removeClass('active');
                    $(this).clearQueue();
                });
            } else {
                $parent.find('.box').slideDown(function() {
                    $parent.addClass('active');
                    $(this).clearQueue();
                });
            }
        });

        $('#update-account-password').validate({
            rules: {
                password_current: {
                    required: true
                },
                password_1: {
                    required: true,
                    pwcheck: true
                },
                password_2: {
                    equalTo: "#password_1"
                }
            }
        });

        $('.avatar input').change(function (e) {
            $('#update-avatar').submit();
        });

        $('.same-address').click(function(e) {
            e.preventDefault();

            $.each($('[name]'), function(k, v) {
                var name = $(v).attr('name');
                var ref_name = name.replace('shipping', 'billing');
                var ref = '[name="' + ref_name + '"]';

                if ($(ref).length) {
                    $(v).val($(ref).val());
                }
            });
        });

        if( $('.account-content select').length ){
            $('.account-content select').select2();
        }

        if ($('.need-to-update-popup').length) {
            $('.need-to-update-popup').lightbox_me({
                centered: true,
                closeClick: false,
                closeSelector: '.close-popup'
            });
        }
    };

    self.init_card_carousel = function() {
        if (!$('.card-carousel').length) {
            return false;
        }

        $.each($('.card-carousel .cards'), function (i, elem) {
            $(elem).slick({
                slidesToShow: 3,
                slidesToScroll: 1,
                dots: false,
                arrows: true,
                responsive: [
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1,
                            arrows: false,
                            dots: true
                        }
                    }
                ]
            });
        });
    };

    self.init_res_menu = function() {
        var scroll_top = 0;

        $('.menu-toggle').click(function(e) {
            e.preventDefault();

            var $bt = $(this);
            var $menu = $('.res-menu');

            if (!$bt.hasClass('active')) {
                scroll_top = $(window).scrollTop();

                $menu.animate({
                    right: 0
                },
                500,
                function() {
                    $bt.addClass('active');
                    $(this).clearQueue();
                });

                $('body').addClass('locked');
            } else {
                $menu.animate({
                    right: '-100%'
                },
                500,
                function() {
                    $bt.removeClass('active');
                    $(this).clearQueue();
                });

                $('body').removeClass('locked');
                $(window).scrollTop(scroll_top);
            }
        });
        
        $('.menu-seo-toggle').click(function(e) {
            e.preventDefault();

            var $bt = $(this);
            var $menu = $('.res-seo-menu');

            if (!$bt.hasClass('active')) {
                scroll_top = $(window).scrollTop();

                $menu.animate({
                    right: 0
                },
                500,
                function() {
                    $bt.addClass('active');
                    $(this).clearQueue();
                });

                $('body').addClass('locked');
            } else {
                $menu.animate({
                    right: '-100%'
                },
                500,
                function() {
                    $bt.removeClass('active');
                    $(this).clearQueue();
                });

                $('body').removeClass('locked');
                $(window).scrollTop(scroll_top);
            }
        });        
    };

    self.get_age_by_date = function(date_string) {
        var today = new Date();
        date_string = date_string.split('/');
        var birthDate = new Date(date_string[2] + '-' + date_string[1] + '-' + date_string[0]);
        var age = today.getFullYear() - birthDate.getFullYear();
        var m = today.getMonth() - birthDate.getMonth();

        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        return age;
    };

    $(document).ready(function($) {
	    $('html').removeClass('no-js');

        // jQuery validator defaults
        $.validator.addMethod("pwcheck", function (value) {
            // at least one number, one lowercase and one uppercase letter
            // at least six characters
            var regex = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/;
            return regex.test(value);
        }, 'Please enter a valid password');

        $.validator.addMethod(
            "australianDate",
            function (value, element) {
                return value.match(/^(0?[1-9]|[12][0-9]|3[0-1])[/., -](0?[1-9]|1[0-2])[/., -](19|20)?\d{2}$/);
            },
            "Please enter a date in the format dd/mm/yy."
        );
        
        $.validator.addMethod(
            "validAge",
            function (value, element) {
                var age = clinic.get_age_by_date(value);
                return age >= 18 && age <= 75;
            },
            "Valid ages are between 18 and 75"
        );

        self.init_rotating_text();
        self.init_medical_form();
        self.init_login();
        self.init_faq();
        self.init_blog();
        self.init_product();
        self.init_account();
        self.init_card_carousel();
        self.init_res_menu();

        // Generic
        $('.scroll-down').click(function(e) {
            e.preventDefault();

            var target = $(this).parents('section.elementor-element').next('section.elementor-element');
            self.move_to(target);
        });

        $('.white-boxes .elementor-row').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            mobileFirst: true,
            dots: true,
            arrows: false,
            responsive: [
                {
                    breakpoint: 991,
                    settings: 'unslick'
                }
            ]
        });

        $('.list-scroller ul').slick({
            slidesToShow: 6,
            slidesToScroll: 1,
            variableWidth: true,
            dots: false,
            arrows: true,
            infinite: false,
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2,
                        variableWidth: false,
                    }
                }
            ]
        });

        $('.image-text-carousel .items').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            mobileFirst: true,
            arrows: false,
            dots: true,
            responsive: [
                {
                    breakpoint: 768,
                    settings: 'unslick'
                }
            ]
        });

        // Active effect on navigation
        $('.main-header .menu-top-menu-container .menu li:not(.current-menu-item) a').hover(
            function () {
                $('.main-header .menu-top-menu-container .menu').addClass('hover');
            }, function () {
                $('.main-header .menu-top-menu-container .menu').removeClass('hover');
            }
        );

        // Change all links if already logged in
        if ($('body').hasClass('logged-in')) {
            $.each($('a'), function(k, link) {
                var $a;
                var text;

                if ($(link).text().trim() != '') {
                    $a = $(link);
                } else {
                    $a = $(link).find('span');
                }

                text = $a.text().toLowerCase().trim();

                if (text == 'check eligibility' || text == 'get started') {
                    $a.attr('href', wp_paths.home_url + '/my-account');
                    $a.text('Your Treatments');
                    $a.removeClass('mf-next');
                    //$a.addClass('block');
                }
            });
        }
    });

    return {
        move_to: self.move_to,
        get_age_by_date: self.get_age_by_date
    };
}) (window, document);




jQuery(function($){

    // ========================== //
    //     Multistep form         //
    //  ==========================//

    $('body').on('change','#payment .woocommerce-form__input-checkbox',function () {
        if ( $('#terms').is(":checked") && $('#checked_medical_details_confirm').is(":checked") ) {
            $('.place-order-button').addClass("btn-checked");
        } else {
            $('.place-order-button').removeClass("btn-checked");
        }
    });


    $('body').on('click','span.woocommerce-terms-and-conditions-checkbox-text',function () {

    });


    //convert svg image to svg code
    $("img.svg").each(function() {
        var $img = $(this);
        var imgID = $img.attr("id");
        var imgClass = $img.attr("class");
        var imgURL = $img.attr("src");
        $.get(
            imgURL,
            function(data) {
                // Get the SVG tag, ignore the rest
                var $svg = $(data).find("svg");
                // Add replaced image's ID to the new SVG
                if (typeof imgID !== "undefined") {
                    $svg = $svg.attr("id", imgID);
                }
                // Add replaced image's classes to the new SVG
                if (typeof imgClass !== "undefined") {
                    $svg = $svg.attr("class", imgClass + " replaced-svg");
                }
                // Remove any invalid XML tags as per http://validator.w3.org
                $svg = $svg.removeAttr("xmlns:a");
                // Replace image with new SVG
                $img.replaceWith($svg);
            },
            "xml"
        );
    });

    //hisclininc form validate
    var cusValidator = $('#mf-app form').validate({
        rules: {
            'rfullname': {
                required: true
            },
            'remail': {
                required: true
            },
            'rpassword': {
                required: true
            }
        }
    });


    //weight/height validation
    $('[mf-step="4"] input').on('change keyup', function(){
        setTimeout(function(){ 
            if( ($('.mf-height input:checked').length || $('.mf-height input').val().length) && ($('.mf-weight input:checked').length || $('.mf-weight input').val().length) &&  !$('.mf-height [type="number"]').hasClass('error') && !$('.mf-weight [type="number"]').hasClass('error')  ){
                // console.log('at least one val is true');
                $('.mf-step[mf-step="4"]').find('.btn').removeClass('disabled');
            }else{
                $('.mf-step[mf-step="4"]').find('.btn').addClass('disabled');
                // console.log('at least one val is true');
            }    
         }, 500);
    });

    // weight/height clear values
    $('.mf-height [type="checkbox"]').on('change', function(){ //label
        console.log('height is checked');
        if(  $(this).prop('checked') ){
            $('.mf-height [type="number"]').val('');
            $('.mf-height [type="number"]').removeClass('error');
            $('.mf-height [type="number"] + label.error').remove();
           
        }
    });

    $('.mf-height [type="number"]').on('keyup change', function(){ //input
        console.log('num is filled');
        if( $(this).val().length > 0 ){
            $('.mf-height [type="checkbox"]').prop('checked',false);
        }
    });

    $('.mf-weight [type="checkbox"]').on('change', function(){ //label
        console.log('weight is checked');
        if(  $(this).prop('checked') ){
            $('.mf-weight [type="number"]').val('');
            $('.mf-weight [type="number"]').removeClass('error');
            $('.mf-weight [type="number"] + label.error').remove();
           
        }
    });

    $('.mf-weight [type="number"]').on('keyup change', function(){ //input
        console.log('num is filled');
        if( $(this).val().length > 0 ){
            $('.mf-weight [type="checkbox"]').prop('checked',false);
        }
    });

    //goto next
    $('.mf-next').click(function(){
        $(this).parents('.mf-step').hide().next('.mf-step').fadeIn('slow');

        //offset top
        if( $(window).innerWidth() < 991 ){
            $('html,body').animate({
                scrollTop: $('#mf-app').offset().top - 140
            },500);
        }
    });

    //  mf-step="9.c" repeater blocks
    // var get_repeater = $('.repeat-block').html();
    var repeat_block_count = Math.floor(Math.random() * (999999 - 20 + 1) ) + 20;
    $('#add-more span').click(function(){
    //   $('#add-more').before('<div class="repeat-block">' + get_repeater + '<div class="remove-item"> <span>REMOVE</span> </div></div>');
    var mf_admision_date = wp_paths.mf_admision_date || 'Year of hospital admission or surgery';
    var mf_admision_description = wp_paths.mf_admision_description || 'Details about your hospital admission or surgery';
      $('[mf-step="9.c"] .btn').addClass('disabled');
        $('#add-more').before(' <div class="repeat-block"><div class="animate-inputs animate-textarea"> <div class="animate-input"> <input required type="tel" name="medical_form_details[medical_history][medical_history_desctiption][answer]['+repeat_block_count+'][date]" id="date' + repeat_block_count + '_9.c" placeholder="YYYY" maxlength="4"> <label for="date' + repeat_block_count + '_9.c">' + mf_admision_date +'</label> </div> <div class="animate-input"> <textarea  type="text" required name="medical_form_details[medical_history][medical_history_desctiption][answer]['+repeat_block_count+'][description]" id="textarea' + repeat_block_count + '_9.c" placeholder="Please provide details"></textarea> <label for="textarea' + repeat_block_count + '_9.c">'+mf_admision_description+'</label> </div> </div> <div class="remove-item"> <span>REMOVE</span> </div> </div>');
        repeat_block_count ++;

        $('.mask-date:last-of-type').mask('00/00/0000');

    });

    $('body').on('click' , '.remove-item', function(){
        $(this).parent().remove();
        enable9c_btn();
    });

    //validate
    // $('.mf-next, [mf-step="7.2"] .btn').click(function(){
    //     cusValidator.form();
    // });

    //previous step
    $('.mf-prev').click(function(){
        $(this).parents('.mf-step').hide().prev('.mf-step').fadeIn('slow');
    });

    //back step dynamic for exception template 11.1
    $('[goto-step="11.1"]').click(function(){
        var exception_bck =  $(this).parents('.mf-step').attr('mf-step');
        $('[mf-step="11.1"] .mf-progress .mf-stop').attr('goto-step', exception_bck)
    });

    //stop steps
    $('.mf-stop').click(function(){
        console.log('mf-stop-clicked')
        var get_mfStep = $(this).attr('goto-step');
        $(this).parents('.mf-step').hide()
        $('[mf-step="' + get_mfStep + '"]').fadeIn('slow');
    });

    //cialis , Sildenafil conditional logic
    // $('.mf-step__chkbox__validate input').change(function(){
    //     $this = $(this);
    //     console.log('cialis , Sildenafil c');
    //     //select only one cialis
    //     if( $this.hasClass('cialis') ){
    //         $('.cialis').not($this).attr('checked', false); // Unchecks it
    // }

    //     //goto either cialis , Sildenafil or both
    //     if( $('[data-sildenafil="0"]:checked').length ){
    //         $this.parents('.mf-step__chkbox__validate').find('.btn.mf-stop').attr('goto-step',8.1);

    //     }else{
    //         $this.parents('.mf-step__chkbox__validate').find('.btn.mf-stop').attr('goto-step',8.2);
    //     }

    //     if( $('[data-cialis="0"]:checked').length || $('[data-cialis="1"]:checked').length ){
    //         $('[mf-step="8.1"]').find('label').attr('goto-step',8.2);
    //         console.log('is checked');
    //     }else{
    //         $('[mf-step="8.1"]').find('label').attr('goto-step',9);
    //         console.log('is not checked');
    //     }

    //     var is_chkChkd = $this.parents('.mf-step__chkbox__validate').find('input:checked').length;
    //     console.log('checkbox checked : ' + is_chkChkd );
    //     if( is_chkChkd > 0 ){
    //         $this.parents('.mf-step__chkbox__validate').find('.btn.mf-next').hide();
    //         $this.parents('.mf-step__chkbox__validate').find('.btn.mf-stop').show();
    //         console.log('is graewtear' );
    //     }else{
    //         $this.parents('.mf-step__chkbox__validate').find('.btn.mf-next').show();
    //         $this.parents('.mf-step__chkbox__validate').find('.btn.mf-stop').hide();
    //         console.log('is smaller' );
    //     }
    // });

    //cialis ,cialis 36 hours , Sildenafil conditional logic
    function product_steps($this){
        console.log('products steps triggered');

        //toggle products page or next step
        var is_chkChkd = $this.parents('.mf-step__chkbox__validate').find('input:checked').length;
        console.log('checkbox checked : ' + is_chkChkd );
        if( is_chkChkd > 0 ){
            $this.parents('.mf-step__chkbox__validate').find('.btn.mf-stop').hide();
            $this.parents('.mf-step__chkbox__validate').find('.btn.mf-products').show();
            console.log('more products checked' );
        }else{
            $this.parents('.mf-step__chkbox__validate').find('.btn.mf-stop').show();
            $this.parents('.mf-step__chkbox__validate').find('.btn.mf-products').hide();
            console.log('no products checked' );
        }

        //make relavant product visible
        var get_prod_id = $this.attr('data-product');
        if( $this.is(':checked') ){
            //alert( 'product id is: ' + get_prod_id + 'is checked');
            $('.mf-product[data-product="' + get_prod_id + '"]').addClass('is-selected');
        }else{
            //alert( 'product id is: ' + get_prod_id + 'is unchecked');
            $('.mf-product[data-product="' + get_prod_id + '"]').removeClass('is-selected');
        }
    }

    $('.mf-step__chkbox__validate input').change(function(){
        $this = $(this);
        product_steps($this);
    });

    //goto products if checked or skip to next step
    $('.mf-products').click(function(){
        // alert('going to products');
        $(this).parents('.mf-step').hide();
        $('.mf-product.is-selected').first().fadeIn('slow');
    });

    //hide products on moving back
    $('.hide-products').click(function(){
         $('.mf-product').hide().removeClass('is-selected');
         $('[mf-step="8"] input').prop("checked", false);
         $('[mf-step="8"] .mf-products').hide();
         $('[mf-step="8"] .mf-stop').show();
    });

    //goto next product or next step
    $('.next-product').click(function(){
        // alert('going to next product');
        $(this).parents('.mf-product').removeClass('is-selected').hide();
        if( $('.mf-product.is-selected').length ){
            $('.mf-product.is-selected').first().fadeIn('slow');
            // alert('has next step');
        }else{
            // alert('no next step');
            $('[mf-step="9"]').fadeIn('slow')
        }


    });

    //radio hypertension
    $('[mf-step="9.4"] input').change(function(){
        console.log('hypertension radio');
        $('[mf-step="9.4"] input').not(this).not('.mf-hidden-input').attr('checked', false);
    });

     //hypertension lightheadedness conditional logic
    //  $('.mf-step__chkbox__validate1 input').change(function(){
    //     $this = $(this);
    //     console.log('ypertension lightheadedness ');

    //     //goto either hypertension , lightheadedness or both
    //     if( $('[data-lighthead="0"]:checked').length ){
    //         $this.parents('.mf-step__chkbox__validate1').find('.btn.mf-stop').attr('goto-step', '10a.1' );
    //         $('[mf-step="10a.1"]').find('label.no').attr('goto-step',11);
    //     }else{
    //         // $('[mf-step="10a.1"]').find('label.no').attr('goto-step',11);
    //     }


    //     if( $('[data-hypertension="0"]:checked').length){
    //         $this.parents('.mf-step__chkbox__validate1').find('.btn.mf-stop').attr('goto-step', '10a.2' );
    //         $('[mf-step="10a.1"]').find('label.no').attr('goto-step','10a.2');
    //         console.log('is checked');
    //     }
    //     if( $('[data-hypertension="0"]:checked').length && $('[data-lighthead="0"]:checked').length){
    //         $this.parents('.mf-step__chkbox__validate1').find('.btn.mf-stop').attr('goto-step', '10a.1' );
    //         console.log('both hyper light checked');
    //     }

    //     var is_chkChkd = $this.parents('.mf-step__chkbox__validate1').find('input:checked').length;
    //     console.log('checkbox checked : ' + is_chkChkd );
    //     if( is_chkChkd > 0 ){
    //         $this.parents('.mf-step__chkbox__validate1').find('.btn.mf-next').hide();
    //         $this.parents('.mf-step__chkbox__validate1').find('.btn.mf-stop').show();
    //         console.log('is graewtear' );
    //     }else{
    //         $this.parents('.mf-step__chkbox__validate1').find('.btn.mf-next').show();
    //         $this.parents('.mf-step__chkbox__validate1').find('.btn.mf-stop').hide();
    //         console.log('is smaller' );
    //     }
    // });

    //text change on normal checkbox toggle
    $('[mf-step="8"] .mf-step__chkbox input').not('.mf-step__chkbox__validate input').change(function(){
        console.log('only checkbox without valdiate');
        if( $(this).parents('.mf-step__chkbox').find('input:checked').length ){
            console.log('chk chked');
            $(this).parents('.mf-step__chkbox').find('.btn').text('Continue');
        }else{
            console.log('chk not chked');
            $(this).parents('.mf-step__chkbox').find('.btn').text('None apply');
        }
    });

    //skip step if none selected
    $('.mf-step__stop input').change(function(){
        console.log('mf-step__stop validate');
        if( $(this).parents('.mf-step__chkbox').find('input:checked').length > 1 ){
        //    alert('true');
            $(this).parents('.mf-step__chkbox').find('.btn.mf-next').hide();
            $(this).parents('.mf-step__chkbox').find('.btn.mf-stop').show();
        }else{
            console.log('chk not chked');
            // alert('false');
            $(this).parents('.mf-step__chkbox').find('.btn.mf-stop').hide();
            $(this).parents('.mf-step__chkbox').find('.btn.mf-next').show();
        }
    });

    //toggle final empty check form
    $('[mf-step="14"] .mf-step__stop input').change(function(){
        console.log('final mf-step__stop validate');
        if( $(this).parents('.mf-step__chkbox').find('input:checked').length > 1 ){
            console.log('chk chked');
            $('[mf-step="14"]').find('.btn').addClass('mf-stop').removeClass('goPopup').attr('goto-step',11.1);
            $('[mf-step="14"]').find('.btn').text('Continue');
        }else{
            $('[mf-step="14"]').find('.btn').text('None apply');
            console.log('final chk not chked');
            find_final_form();
        }
    });

    //toggle final form based on having prior surgeries

    // $('[mf-step="9"] input').change(function(){
    //     if( $(this).parents('.mf-step').find('.yes:checked').length ){
    //         $('[mf-step="14"]').find('.btn').addClass('mf-stop').attr('goto-step',15).removeClass('goPopup');
    //         // alert('goto normal form');
    //     }

    //     if( $(this).parents('.mf-step').find('.no:checked').length ){
    //         //$('[mf-step="14"]').find('.mf-stop').attr('goto-step',14.1);
    //         $('[mf-step="14"]').find('.btn').removeClass('mf-stop').addClass('goPopup');
    //         // alert('goto popup form');
    //     }
    // });

    function find_final_form(){
        if( $('[mf-step="9"] .yes:checked').length || $('[mf-step="9.d"] .yes:checked').length || $('[mf-step="9.b"] .yes:checked').length || $('[mf-step="9.c"] .yes:checked').length ){
/*
             $('[mf-step="14"]').find('.btn').addClass('mf-stop').attr('goto-step',15).removeClass('goPopup');
             console.log("FINAL STEP IS NOT POPUP");
*/
             $('[mf-step="14"]').find('.btn').removeClass('mf-stop').addClass('goPopup');
             console.log("FINAL STEP IS POPUP");
        }else{
             $('[mf-step="14"]').find('.btn').removeClass('mf-stop').addClass('goPopup');
             console.log("FINAL STEP IS POPUP");
        }
    }

    $('[mf-step="9.d"] input, [mf-step="9.2"] input').change(function(){
        find_final_form();
    });
    $('[mf-step="9.d"] label, [mf-step="9.2"] label').click(function(){
        find_final_form();
    });

    //goto popup form from fourteenth step
    $('[mf-step="14"] .btn').click(function(e){
        if( $(this).hasClass('goPopup') ){
            //alert('redirecting to popup');
            $('form').append('<div class="loader"><div class="lds-ring"><div></div><div></div><div></div><div></div></div> ');
            popup_redirect(event);
        }
        e.stopPropagation();
    });

    //popup form
    function popup_redirect(event){
        event.preventDefault();
        var formdata = $('#new-medical-form').serializeArray();
        var data = {};
        var redirection = '';
        var args = '';
        $(formdata).each(function(index, obj){
            if(obj.value != ''){
                data[obj.name] = obj.value;
            }
        });

        if(
            data['medical_form_details[recommended_prescription][previous_use_sildenafil][answer]'] != 'Sildenafil' &&
            data['medical_form_details[recommended_prescription][sildenafil_effective][answer]'] != 'Yes' &&
            data['medical_form_details[recommended_prescription][previous_use_cialis][answer]'] == 'Cialis' &&
            data['medical_form_details[recommended_prescription][cialis_effective][answer]'] == 'Yes' &&
            data['medical_form_details[recommended_prescription][previous_use_cialis_daily][answer]'] != 'Daily Cialis' &&
            data['medical_form_details[recommended_prescription][daily_cialis_effective][answer]'] != 'Yes'
        ){
            redirection = data['medical_form_details[redirection_link][cialis_redirection_link]'];
        }
        else if(
            data['medical_form_details[recommended_prescription][previous_use_sildenafil][answer]'] != 'Sildenafil' &&
            data['medical_form_details[recommended_prescription][sildenafil_effective][answer]'] != 'Yes' &&
            data['medical_form_details[recommended_prescription][previous_use_cialis][answer]'] != 'Cialis' &&
            data['medical_form_details[recommended_prescription][cialis_effective][answer]'] != 'Yes' &&
            data['medical_form_details[recommended_prescription][previous_use_cialis_daily][answer]'] == 'Daily Cialis' &&
            data['medical_form_details[recommended_prescription][daily_cialis_effective][answer]'] == 'Yes'
        ){
            redirection = data['medical_form_details[redirection_link][daily_cialis_redirection_link]'];
        }
        else if(
            data['medical_form_details[recommended_prescription][previous_use_sildenafil][answer]'] != 'Sildenafil' &&
            data['medical_form_details[recommended_prescription][sildenafil_effective][answer]'] != 'Yes' &&
            data['medical_form_details[recommended_prescription][previous_use_cialis][answer]'] == 'Cialis' &&
            data['medical_form_details[recommended_prescription][cialis_effective][answer]'] == 'Yes' &&
            data['medical_form_details[recommended_prescription][previous_use_cialis_daily][answer]'] == 'Daily Cialis' &&
            data['medical_form_details[recommended_prescription][daily_cialis_effective][answer]'] == 'Yes'
        ){
            redirection = data['medical_form_details[redirection_link][cialis_redirection_link]'];
        }
        else if(
            data['medical_form_details[recommended_prescription][previous_use_sildenafil][answer]'] == 'Sildenafil' &&
            data['medical_form_details[recommended_prescription][sildenafil_effective][answer]'] != 'Yes'
        ){
            redirection = data['medical_form_details[redirection_link][cialis_redirection_link]'];
        }
        else{
            redirection = data['medical_form_details[redirection_link][sildenafil_redirection_link]'];
        }

        console.log(data);
        console.log(redirection);


        // return false;


        formdata.push({ name: 'action', value: 'medical_form_store_cookie' });

        // console.log(wp_paths.new_admin );

        $.post(wp_paths.admin, formdata, function (response) {
            console.log(response.success);
            if (response.success == true){
                window.location.href = data['page_redirect_link'] + '?sign_up=true&redirection=' + redirection;
            }else{
                return false;
            }
        });

        var args = $.param( data ) || '';
    }

    //age calculate
    $('#dob').mask('00/00/0000');
    $('.mask-date').mask('00/00/0000');

    $('#dob').on('keyup change',function(){
        // $(this).parents('.mf-step').find('.btn').removeClass('disabled');
        // console.log('date changing');
        // date_validate();
        if ( $('#dob').val().length > 9 ) {
	        // $(this).parents('.mf-step').find('.btn').removeClass('disabled');
            // $(this).parents('.mf-step').find('.btn').trigger('click');
            date_validate();
		}
    });

    function date_validate(){
        // $this = $('#dob');
        // var dob = $('#dob').val();
        // if(dob != ''){
        //     var str=dob.split('-');
        //     var firstdate=new Date(str[0],str[1],str[2]);
        //     var today = new Date();
        //     var dayDiff = Math.ceil(today.getTime() - firstdate.getTime()) / (1000 * 60 * 60 * 24 * 365);
        //     var age = parseInt(dayDiff);
        //     if( age < 18 ){
        //         console.log('age smaller than 18');
        //         // $(this).parents('.mf-step').hide()
        //         // $('[mf-step="3.1"]').fadeIn('slow');
        //         // $this.parents('.mf-step').hide();
        //     }else{
        //         console.log('age greater than 18');
        //         // $this.parents('.mf-step').hide().next().fadeIn('slow');
        //     }
        // }
        $this = $('#dob');
        
        var age = clinic.get_age_by_date($this.val());

        console.log( 'age is: ' + age )
        if( age >= 18 && age <= 75  ){
            console.log('age between 18 and 70');
            $this.parents('.mf-step').hide().next().fadeIn('slow');
        }
        else if( age < 18 ){
            console.log('age not eligible < 18');
            $this.parents('.mf-step').hide();
                $('[mf-step="3.1"]').fadeIn('slow');
        }
        else if( age > 75 ){
            console.log('age not eligible > 75');
            $this.parents('.mf-step').hide();
                $('[mf-step="3.2"]').fadeIn('slow');
        }
        else{
            console.log('age not eligible');
                $this.parents('.mf-step').hide();
                $('[mf-step="3.1"]').fadeIn('slow');
            // $('#dob').addClass('error').focus();
                console.log('NAN Value for Date');
        }
    }

    // $('.year-validate').click(function(e){
    //     date_validate();
    // });

    //trigger on keypress enter
    $(document).on('keypress',function(e) {
        if(e.which == 13 && $("#dob").is(":focus") && $('#dob').val().length > 9  ) {
            date_validate();
            console.log('enter keypress detect');
        }
    });
    //password toggle view
    // $('.eye').click(function(){
    $('body').on('click','.eye',function(){
        var get_input = $(this).parent().find('input');
        var get_inputAttr = $(this).parent().find('input').attr('type');
        if( get_inputAttr == 'password' ){
            get_input.attr('type','text');
            $(this).addClass('visible');
        }else{
            get_input.attr('type','password');
            $(this).removeClass('visible');
        }
    });

    //enable six step validation
    $('.validate-for-next').on('change keydown keyup blur',function(){
        console.log('textarea changed');
        if( $(this).val().length ){
            $(this).parents('.mf-step').find('.btn').removeClass('disabled');
        }else{
            $(this).parents('.mf-step').find('.btn').addClass('disabled');
        }
    })

    // //add more validation for next step
    // $('[mf-step="9.c"] input').each(funciton(){

    // })
    function enable9c_btn(){
        // console.log('9.c updating');
        var isValid = 0;
        var total_count = 0;
        $('[mf-step="9.c"] input, [mf-step="9.c"] textarea').each(function() {
            var element = $(this);
            if ( element.val().length ) {
                isValid++;
            }
            total_count++;
        });
        if( total_count == isValid ){
            // console.log('success 9.c');
            $('[mf-step="9.c"] .btn').removeClass('disabled');
        }else{
            $('[mf-step="9.c"] .btn').addClass('disabled');
        }
    }
    $('body').on('change keydown keyup blur','[mf-step="9.c"] input, [mf-step="9.c"] textarea',function(){
        enable9c_btn();
    });






    // $('#dob').on('change',function(){
    //     $this = $(this);
    //     var dob = $('#dob').val();
    //     if(dob != ''){
    //         var str=dob.split('-');
    //         var firstdate=new Date(str[0],str[1],str[2]);
    //         var today = new Date();
    //         var dayDiff = Math.ceil(today.getTime() - firstdate.getTime()) / (1000 * 60 * 60 * 24 * 365);
    //         var age = parseInt(dayDiff);
    //         if( age < 18 ){
    //             console.log('age smaller than 18');
    //             $(this).parents('.mf-step').hide()
    //             $('[mf-step="3.1"]').fadeIn('slow');
    //         }else{
    //             console.log('age greater than 18');
    //             $(this).parents('.mf-step').hide().next().fadeIn('slow');
    //         }
    //     }
    // });

    // Get the modal
    if($('#MF_SIGNUP_MODAL').length > 1){
        var modal = document.getElementById("MF_SIGNUP_MODAL");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
            modal.style.display = "none";
            }
        }

    }
    $('.slider-happens-next').slick({
        dots: false,
        arrows: false,
        infinite: false,
        speed: 300,
        slidesToShow: 3,
        slidesToScroll: 3,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    infinite: false,
                    dots: false
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,
                    dots: true
                }
            },
            {
                breakpoint: 481,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    dots: true,
                }
            }

        ]
    });

    // sgv images
    // jQuery('img.svg').each(function () {
    //     var $img = jQuery(this);
    //     var imgID = $img.attr('id');
    //     var imgClass = $img.attr('class');
    //     var imgURL = $img.attr('src');

    //     jQuery.get(imgURL, function (data) {
    //         // Get the SVG tag, ignore the rest
    //         var $svg = jQuery(data).find('svg');

    //         // Add replaced image's ID to the new SVG
    //         if (typeof imgID !== 'undefined') {
    //             $svg = $svg.attr('id', imgID);
    //         }
    //         // Add replaced image's classes to the new SVG
    //         if (typeof imgClass !== 'undefined') {
    //             $svg = $svg.attr('class', imgClass + ' replaced-svg');
    //         }

    //         // Remove any invalid XML tags as per http://validator.w3.org
    //         $svg = $svg.removeAttr('xmlns:a');

    //         // Replace image with new SVG
    //         $img.replaceWith($svg);

    //     }, 'xml');

    // });

    // slick sinc gallery
    // var slideWrapper= ('.slider-for').slick({
    //     slidesToShow: 1,
    //     slidesToScroll: 1,
    //     arrows: false,
    //     dots: false,
    //     fade: true,
    //     asNavFor: '.slider-nav'
    // });

    $( '.input-text' ).each( function() {
        var $field = $(this).closest('.animate-input');
        if (this.value) {
            $field.addClass('field--not-empty');
        } else {
            $field.removeClass('field--not-empty');
        }
    } );

    $('.input-text').on('input', function() {
        var $field = $(this).closest('.animate-input');
        if (this.value) {
            $field.addClass('field--not-empty');
        } else {
            $field.removeClass('field--not-empty');
        }
    });

    $('#shipping_state').each(function () {
        var $field = $(this).closest('.animate-input');
        if (this.value) {
            $field.addClass('field--not-empty');
        } else {
            $field.removeClass('field--not-empty');
        }
    });

    $('#shipping_state').on('change', function () {
        var $field = $(this).closest('.animate-input');
        if (this.value) {
            $field.addClass('field--not-empty');
        } else {
            $field.removeClass('field--not-empty');
        }
    });

    $('.slider-nav').slick({
        slidesToShow: 3,
        // autoplay: true,
        // autoplaySpeed: 2000,
        slidesToScroll: 1,
        asNavFor: '.slider-for',
        dots: false,
        arrows: false,
        focusOnSelect: true
    });

    $('[href="#review"]').on('shown.bs.tab', function (e) {
        $('.slider-for').resize();
    });

    $('.validate-checkout').click(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 800);
        return false;
    });

    //accordian hide
    $('.btn-acordian ').click(function (e) {
        $('.collapse').collapse('hide');
    });

if ($(window).innerWidth() <= 767) {
    $('.mobile-nav').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        dots: false,
        asNavFor: '.tab-content',
        draggable: false,
        swipe: false
    });


    $('.tab-content').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: false,
        asNavFor: '.mobile-nav',
        adaptiveHeight: true,
         draggable: false,
         swipe: false
        // swipeToSlide: 'false',
        // touchMove: 'false',
        // accesibility: false,
    });
}

    var slideWrapper = $('.slider-for');

    // POST commands to YouTube or Vimeo API
    function postMessageToPlayer(player, command){
    if (player == null || command == null) return;
    player.contentWindow.postMessage(JSON.stringify(command), "*");
    }

    // When the slide is changing
    function playPauseVideo(slick, control){
    var currentSlide, slideType, startTime, player, video;

    currentSlide = slick.find(".slick-current");
    slideType = currentSlide.attr("class").split(" ")[1];
    player = currentSlide.find("iframe").get(0);
    startTime = currentSlide.data("video-start");

    if (slideType === "vimeo") {
    switch (control) {
        case "play":
        if ((startTime != null && startTime > 0 ) && !currentSlide.hasClass('started')) {
            currentSlide.addClass('started');
            postMessageToPlayer(player, {
            "method": "setCurrentTime",
            "value" : startTime
            });
        }
        postMessageToPlayer(player, {
            "method": "play",
            "value" : 1
        });
        break;
        case "pause":
        postMessageToPlayer(player, {
            "method": "pause",
            "value": 1
        });
        break;
    }
    } else if (slideType === "youtube") {
    switch (control) {
        case "play":
        postMessageToPlayer(player, {
            "event": "command",
            // "func": "mute"
        });
        postMessageToPlayer(player, {
            "event": "command",
            "func": "playVideo"
        });
        break;
        case "pause":
        postMessageToPlayer(player, {
            "event": "command",
            "func": "pauseVideo"
        });
        break;
    }
    } else if (slideType === "video") {
    video = currentSlide.children("video").get(0);
    if (video != null) {
        if (control === "play"){
        video.play();
        } else {
        video.pause();
        }
    }
    }
    }

    // Resize player
    function resizePlayer(iframes, ratio) {
        if (!iframes[0]) return;
        var win = $(".main-slider"),
            width = win.width(),
            playerWidth,
            height = win.height(),
            playerHeight,
            ratio = ratio || 16/9;

        iframes.each(function(){
            var current = $(this);
            if (width / ratio < height) {
                playerWidth = Math.ceil(height * ratio);
                current.width(playerWidth).height(height).css({
                left: (width - playerWidth) / 2,
                    top: 0
                });
            } else {
                playerHeight = Math.ceil(width / ratio);
                current.width(width).height(playerHeight).css({
                left: 0,
                top: (height - playerHeight) / 2
                });
                }
            });
    }

    // DOM Ready
    $(function() {
        // Initialize
        slideWrapper.on("init", function(slick){
            slick = $(slick.currentTarget);
            setTimeout(function(){
                playPauseVideo(slick,"play");
            }, 1000);
            // resizePlayer(iframes, 16/9);
        });
        slideWrapper.on("beforeChange", function(event, slick) {
            slick = $(slick.$slider);
            playPauseVideo(slick,"pause");
        });
        slideWrapper.on("afterChange", function(event, slick) {
            slick = $(slick.$slider);
            playPauseVideo(slick,"play");
        });

        //start the slider
        slideWrapper.slick({
        lazyLoad:"progressive",
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: false,
        fade: true,
        asNavFor: '.slider-nav',
        cssEase:"cubic-bezier(0.87, 0.03, 0.41, 0.9)"
        });
    });

    $('.readmore-xs .show-content').click(function () {
        $('.readmore-xs-content').toggle();
        $('.readmore-xs').toggleClass('on');
    });

    $( '.subscription-options' ).on( 'change', function() {

        $sync_val  = $(this).val();

        $('.wcsatt-options-product input[value="' + $sync_val + '"]').prop( 'checked', true );

    } );

    $( '.variation-sync-variation' ).on( 'change', function() {

        $( '#pa_pack-size' ).val( $(this).val() ).trigger('change');

        $sync_val  = $('.subscription-options:checked').val();

        $('.wcsatt-options-product input[value="' + $sync_val + '"]').prop( 'checked', true );

    } );

    $( '#hc-order-checkout' ).on( 'click', function( e ) {

        e.preventDefault();

        // var allergy_text_validate = $('#allergy-text').validate({
        //     rules: {
        //         'allergies_details': {
        //             required: true
        //         }
        //     }
        // });

        // if ( allergy_text_validate.form() ) {
            $( '#hisclinic-order-details .single_add_to_cart_button' ).trigger( 'click' );
        // }

    }  );

});


function priceUpdate(){
    $('span.variation-price').html( $('del span.woocommerce-Price-amount.amount').html() );
    // if( $('input[value="1_month"]').is(':checked') ){
    //     $('span.variation-price').html( $('ins span.woocommerce-Price-amount.amount').html() );
    // }else{
    //     $('span.variation-price').html( $('del span.woocommerce-Price-amount.amount').html() );
    // }
}

jQuery(window).on( 'load', function() {
    priceUpdate();
} );

jQuery(function($){

    var checkout_form_validation;
    
    $( '.validate-checkout' ).click( function() {
        var form = $( "form.checkout.woocommerce-checkout" );

        var rules = {
            rules: {
                billing_first_name: {
                    required: true
                },
                billing_last_name: {
                    required: true
                },
                billing_phone: {
                    required: true,
                    number: true,
                },
                billing_address_1: {
                    required: true
                },
                billing_city: {
                    required: true
                },
                billing_state: {
                    required: true
                },
                billing_postcode: {
                    required: true,
                    number: true,
                }
            }
        };

        if ( $( '#ship-to-different-address-checkbox' ).is(':checked') ) {
            rules['rules']['shipping_first_name'] = {
                required: true
            };
            
            rules['rules']['shipping_last_name'] = {
                required: true
            };

            rules['rules']['shipping_address_1'] = {
                required: true
            };

            rules['rules']['shipping_state'] = {
                required: true
            };

            rules['rules']['shipping_city'] = {
                required: true
            };

            rules['rules']['shipping_postcode'] = {
                required: true
            };

            $( '#shipping-locale' ).html( $('#shipping_city').val() );

        } else {
            $( '#shipping-locale' ).html( $('#billing_city').val() );
        }

        if (checkout_form_validation !== undefined) {
            checkout_form_validation.destroy();
        }
        
        checkout_form_validation = form.validate(rules);
        //  console.log('validate run');
        //  console.log(form.valid());
         if( form.valid() ){
            //  console.log('validated and goint to next step');
             $(this).parents('.mf-step').hide().next('.mf-step').fadeIn('slow');
         }else{
            $('html,body').animate({
                scrollTop: $('.woocommerce-billing-fields').offset().top - 140
            },400)
         }

    } );

    //pricetag update
    priceUpdate();
    $('.product-order-detail-table input').on( 'change', function(){
        priceUpdate();
    });

    // $('input[name="var_subscriptions_options"]').on( 'click touchstart', function(){

        
    //     if( $('input[value="1_month"]').prop('checked') ){
    //         $('input[value="0"]').prop('checked',true);
    //         priceUpdate();
    //     }else{
    //         priceUpdate();
    //     }

    // });
    $('body').on('click','#toggle-mo',function(){
        $('.subscription-options').not(':checked').parent().click();
        if( $('.subscription-options:checked').val() == '1_month' ){
            $('#toggle-mo').addClass('is-active');
        }else{
            $('#toggle-mo').removeClass('is-active');        
        }
        console.log('clicked' + $('.subscription-options:checked').val() );
    });

    if ($('.woocommerce-error').length > 0) {

		$('html, body').animate({
			scrollTop: ($('.woocommerce-error').offset().top - 200)
		}, 1000);

	}

     //value bind to label.
    $(document).on('change keyup', "*[bind]", function(e) {
        var to_bind = $(this).attr('bind');
        var value = ('' != $(this).val()) ? $(this).val() : 'Untitled';
        $("*[bind='" + to_bind + "']").html(value);
        $("*[bind='" + to_bind + "']").val($(this).val());
    });

    $( '#triggerApplyCoupon' ).on( 'click', function(e) {
        e.preventDefault();
        $('form.checkout_coupon.woocommerce-form-coupon #ApplyCouponChcoutform').trigger('click');
        clinic.move_to('.woocommerce');
    } );

	$('[mf-step="1"] .mf-next').click(function() {
		dataLayer.push({
			event: 'eligibililtyBrowse'
		});
	});

	$('[mf-step="2"] .img-radio-item label').click(function() {
		dataLayer.push({
			event: 'ChooseGender',
			gender: $(this).siblings('input').val()
		});
	});

	$('[mf-step="4"] .mf-next').click(function() {
		var height,
			weight;

		if ( $('[mf-step="4"] input[name="heightchk"]:checked').length ) {
			height = 'I do not know';
		} else {
			height = $('[mf-step="4"] input[name="height"]').val();
		}
		if ( $('[mf-step="4"] input[name="weightchk"]:checked').length ) {
			weight = 'I do not know';
		} else {
			weight = $('[mf-step="4"] input[name="weight"]').val();
		}
		dataLayer.push({
			event: 'heightWeight',
			height: height,
			weight: weight
		});
	});

	$('[mf-step="5"] .img-radio-item label').click(function() {
		dataLayer.push({
			event: 'diet',
			diet: $(this).siblings('input').val()
		});
	});

	$('[mf-step="5a"] .radio-btn label').click(function() {
		dataLayer.push({
			event: 'anticipatedUsageFrequency',
			anticipatedUsageFrequency: $(this).siblings('input').val()
		});
	});

	$('[mf-step="6"] .radio-btn label').click(function() {
		dataLayer.push({
			event: 'erectionProblems',
			erectionProblems: $(this).siblings('input').val()
		});
	});

	$('[mf-step="7"] .radio-btn label').click(function() {
		dataLayer.push({
			event: 'previouslySubscribedOrApproved',
			previouslySubscribedOrApproved: $(this).siblings('input').val()
		});
	});

	$('[mf-step="7.1"] .radio-btn label').click(function() {
		dataLayer.push({
			event: 'heartDisease',
			heartDisease: $(this).siblings('input').val()
		});
	});

	$('[mf-step="8"] .mf-stop').click(function() {
		dataLayer.push({
			event: 'previouslyUsedAnyProduct',
			previouslyUsedAnyProduct: 'None'
		});
	});
	$('[mf-step="8"] .mf-products').click(function() {
		var products = '';
		$('[mf-step="8"] input:checked').each(function() {
			products += $(this).val() + ', ';
		});
		dataLayer.push({
			event: 'previouslyUsedAnyProduct',
			previouslyUsedAnyProduct: products
		});
	});
	$('.mf-product[data-product="1"] .radio-btn label').click(function() {
		dataLayer.push({
			event: 'wasSildenafilEffective',
			wasSildenafilEffective: $(this).siblings('input').val()
		});
	});
	$('.mf-product[data-product="2"] .radio-btn label').click(function() {
		dataLayer.push({
			event: 'wasCialisEffective',
			wasCialisEffective: $(this).siblings('input').val()
		});
	});
	$('.mf-product[data-product="3"] .radio-btn label').click(function() {
		dataLayer.push({
			event: 'wasDailyCialisEffective',
			wasDailyCialisEffective: $(this).siblings('input').val()
		});
	});

	$('[mf-step="9"] .radio-btn label').click(function() {
		dataLayer.push({
			event: 'healthConditionsOrPriorSurgeries',
			healthConditionsOrPriorSurgeries: $(this).siblings('input').val()
		});
	});

	$('[mf-step="9.1"] .radio-btn label').click(function() {
		dataLayer.push({
			event: 'nitrateMedications',
			nitrateMedications: $(this).siblings('input').val()
		});
	});

	$('[mf-step="9.3"] .radio-btn label').click(function() {
		dataLayer.push({
			event: 'bloodPressureChecked',
			bloodPressureChecked: $(this).siblings('input').val()
		});
	});

	$('[mf-step="9.4"] .mf-next').click(function() {
		dataLayer.push({
			event: 'diagnosedWithHypertensionOrHypotension',
			diagnosedWithHypertensionOrHypotension: 'No, it was normal'
		});
	});
	$('[mf-step="9.4"] .mf-stop').click(function() {
		dataLayer.push({
			event: 'diagnosedWithHypertensionOrHypotension',
			diagnosedWithHypertensionOrHypotension: $('[mf-step="9.4"] input:checked').val()
		});
	});

	$('[mf-step="9.5"] .radio-btn label').click(function() {
		dataLayer.push({
			event: 'frequentlyExperienceLightheadedness',
			frequentlyExperienceLightheadedness: $(this).siblings('input').val()
		});
	});

	$('[mf-step="10"] .mf-next').click(function() {
		dataLayer.push({
			event: 'cardiovascularSymptoms',
			cardiovascularSymptoms: 'None apply'
		});
	});
	$('[mf-step="10"] .mf-stop').click(function() {
		var diagnosis = '';
		$('[mf-step="10"] input:checked').each(function() {
			if ( $(this).val() != 'None' ) {
				diagnosis += $(this).val() + ', ';
			}
		});
		dataLayer.push({
			event: 'cardiovascularSymptoms',
			cardiovascularSymptoms: diagnosis
		});
	});

	$('[mf-step="11"] .radio-btn label').click(function() {
		dataLayer.push({
			event: 'heartAttackLastSixMonths',
			heartAttackLastSixMonths: $(this).siblings('input').val()
		});
	});

	$('[mf-step="12"] .radio-btn label').click(function() {
		dataLayer.push({
			event: 'everHadStrokeOrTia',
			everHadStrokeOrTia: $(this).siblings('input').val()
		});
	});

	$('[mf-step="13"] .mf-next').click(function() {
		dataLayer.push({
			event: 'everHadTheFollowingConditions',
			everHadTheFollowingConditions: 'None apply'
		});
	});
	$('[mf-step="13"] .mf-stop').click(function() {
		var diagnosis = '';
		$('[mf-step="13"] input:checked').each(function() {
			if ( $(this).val() != 'None' ) {
				diagnosis += $(this).val() + ', ';
			}
		});
		dataLayer.push({
			event: 'everHadTheFollowingConditions',
			everHadTheFollowingConditions: diagnosis
		});
	});

	$('[mf-step="14"] .btn').click(function() {
		var diagnosis = 'None apply';

		if ( $(this).hasClass('mf-stop') ) {
			diagnosis = '';
			$('[mf-step="14"] input:checked').each(function() {
				if ( $(this).val() != 'None' ) {
					diagnosis += $(this).val() + ', ';
				}
			});
		}
		dataLayer.push({
			event: 'everHadTheFollowingConditions',
			everHadTheFollowingConditions: diagnosis
		});
	});

	/*Allergies 7-1-2019*/
    $( '.allergies-options' ).on( 'change', function() {

        if ( 'yes' == $(this).val() ) {
            $( '.detail-textarea' ).slideDown();
            // var allergy_text_validate = $('#allergy-text').validate({
            //     rules: {
            //         'allergies_details': {
            //             required: true
            //         }
            //     }
            // });
            // allergy_text_validate.form();
        } else {
            $( '.detail-textarea' ).slideUp();
        }
        $( '#allergies_check_hin' ).val( $(this).val() );
    } );

    // google MAPS
    // <script
    // src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

    var placeSearch, autocomplete;

    var componentForm = {
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'short_name',
        postal_code: 'short_name'
    };

    for (var component in componentForm) {

        if ( $( "input[data-autofill='"+ component +"']").val() === '' ) {

            // $( "input[data-autofill='"+ component +"']").parents( '.animate-input' ).hide();

        }
    }

    function initAutocomplete(id, callback) {
        // Create the autocomplete object, restricting the search predictions to geographical location types.
        var instance = new google.maps.places.Autocomplete(
            document.getElementById(id), {
                types: ['geocode'],
                componentRestrictions: {
                    country: 'au'
                }
            }
        );

        // Avoid paying for data that you don't need by restricting the set of place fields that are returned to just the address components.
        instance.setFields(['address_component']);

        // Callback on place changed
        instance.addListener('place_changed', function() {
            callback(this);
        });

        return instance;
    }

    // Autocompleting Billing Suburb for suburb, state and postcode
    if ($('#billing_city').length){
        initAutocomplete('billing_city', function(instance) {
            var place = instance.getPlace();

            if (place != undefined) {
                if (place.address_components.length) {
                    $.each(place.address_components, function (i, component) { 
                        if (component.types.indexOf('locality') != -1) {
                            $('#billing_city').val(component.long_name);
                            return;
                        }

                        if (component.types.indexOf('postal_code') != -1) {
                            $('#billing_postcode').val(component.short_name);
                            return;
                        }
                        
                        if (component.types.indexOf('administrative_area_level_1') != -1) {
                            $('#billing_state').val(component.short_name);
                            return;
                        }
                    });
                }
            }
            
            $( 'body' ).trigger( 'update_checkout' );
        });
    }

    // Autocompleting Shipping Suburb for suburb, state and postcode
    if ($('#shipping_city').length){
        initAutocomplete('shipping_city', function(instance) {
            var place = instance.getPlace();

            if (place != undefined) {
                if (place.address_components.length) {
                    $.each(place.address_components, function (i, component) { 
                        if (component.types.indexOf('locality') != -1) {
                            $('#shipping_city').val(component.long_name);
                            return;
                        }

                        if (component.types.indexOf('postal_code') != -1) {
                            $('#shipping_postcode').val(component.short_name);
                            return;
                        }
                        
                        if (component.types.indexOf('administrative_area_level_1') != -1) {
                            $('#shipping_state').val(component.short_name);
                            return;
                        }
                    });
                }
            }
            
            $( 'body' ).trigger( 'update_checkout' );
        });
    }

    // Bias the autocomplete object to the user's geographical location,
    // as supplied by the browser's 'navigator.geolocation' object.
    function geolocate() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
            var geolocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            var circle = new google.maps.Circle(
                {center: geolocation, radius: position.coords.accuracy});
            autocomplete.setBounds(circle.getBounds());
            });
        }
    }

    // $( '#shipping_address_1' ).on( 'change', function() {

    //     // console.log('chdb');

    //     if ( $('#shipping-locale').length > 0 ) {

    //         $( '#shipping-locale' ).html( $(this).val() );

    //     }

    // } );

    // $( '#shipping-locale' ).html( $('#billing_address_1').val() );

});

$( '#prod-order-details .validate-checkout' ).click( function(e) {

    console.log( 'updated_checkout' );
    $( 'body' ).trigger( 'update_checkout' );

} );


//menu resize top 
function topMenu(){
    $('.res-menu').css('top',$('header.main-header').outerHeight());
}

$(function(jQuery){
    if( $(window).innerWidth() < 992 ){
        topMenu();
        $(window).resize( topMenu );
        // console.log('yes below');
    }else{
        $('.res-menu').css('top',"");
        // console.log('no below');
    }

    //on sldier init
    if( $(window).innerWidth() <=1260 ){
        var getActive = $('.woocommerce-account ul.navigation li.is-active').attr('data-slick-index');
        $('.woocommerce-account ul.navigation').on('init', function(event, slick){
            setTimeout(function(){ 
                var getActive = $('.woocommerce-account ul.navigation li.is-active').attr('data-slick-index');
                console.log( Math.abs(getActive));
                $('.woocommerce-account ul.navigation').slick('slickGoTo', ' ' + Math.abs(getActive) + ',true');
            }, 1000);
        });
    }
    //navigation account slider
    if( $(window).innerWidth() <=1260 ){
        $('.woocommerce-account ul.navigation').slick({
            slidesToShow: 3,
            slidesToScroll: 1,
            dots: false,
            arrows: false,
            infinite: true,
            variableWidth: true
            // centerMode: false,
            // centerPadding: '60px'
        });
    }

    
});

jQuery(function($){
    $('body').on('click','.treatment-change-request',function (e) {
        
        e.preventDefault();

        form = $(this).parent( '.treatment-change-form' );

        form.validate();
        
        if( form.valid() ){

            form.hide();
            $( '.loading-spinner' ).show();
            
            formdata = $(this).parent( '.treatment-change-form' ).serializeArray();
    
            $.post(wp_paths.admin, formdata, function (data, textStatus, jqXHR) {

                $( '.loading-spinner' ).hide();
                
                if (data.data.success) {
                    
                    $( '.treatment-change-message' ).addClass('success');
                    $( '.treatment-change-message' ).html( data.data.message );
            
                } else {
        
                    $( '.treatment-change-message' ).addClass('error');
                    $( '.treatment-change-message' ).html( data.data.message );
                }
            }, 'json');
        } else {
            
        }
    
    } );

    $( '#hc-send-chat-message' ).on( 'submit', function(e) {

        e.preventDefault();

        data = $(this).serializeArray();

        // console.log ( data );

        $( '#hc-send-chat-submit' ).attr( 'disabled', 'disabled' ).text('Sending...');

        $.post(wp_paths.admin, data, function (data, textStatus, jqXHR) {

            console.log(data);
            
            if (data.success) {
                
                $( '#msg-reply' ).val('');
                $('#hc-send-chat-submit').removeAttr( 'disabled' ).text('Send Message');

                var template = wp.template('hc-chat-block');
                // var rand = Math.floor(Math.random() * (999 - 10 + 1)) + 10;
                $('#chat-blocks-wrap').append(template({ data: data }));

                window.dispatchEvent(new Event('resize'));
        
            } else {
                
                

            }
        }, 'json');

    } );

});



// Medical Account dahsboard
$(function(){
    //textarea toggle
    $('#hc-account-dashbrd-md .toggle-below-yes').click(function(){
        $(this).parents('.mf-step').next().slideDown();
    });
    $('#hc-account-dashbrd-md .toggle-below-no').click(function(){
        $(this).parents('.mf-step').next().slideUp();
        $(this).parents('.mf-step').next().find('textarea, input').val('');
    });    

    //value none value toggle
    $('#hc-account-dashbrd-md .mf-step__chkbox [value="None"][type="checkbox"] + label').click(function(){
        $this = $(this);
        $this.parents('.mf-step').find('input:not([value="None"])').prop('checked', false);
    });

    $('#hc-account-dashbrd-md .mf-step__chkbox input:not([value="None"]) + label').click(function(){
        $this = $(this);
        $this.parents('.mf-step').find('input[value="None"]').prop('checked', false);
    });

	$('.autoplay').slick({
		dots: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		autoplay: true,
		autoplaySpeed: 3000,
		variableWidth: true
	});

    
	
	var theMenu = $("#sticky ul li a").find(".open-sub");
		
	$('.open-page').parent().on('click', function(e) {
	    e.preventDefault();
	});
	
			
	$("#sticky .open-page").click(function() {			
		var href = $(this).parent();
		var link = href.attr('href');			
		window.location = link;		
	});
		
	$("#sticky .open-sub").click(function() {	
		var a =	$(this);		
		var b = $(this).parent().parent().find(".sub-menu");		
		
		$(a).addClass('close');	
		
			
		if ($(b).is(":hidden")){				
			$(b).addClass('open');	
			$(a).addClass('close');			
		}else {				
			$(b).removeClass('open');
			$(a).removeClass('close');			
		}
	});		
	

		
			
	$("#menusticky .open-page").click(function() {			
		var href = $(this).parent();
		var link = href.attr('href');			
		window.location = link;		
	});
		
	$("#menusticky .open-sub").click(function() {	
		var a =	$(this);		
		var b = $(this).parent().parent().find(".sub-menu");		
		
		$(a).addClass('close');	
		
			
		if ($(b).is(":hidden")){				
			$(b).addClass('open');	
			$(a).addClass('close');			
		}else {				
			$(b).removeClass('open');
			$(a).removeClass('close');			
		}
	});	

	

    
	$( ".seo-section" ).mouseover(function() {		
		$(this).find('li').hover(
		    function(){ 
			    $(this).parentsUntil( '.seo-section' ).find('h2, .read-more').addClass('hover'); $(this).addClass('hover') 
			},
		    function(){ 
			    $(this).parentsUntil( '.seo-section' ).find('h2, .read-more').removeClass('hover'); $(this).removeClass('hover') 
			}
		)			
		$(this).find('a').hover(
		    function(){ 			    
			    $(this).parentsUntil( '.seo-section' ).find('h2').addClass('hover')		    
		    },
		    function(){ 
			    $(this).parentsUntil( '.seo-section' ).find('h2').removeClass('hover')  
			}
		)			  
	}); 
    
	// SEO NEXT PAGE SIDEBAR MENU

	function sticky_relocate() {
        if ( $("#menu-stops").length > 0 ) {
            var window_top = $(window).scrollTop();
            var footer_top = $("#menu-stops").offset().top;
            var div_top = $('#sticky-anchor').offset().top;
            var div_height = $("#menusticky").height();
        
            if (window_top + div_height > footer_top)
                $('#menusticky').removeClass('stick');    
            else if (window_top > div_top) {
                $('#menusticky').addClass('stick');
            } else {
                $('#menusticky').removeClass('stick');
            }
        }
    }

    jQuery.validator.addClassRules('mask-date', {
		required: true,
		// date: true,
		dateFormat: true
    });
    
    $( '#admin-update-p' ).validate();
	
	jQuery.validator.addMethod(
		"dateFormat",
		function(value, element) {

            // return true;

			var check = false;
			var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
				if( re.test(value)){
					var adata = value.split('/');
					var dd = parseInt(adata[0],10);
					var mm = parseInt(adata[1],10);
					var yyyy = parseInt(adata[2],10);
					var xdata = new Date(yyyy,mm-1,dd);
					if ( ( xdata.getFullYear() === yyyy ) && ( xdata.getMonth () === mm - 1 ) && ( xdata.getDate() === dd ) ) {
					check = true;
				}
				else {
					check = false;
				}
			} else {
			check = false;
			}
			return this.optional(element) || check;
		},
		"Wrong date format"
	);

	$(function () {
        $(window).scroll(sticky_relocate);
        if ( $("#menu-stops").length > 0 ) {
            sticky_relocate();
        }
	});

	$("#down").click(function(){
	    $('html, body').animate({
        	scrollTop: $("#section-2").offset().top
	    }, 800);	
	});
    
    // Flag messages as read
    $('#support-queries-tab').click(function(e) {
        var user_id = $(this).data('id');

        if (!user_id) {
            return false;
        }

        var params = {
            action: 'hc_queries_seen',
            user_id: user_id
        };

        $.post(wp_paths.admin, params, function (data, textStatus, jqXHR) {
            // Nothing to do
        }, 'json');
    });
});

jQuery(function($){
	function medical_history_updates_validate() {
        var form = $('#account-medical-form');

        var rules = {
            ignore:'',
			rules: {
                // Personal Information
                'medical_form_details[personal_information][date_of_birth][answer]': {
                    required: true,
                    validAge: true
                },
                'medical_form_details[personal_information][height][answer]': {
                    required: function(element) {
                        return $('input[name="medical_form_details[personal_information][height_no_info][answer]"]:checked').length < 1;
                    }
                },
                'medical_form_details[personal_information][weight][answer]': {
                    required: function(element) {
                        return $('input[name="medical_form_details[personal_information][weight_no_info][answer]"]:checked').length < 1;
                    }
                },
                'medical_form_details[personal_information][diet][answer]': {
                    required: true
                },

                // Sexual Activity
                'medical_form_details[sexual_activity][uses][answer]': {
                    required: true
                },
                'medical_form_details[sexual_activity][erection][answer]': {
                    required: true
                },

                // Medical History
				'medical_form_details[medical_history][heart_disease][answer]': {
					required: true
				},
				'medical_form_details[medical_history][medical_condition][answer]': {
					required: true
				},
				'medical_form_details[medical_history][medical_condition_description][answer]': {
					required: function(element) {
						return $('input[name="medical_form_details[medical_history][medical_condition][answer]"]:checked').val() == 'Yes';
					}
				},
				'medical_form_details[medical_history][medical_history][answer]': {
					required: true
				},
				'medical_form_details[medical_history][allergies][answer]': {
					required: true
				},
				'medical_form_details[medical_history][allergies_description][answer]': {
					required: function(element) {
						return $('input[name="medical_form_details[medical_history][allergies][answer]"]:checked').val() == 'Yes';
					}
				},
				'medical_form_details[medical_history][nitrate][answer]': {
					required: true
				},
				'medical_form_details[medical_history][herbs][answer]': {
					required: true
				},
				'medical_form_details[medical_history][herbs_description][answer]': {
					required: function(element) {
						return $('input[name="medical_form_details[medical_history][herbs][answer]"]:checked').val() == 'Yes';
					}
				},
				'medical_form_details[medical_history][blood_pressure_test][answer]': {
					required: true
				},
				'medical_form_details[medical_history][blood_pressure_diagnosis][answer]': {
					required: function(element) {
						return $('input[name="medical_form_details[medical_history][blood_pressure_test][answer]"]:checked').val() == 'Yes';
					}
				},
				'medical_form_details[medical_history][lightheadedness][answer]': {
					required: true
				},
				'medical_form_details[medical_history][cardiovascular_symptoms][answer]': {
					require_from_group: [1, ".cardiovascular-symptoms-group"]
				},
				'medical_form_details[medical_history][cardiovascular_symptoms][0][answer]': {
					require_from_group: [1, ".cardiovascular-symptoms-group"]
				},
				'medical_form_details[medical_history][cardiovascular_symptoms][1][answer]': {
					require_from_group: [1, ".cardiovascular-symptoms-group"]
				},
				'medical_form_details[medical_history][cardiovascular_symptoms][2][answer]': {
					require_from_group: [1, ".cardiovascular-symptoms-group"]
				},
				'medical_form_details[medical_history][cardiovascular_symptoms][3][answer]': {
					require_from_group: [1, ".cardiovascular-symptoms-group"]
				},
				'medical_form_details[medical_history][heart_attack_past][answer]': {
					required: true
				},
				'medical_form_details[medical_history][stroke_TIA][answer]': {
					required: true
				},
				'medical_form_details[medical_history][conditions_1][answer]': {
					require_from_group: [1, ".conditions-1-group"]
				},
				'medical_form_details[medical_history][conditions_1][0][answer]': {
					require_from_group: [1, ".conditions-1-group"]
				},
				'medical_form_details[medical_history][conditions_1][1][answer]': {
					require_from_group: [1, ".conditions-1-group"]
				},
				'medical_form_details[medical_history][conditions_1][2][answer]': {
					require_from_group: [1, ".conditions-1-group"]
				},
				'medical_form_details[medical_history][conditions_1][3][answer]': {
					require_from_group: [1, ".conditions-1-group"]
				},
				'medical_form_details[medical_history][conditions_1][4][answer]': {
					require_from_group: [1, ".conditions-1-group"]
				},
				'medical_form_details[medical_history][conditions_2][answer]': {
					require_from_group: [1, ".conditions-2-group"]
				},
				'medical_form_details[medical_history][conditions_2][0][answer]': {
					require_from_group: [1, ".conditions-2-group"]
				},
				'medical_form_details[medical_history][conditions_2][1][answer]': {
					require_from_group: [1, ".conditions-2-group"]
				},
				'medical_form_details[medical_history][conditions_2][2][answer]': {
					require_from_group: [1, ".conditions-2-group"]
				},
				'medical_form_details[medical_history][conditions_2][3][answer]': {
					require_from_group: [1, ".conditions-2-group"]
				}
            },
            errorPlacement: function(error, element) {
				error.insertBefore(element);
			},
            invalidHandler: function(event, validator) {
				var errors = validator.errorList;

				if (errors.length) {
                    $errors_list = $('#account-medical-form-errors .fields');
                    $errors_list.html('');
                    var errors_lines = [];

                    $.each(errors, function (key, value) { 
                        var $mf_field = $(value.element).parents('.mf-field').first();
                        var question = $mf_field.data('question');

                        if (errors_lines.indexOf(question) == -1) {
                            errors_lines.push(question);

                            var $li = $('<li><a href="#">' + question + '</a></li>');

                            $li.click(function (e) { 
                                e.preventDefault();
                                
                                $('.accordion').not('.active').find('.title').click();
                                
                                setTimeout(function() {
                                    clinic.move_to($mf_field);
                                }, 300);
                            });
                            
                            $errors_list.append($li);
                        }
                    });
                    
					$('#account-medical-form-errors').show();

					$('html, body').animate({
						scrollTop: $('#account-medical-form-errors').offset().top - $('.main-header').height()
					});
				} else {
					$('#account-medical-form-errors').hide();
				}
			}
        };

		$.each($('.medical-history-description input, .medical-history-description textarea'), function(){
			rules['rules'][$(this).attr('name')] = {
				required: function(element) {
					return $('input[name="medical_form_details[medical_history][medical_history][answer]"]:checked').val() == 'Yes';
				}
			};
		});

		var validator = form.validate(rules);

		$('#add-more span').click(function(){
			validator.destroy();

			medical_history_updates_validate();
        });
    }
    
    medical_history_updates_validate();

    $( '#hc-send-chat-message-backend' ).on( 'submit', function(e) {

        e.preventDefault();

        data = $(this).serializeArray();

        // console.log ( data );

        $( '#hc-send-chat-submit' ).attr( 'disabled', 'disabled' ).text('Sending...');

        $.post(new_wp_paths.new_admin, data, function (data, textStatus, jqXHR) {

            // console.log(data);
            
            if (data.success) {
                
                $( '#msg-reply' ).val('');
                $('#hc-send-chat-submit').removeAttr( 'disabled' ).text('Send Message');

                var template = wp.template('hc-chat-block');
                // var rand = Math.floor(Math.random() * (999 - 10 + 1)) + 10;
                $('#chat-blocks-wrap').append(template({ data: data }));
        
            } else {
                
                

            }
        }, 'json');

    } );
    
});