var $ = jQuery;

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
                            $('#step-4 .under-age').hide();
                        } else {
                            $('#step-4 .approved').hide();
                            $('#step-4 .not-approved').show();
                            $('#step-4 .under-age').hide();
                        }

					   var today = new Date();
					   var dob = $('.dob').val();
					   dob = dob.split('/');
					   var birthDate = new Date(dob[2] + '-' + dob[1] + '-' + dob[0]);
					   var age = today.getFullYear() - birthDate.getFullYear();
					   var m = today.getMonth() - birthDate.getMonth();
					   if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
					   	  age--;
					   }

					   if ( age < 18 ) {
						   $('#step-4 .under-age-hide').hide();
                            $('#step-4 .under-age').show();
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

        $('.dob').mask('00/00/0000');
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
            var $parent = $(this).parent();

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

		$('#account_first_name, #account_last_name').blur(function() {
			$('#account_display_name').val($('#account_first_name').val() + ' ' + $('#account_last_name').val());
		});

        $('.account-content select').select2();
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
            "Please enter a date in the format dd/mm/yyyy."
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
                    $a.attr('href', wp_paths.home_url + '/shop');
                    $a.text('See Treatments');
                    //$a.addClass('block');
                }
            });
        }
    });

    return {
    };
}) (window, document);

// ========================== //
//     Multistep form         //
//  ==========================//

jQuery(function($){
	var last_step = 1;

	// Skip step 1 if #lp is in URL
	if ( window.location.href.indexOf('#lp') > 1 ) {
		$('[mf-step="1"]').hide();
		$('[mf-step="2"]').fadeIn();
	}

	// Don't allow form submission with ENTER
	$("#new-medical-form").bind("keypress", function (e) {
		if (e.keyCode == 13) {
			return false;
		}
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
        if( ($('.mf-height input:checked').length || $('.mf-height input').val().length) && ($('.mf-weight input:checked').length || $('.mf-weight input').val().length) ){
            //console.log('at least one val is true');
            $(this).parents('.mf-step').find('.btn').removeClass('disabled');
        }else{
            $(this).parents('.mf-step').find('.btn').addClass('disabled');
            //console.log('at least one val is true');
        }
    });

    //goto next
    $('.mf-next').click(function(){
	    last_step = $(this).parents('.mf-step').attr('mf-step');
	    //console.log(last_step);

        $(this).parents('.mf-step').hide().next('.mf-step').fadeIn('slow', function() {
	        gtm_not_qualified_check($(this));
        });

        //offset top
        if( $(window).innerWidth() < 991 ){
            $('html,body').animate({
                scrollTop: $('#mf-app').offset().top - 80
            },500);
        }
    });

    //validate
    // $('.mf-next, [mf-step="7.2"] .btn').click(function(){
    //     cusValidator.form();
    // });

    //previous step
    $('.mf-prev').click(function(){
        if ( $(this).parents('.mf-step').find('.mf-content__sorry').length ) {
	        //console.log('back to: ' + last_step);
	        	$(this).parents('.mf-step').hide();
			$('[mf-step="' + last_step + '"]').fadeIn('slow');
        } else {
	        $(this).parents('.mf-step').hide().prev('.mf-step').fadeIn('slow');
		}
    });

    //stop steps
    $('.mf-stop').click(function(){
        //console.log('mf-stop-clicked')
        if ( $(this).parents('.mf-step').find('.mf-content__sorry').length ) {
	        //console.log('back to: ' + last_step);
	        	$(this).parents('.mf-step').hide();
			$('[mf-step="' + last_step + '"]').fadeIn('slow');
        } else {
	        var get_mfStep = $(this).attr('goto-step');
	        last_step = $(this).parents('.mf-step').attr('mf-step');
	        $(this).parents('.mf-step').hide()
	        $('[mf-step="' + get_mfStep + '"]').fadeIn('slow', function() {
		        gtm_not_qualified_check($(this));
	        });

	        //console.log(last_step);
		}
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
        //console.log('products steps triggered');

        //toggle products page or next step
        var is_chkChkd = $this.parents('.mf-step__chkbox__validate').find('input:checked').length;
        //console.log('checkbox checked : ' + is_chkChkd );
        if( is_chkChkd > 0 ){
            $this.parents('.mf-step__chkbox__validate').find('.btn.mf-stop').hide();
            $this.parents('.mf-step__chkbox__validate').find('.btn.mf-products').show();
            //console.log('more products checked' );
        }else{
            $this.parents('.mf-step__chkbox__validate').find('.btn.mf-stop').show();
            $this.parents('.mf-step__chkbox__validate').find('.btn.mf-products').hide();
            //console.log('no products checked' );
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
        //console.log('hypertension radio');
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
        //console.log('only checkbox without valdiate');
        if( $(this).parents('.mf-step__chkbox').find('input:checked').length ){
            //console.log('chk chked');
            $(this).parents('.mf-step__chkbox').find('.btn').text('Continue');
        }else{
            //console.log('chk not chked');
            $(this).parents('.mf-step__chkbox').find('.btn').text('None apply');
        }
    });

    //skip step if none selected
    $('.mf-step__stop input').change(function(){
        //console.log('mf-step__stop validate');
        if( $(this).parents('.mf-step__chkbox').find('input:checked').length > 1 ){
        //    alert('true');
            $(this).parents('.mf-step__chkbox').find('.btn.mf-next').hide();
            $(this).parents('.mf-step__chkbox').find('.btn.mf-stop').show();
        }else{
            //console.log('chk not chked');
            // alert('false');
            $(this).parents('.mf-step__chkbox').find('.btn.mf-stop').hide();
            $(this).parents('.mf-step__chkbox').find('.btn.mf-next').show();
        }
    });

    //toggle final empty check form
    $('[mf-step="14"] .mf-step__stop input').change(function(){
        //console.log('final mf-step__stop validate');
        if( $(this).parents('.mf-step__chkbox').find('input:checked').length > 1 ){
            //console.log('chk chked');
            $('[mf-step="14"]').find('.btn').addClass('mf-stop').removeClass('goPopup').attr('goto-step',11.1);
            $('[mf-step="14"]').find('.btn').text('Continue');
        }else{
            $('[mf-step="14"]').find('.btn').text('None apply');
            //console.log('final chk not chked');
            if(  $('[mf-step="9"] .yes:checked').length ){
                $('[mf-step="14"]').find('.btn').addClass('mf-stop').attr('goto-step',15).removeClass('goPopup');
                // alert('goto normal form');
            }
            if(  $('[mf-step="9"] .no:checked').length ){
                //  $('[mf-step="14"]').find('.mf-stop').attr('goto-step',14.1);
                 $('[mf-step="14"]').find('.btn').removeClass('mf-stop').addClass('goPopup');
                //  alert('goto popup form');
            }
        }
    });

    //toggle final form based on having prior surgeries

    $('[mf-step="9"] input').change(function(){
        if( $(this).parents('.mf-step').find('.yes:checked').length ){
            $('[mf-step="14"]').find('.btn').addClass('mf-stop').attr('goto-step',15).removeClass('goPopup');
            // alert('goto normal form');
        }

        if( $(this).parents('.mf-step').find('.no:checked').length ){
            //$('[mf-step="14"]').find('.mf-stop').attr('goto-step',14.1);
            $('[mf-step="14"]').find('.btn').removeClass('mf-stop').addClass('goPopup');
            // alert('goto popup form');
        }
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

        if(data['previous_use_sildenafil'] != 'Sildenafil' && data['sildenafil_effective'] != 'Yes' &&
           data['previous_use_cialis'] == 'Cialis' && data['cialis_effective'] == 'Yes' &&
           data['previous_use_cialis_daily'] != 'Daily Cialis' && data['daily_cialis_effective'] != 'Yes'){
            redirection = data['cialis_redirection_link'];
        }
        else if(data['previous_use_sildenafil'] != 'Sildenafil' && data['sildenafil_effective'] != 'Yes' &&
           data['previous_use_cialis'] != 'Cialis' && data['cialis_effective'] != 'Yes' &&
           data['previous_use_cialis_daily'] == 'Daily Cialis' && data['daily_cialis_effective'] == 'Yes'){
            redirection = data['daily_cialis_redirection_link'];
        }
        else if(data['previous_use_sildenafil'] != 'Sildenafil' && data['sildenafil_effective'] != 'Yes' &&
           data['previous_use_cialis'] == 'Cialis' && data['cialis_effective'] == 'Yes' &&
           data['previous_use_cialis_daily'] == 'Daily Cialis' && data['daily_cialis_effective'] == 'Yes'){
            redirection = data['cialis_redirection_link'];
        }
        else if(data['previous_use_sildenafil'] == 'Sildenafil' && data['sildenafil_effective'] != 'Yes'){
            redirection = data['cialis_redirection_link'];
        }
        else{
            redirection = data['sildenafil_redirection_link'];
        }
        //console.log(data);
        //console.log(redirection);
        var args = $.param( data ) || '';

        //$(location).attr('href', '/shop?'+status);
        window.location.href = redirection+'?sign_up=true&'+args;
    }

    /*$(document).on('click', '.form-2-submit',function(event) {
        popup_redirect(event);
    });*/

    //skip step 10 if condition is true
    // $('[mf-step="9"]').change(function(){
    //     if( $('[data-skip="10"]:checked').length ){
    //         //goto final form if no selected
    //         $('[mf-step="9.1"]').find('label').attr('goto-step', 9.2);
    //         console.log('true in 9th');
    //     }else{
    //         console.log('false in 9th');
    //         //goto final form if yes is seelcted
    //         //goto step 10 if no is selected
    //         $('[mf-step="9.1"]').find('label.yes').attr('goto-step', 3.1);
    //         $('[mf-step="9.1"]').find('label.no').attr('goto-step', 10);
    //     }
    // });

	$('#dob').mask('00/00/0000');
    //age calculate
    $('#dob').on('change keyup',function(){
		if ( $('#dob').val().length > 9 ) {
	        $(this).parents('.mf-step').find('.btn').removeClass('disabled');

			$(this).parents('.mf-step').find('.btn').trigger('click');
		}
    });

    function date_validate(){
        $this = $('#dob');
        var dob = $('#dob').val();

        if(dob != ''){
            var str=dob.split('/');
            var firstdate=new Date(str[2] + '-' + str[1] + '-' + str[0]);
            var today = new Date();
            var age = today.getFullYear() - firstdate.getFullYear();
            var m = today.getMonth() - firstdate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < firstdate.getDate())) {
                age--;
            }
            last_step = 3;
            if( age < 18 ){
                //console.log('age smaller than 18');
                $(this).parents('.mf-step').hide()
                $('[mf-step="3.1"]').fadeIn('slow', function() {
	                gtm_not_qualified_check($(this));
                });
                $this.parents('.mf-step').hide();
            }else{
                //console.log('age greater than 18');
                $this.parents('.mf-step').hide().next().fadeIn('slow');
            }

			dataLayer.push({
				event: 'validateAge',
				age: dob,
			});
        }
    }

    $('.year-validate').click(function(e){
        date_validate();
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


	// GTM tracking
	function gtm_not_qualified_check(e) {

        if ( e.attr('mf-step') == '7.2' || e.attr('mf-step') == '16' || e.attr('mf-step') == '2.1' || e.attr('mf-step') == '3.1' || e.attr('mf-step') == '11.1' || e.attr('mf-step') == '11.2' ) {
    			var rejected_reason = '';

			switch($('[mf-step="' + last_step + '"] h2').text()) {
				case 'What is your gender?':
					rejected_reason = 'selectedFemale';
					break;
				case 'What is your date of birth?':
					rejected_reason = 'underEighteen';
					break;
				case 'Do you have, or have you ever had, Heart Disease?':
					rejected_reason = 'heartDisease';
					break;
				case 'Are you currently taking any Nitrate medications (GTN patch, Mononitrates, etc)?':
					rejected_reason = 'takingNitrateMedications';
					break;
				case 'You need to have your blood Pressure (BP) checked within the last 12 months to receive treatment.':
					rejected_reason = 'bloodPressureNotChecked';
					break;
				case 'When your blood pressure was taken were you diagnosed with?':
					rejected_reason = 'hypertensionOrHypotension';
					break;
				case 'Do you frequently experience lightheadedness?':
					rejected_reason = 'frequentLightheadedness';
					break;
				case 'Do you have any of the following cardiovascular symptoms?':
					rejected_reason = 'cardiovascularSymptoms';
					break;
				case 'Have you had a heart attack in the last 6 months?':
					rejected_reason = 'heartAttackLastSixMonths';
					break;
				case 'Have you ever had a stroke or TIA?':
					rejected_reason = 'strokeOrTia';
					break;
				case 'Do you have now, or have you ever had, any of the following conditions?':
					rejected_reason = 'everHadFollowingConditions';
					break;
				case 'Do you have any of the following conditions?':
					rejected_reason = 'haveFollowingConditions';
					break;
			}

			dataLayer.push({
				event: 'rejected',
				rejected: rejected_reason
			});
		}
	}

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
    // $( '.allergies-options' ).on( 'change', function() {

    //     if ( 'yes' == $(this).val() ) {
    //         $( '.detail-textarea' ).slideDown();
    //     } else {
    //         $( '.detail-textarea' ).slideUp();
    //     }
    //     $( '#allergies_check_hin' ).val( $(this).val() );
    // } );
});
