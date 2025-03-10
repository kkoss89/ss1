"use strict"; 
function General(){
    var self = this;
    this.init= function(){
        self.General();
        self.AddFunds();
        
        if ($("#order_resume").length > 0) {
            self.Order();
            self.CalculateOrderCharge();
        }

        if ($(".component-button-refill").length > 0) {
            self.Refill();
        }

        if ($(".navbar-side").length > 0) {
            self.MenuOption();
        }
        
    };

    this.Refill = function () {
        // Refill
        $(document).on("click", ".component-button-refill .btn-refill" , function(){
            event.preventDefault();
            var element = $(this),
                url = element.attr("data-href"),
                data = $.param({ token: token });
            $.post(url, data, function (_result) {
                console.log(_result);
                if (_result.status == 'success') {
                    element.replaceWith(_result.btn_text);
                }
            }, 'json');
        });
    }

    this.MenuOption = function () {
        const ps1 = new PerfectScrollbar('.navbar-side .scroll-bar', {
            wheelSpeed: 1,
            wheelPropagation: true,
            minScrollbarLength: 10,
            suppressScrollX: true
        });

        $(document).on("click", ".mobile-menu", function(){
            var _that = $(".navbar.navbar-side");
            if (_that.hasClass('navbar-folded')) {
                _that.removeClass('navbar-folded');
            }
            _that.toggleClass("active");
        });
    }

    this.AddFunds = function () {
      $(document).on("submit", ".actionAddFundsForm", function(){
        pageOverlay.show();
        event.preventDefault();
        var _that = $(this),
            _action = PATH + 'add_funds/process',
            _redirect = _that.data("redirect"),
            _data = _that.serialize();
        _data         = _data + '&' + $.param({token:token});
        $.post(_action, _data, function(_result){
            setTimeout(function(){
              pageOverlay.hide();
            },1500)
            if (is_json(_result)) {
                _result = JSON.parse(_result);
                if (_result.status == 'success' && typeof _result.redirect_url != "undefined") {
                    window.location.href = _result.redirect_url;
                }
                setTimeout(function(){
                    notify(_result.message, _result.status);
                },1500)
                setTimeout(function(){
                    if(_result.status == 'success' && typeof _redirect != "undefined"){
                        reloadPage(_redirect);
                    }
                }, 2000)
            }else{
                setTimeout(function(){
                    $(".add-funds-form-content").html(_result);
                }, 100)
            }
        })
        return false;
      })
    }

    this.Order = function () {
        // order page
        var _total_quantity = 0;
        var _service_price  = 0;
        $(document).on("input", ".ajaxQuantity" , function(){
            var _that = $(this),
                _quantity = _that.val(),
                _service_id = $("#service_id").val(),
                _service_max = $("#order_resume input[name=service_max]").val(),
                _service_min = $("#order_resume input[name=service_min]").val(),
                _service_price = $("#order_resume input[name=service_price]").val(),
                _is_drip_feed = $("#new_order input[name=is_drip_feed]:checked").val();
            if (_is_drip_feed) {
                var _runs           = $("#new_order input[name=runs]").val();
                var _interval       = $("#new_order input[name=interval]").val();
                var _total_quantity = _runs * _quantity;
                if (_total_quantity != "") {
                    $("#new_order input[name=total_quantity]").val(_total_quantity);
                }
            }else{
                var _total_quantity = _quantity;
            }
            var _total_charge = (_total_quantity != "" && _service_price != "") ? (_total_quantity * _service_price)/1000 : 0;
            _total_charge = preparePrice(_total_charge);
            
            var _currency_symbol = $("#new_order input[name=currency_symbol]").val();
            $("#new_order input[name=total_charge]").val(_total_charge);
            $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
        })
        // callback ajaxDripFeedRuns
        $(document).on("input", ".ajaxDripFeedRuns" , function(){
            var _that = $(this),
                _runs = _that.val(),
                _service_id = $("#service_id").val(),
                _quantity = $("#new_order input[name=quantity]").val(),
                _service_max = $("#order_resume input[name=service_max]").val(),
                _service_min = $("#order_resume input[name=service_min]").val(),
                _service_price = $("#order_resume input[name=service_price]").val(),
                _is_drip_feed = $("#new_order input[name=is_drip_feed]:checked").val();
            if (_is_drip_feed) {
                var _interval       = $("#new_order input[name=interval]").val();
                var _total_quantity = _runs * _quantity;
                if (_total_quantity != "") {
                    $("#new_order input[name=total_quantity]").val(_total_quantity);
                }
            }else{
                var _total_quantity = _quantity;
            }
            var _total_charge = (_total_quantity != "" && _service_price != "") ? (_total_quantity * _service_price)/1000 : 0;
            _total_charge = preparePrice(_total_charge);
            var _currency_symbol = $("#new_order input[name=currency_symbol]").val();
            $("#new_order input[name=total_charge]").val(_total_charge);
            $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
        })

        $(document).on("click", ".is_drip_feed" , function(){
            var _that = $(this),
                _service_id = $("#service_id").val(),
                _quantity = $("#new_order input[name=quantity]").val(),
                _service_max = $("#order_resume input[name=service_max]").val(),
                _service_min = $("#order_resume input[name=service_min]").val(),
                _service_price = $("#order_resume input[name=service_price]").val();
            if (_that.is(":checked")) {
                var _runs           = $("#new_order input[name=runs]").val();
                var _interval       = $("#new_order input[name=interval]").val();
                var _total_quantity = _runs * _quantity;
                if (_total_quantity != "") {
                    $("#new_order input[name=total_quantity]").val(_total_quantity);
                }
            }else{
                var _total_quantity = _quantity;
            }
            var _total_charge = (_total_quantity != "" && _service_price != "") ? (_total_quantity * _service_price)/1000 : 0;
            _total_charge = preparePrice(_total_charge);
            var _currency_symbol = $("#new_order input[name=currency_symbol]").val();
            $("#new_order input[name=total_charge]").val(_total_charge);
            $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
        })

        // callback ajaxChangeCategory
        $(document).on("change", ".ajaxChangeCategory", function () {
            event.preventDefault();
            $("#new_order .drip-feed-option").addClass("d-none");
            if ($("#order_resume").length > 0) {
                $("#order_resume input[name=service_name]").val("");
                $("#order_resume input[name=service_min]").val("");
                $("#order_resume input[name=service_max]").val("");
                $("#order_resume input[name=service_price]").val("");
                $("#order_resume input[name=service_price_show]").val("");
                $("#order_resume textarea[name=service_desc]").val("");
                $("#order_resume #service_desc").val("");
                $("#new_order input[name=service_price]").val("");
                $("#new_order input[name=service_min]").val("");
                $("#new_order input[name=service_max]").val("");
            }
            var element = $(this);
            var id = element.val();
            if (id == "") {
                return;
            }
            var url = element.data("url") + id;
            var data = $.param({ token: token });
            $.post(url, data, function (_result) {
                setTimeout(function () {
                    $("#result_onChange").html(_result);
                }, 100);
            });
        })
		
		$(document).on("change", ".ajaxMassChangeCategory", function () {
            event.preventDefault();
            
            var element = $(this);
            var id = element.val();
            if (id == "") {
                return;
            }
            var url = element.data("url") + id;
            var data = $.param({ token: token });
            $.post(url, data, function (_result) {
                setTimeout(function () {
                    $("#result_onMassChange").html(_result);
                }, 100);
            });
        })

        $(document).on("change", ".ajaxChangeService" , function(){
            event.preventDefault();
            var _that         = $(this);
            var _id           = _that.val();
            var _dripfeed     = _that.children("option:selected").data("dripfeed");
            var _service_type = _that.children("option:selected").data("type");

            $("#new_order .order-default-quantity input[name=quantity]").attr("disabled", false);
            $("#new_order .order-usernames-custom").addClass("d-none");
            $("#new_order .order-comments-custom-package").addClass("d-none");

            /*----------  reset quantity  ----------*/
            $("#new_order input[name=service_price]").val();
            $("#new_order input[name=service_min]").val();
            $("#new_order input[name=service_max]").val();

            $("#new_order .order-default-quantity input[name=quantity]").val('');
            var _total_charge = 0;
            var _currency_symbol = $("#new_order input[name=currency_symbol]").val();
            $("#new_order input[name=total_charge]").val(_total_charge);
            $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
            switch(_service_type) {
              case "subscriptions":
                $("#new_order input[name=sub_expiry]").val('');
                
                // Disable Schedule
                $('.schedule-option').addClass('d-none');
                
                $("#new_order .order-default-link").addClass("d-none");
                $("#new_order .order-default-quantity").addClass("d-none");
                $("#new_order #result_total_charge").addClass("d-none");

                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-username").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");
                
                $("#new_order .order-subscriptions").removeClass("d-none");
                break;

              case "custom_comments":
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order .order-comments").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");

                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-username").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");

                $("#new_order .order-default-quantity").removeClass("d-none");
                $("#new_order .order-default-quantity input[name=quantity]").attr("disabled", true);
                
                $("#new_order .order-subscriptions").addClass("d-none");
                break; 

              case "custom_comments_package":
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order .order-comments-custom-package").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");

                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-default-quantity").addClass("d-none");
                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-username").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");
                $("#new_order .order-subscriptions").addClass("d-none");
                break; 

              case "mentions_with_hashtags":
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order .order-default-quantity").removeClass("d-none");
                $("#new_order .order-usernames").removeClass("d-none");
                $("#new_order .order-hashtags").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");

                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-username").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");
                
                $("#new_order .order-subscriptions").addClass("d-none");

                break;

              case "mentions_custom_list":
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order .order-usernames-custom").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");
                $("#new_order .order-default-quantity").removeClass("d-none");
                $("#new_order .order-default-quantity input[name=quantity]").attr("disabled", true);
                
                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-username").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");
                
                $("#new_order .order-subscriptions").addClass("d-none");

                break;  

              case "mentions_hashtag":
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order .order-default-quantity").removeClass("d-none");
                $("#new_order .order-hashtag").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");

                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-username").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");
                $("#new_order .order-subscriptions").addClass("d-none");

                break;

              case "mentions_user_followers":
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order .order-default-quantity").removeClass("d-none");
                $("#new_order .order-username").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");

                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");
                $("#new_order .order-subscriptions").addClass("d-none");
                break;

              case "mentions_media_likers":
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order .order-default-quantity").removeClass("d-none");
                $("#new_order .order-media").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");

                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-username").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-subscriptions").addClass("d-none");

                break;  

              case "package":
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");
                

                $("#new_order .order-default-quantity").addClass("d-none");
                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-username").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");
                $("#new_order .order-subscriptions").addClass("d-none");

                break;

              case "comment_likes":
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order .order-default-quantity").removeClass("d-none");
                $("#new_order .order-username").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");

                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");
                $("#new_order .order-subscriptions").addClass("d-none");
                break;

              default:
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order .order-default-quantity").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");

                
                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-username").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");

                $("#new_order .order-subscriptions").addClass("d-none");

                break;
            }
            if (_dripfeed) {
                $("#new_order .drip-feed-option").removeClass("d-none");
            } else {
                $("#new_order .drip-feed-option").addClass("d-none");
            }
            var _action     = _that.data("url") + _id;
            var _data       = $.param({token:token});
            $.post( _action, _data,function(_result){
                $("#result_onChangeService").html(_result);
                // display min-max on Mobile Reponsive
                var _service_price  = $("#order_resume input[name=service_price]").val();
                var _service_min    = $("#order_resume input[name=service_min]").val();
                var _service_max    = $("#order_resume input[name=service_max]").val();
                $("#new_order input[name=service_price]").val(_service_price);
                $("#new_order input[name=service_min]").val(_service_min);
                $("#new_order input[name=service_max]").val(_service_max);

                setTimeout(function () {
                    if (_service_type == "package" || _service_type == "custom_comments_package" ) {
                        _total_charge   = _service_price;
                        _currency_symbol = $("#new_order input[name=currency_symbol]").val();
                        $("#new_order input[name=total_charge]").val(_total_charge);
                        $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
                    }
                }, 100);
            });
        }) 

		$(document).on("change", ".ajaxMassChangeService" , function(){
            event.preventDefault();
            var _that         = $(this);
            var _id           = _that.val();
            
            $("#mass_order input[name=mass_service_price]").val();
            $("#mass_order input[name=mass_service_min]").val();
            $("#mass_order input[name=mass_service_max]").val();

            $("#mass_order .order-default-quantity input[name=mass_quantity]").val('');
            var _total_charge    = 0;
            var _currency_symbol = $("#new_order input[name=currency_symbol]").val();
			
            $("#mass_order input[name=mass_total_charge]").val(_total_charge);
            $("#mass_order .mass_charge_number").html(_currency_symbol + _total_charge);
			
			$(".mass_service_description_info").addClass('d-none');
			$(".mass_service_description").html('');
            
            var _action     = _that.data("url") + _id;
            var _data       = $.param({token:token});
            $.post( _action, _data,function(_result){
				if (is_json(_result)) {
                    _result = JSON.parse(_result);
				
					$("#mass_order input[name=mass_service_price]").val(_result.price);
					$("#mass_order input[name=mass_service_min]").val(_result.min);
					$("#mass_order input[name=mass_service_max]").val(_result.max);
					$("#mass_order input[name=mass_min_quantity]").val(_result.min);
					$("#mass_order input[name=mass_max_quantity]").val(_result.max);
					
					$("#mass_order input[name=mass_quantity]").attr('max',_result.max);
					$("#mass_order input[name=mass_quantity]").attr('min',_result.min);

					setTimeout(function () {
						_total_charge   = _result.price;
						_currency_symbol = $("#new_order input[name=currency_symbol]").val();
						$("#mass_order input[name=mass_total_charge]").val(_total_charge);
						$("#mass_order .mass_charge_number").html(_currency_symbol + _total_charge);
						
						$(".mass_service_description_info").removeClass('d-none');
						
						var result_dec = _result.desc;
						
						$(".mass_service_description").html(result_dec.replace(/\r\n/g, '<br>'));
					}, 100);
				}
            });
        }) 
		
		$(document).on("input", ".mass_order_links" , function(){
			$('.ajaxMassQuantity').trigger('input');
		})
		
		var _mass_total_quantity = 0;
        var _mass_service_price  = 0;
        $(document).on("input", ".ajaxMassQuantity" , function(){
            var _that = $(this),
                _quantity           = _that.val(),
                _service_id         = $("#mass_order input[name=mass_service_id]").val(),
                _service_max        = $("#mass_order input[name=mass_service_max]").val(),
                _service_min        = $("#mass_order input[name=mass_service_min]").val(),
                _mass_service_price = $("#mass_order input[name=mass_service_price]").val(),
				_mass_order         = $(".mass_order_links").val();
				
			var lines      = _mass_order.split('\n');
			var totalLinks = 0;
			
			for (var i = 0; i < lines.length; i++) {
				var line = lines[i];

				// Trim leading and trailing whitespace
				var trimmedLine = line.trim();

				// Check if the line is not blank
				if (trimmedLine !== '') {
					totalLinks = totalLinks + 1;
				}
			}
			
			var _mass_total_quantity = (_quantity) ? _quantity : 0;
			
            var _total_charge   = (_mass_total_quantity != "" && _mass_service_price != "" && _mass_total_quantity > 0 && _mass_service_price > 0) ? (_mass_total_quantity * _mass_service_price) / 1000 : 0;
			
			_total_charge = _total_charge * totalLinks;
            _total_charge = preparePrice(_total_charge);

            var _currency_symbol = $("#new_order input[name=currency_symbol]").val();
            $("#mass_order input[name=mass_total_charge]").val(_total_charge);
            $("#mass_order .mass_charge_number").html(_currency_symbol + _total_charge);
        })
    }

    this.CalculateOrderCharge = function() {

        // callback ajax_custom_comments
        $(document).on("keyup", ".ajax_custom_comments" , function(){
            var _comments = $("#new_order .order-comments textarea[name=comments]").val();
            if (_comments == "") {
                var _quantity = 0;
            }else{
                var _quantity = 0;
                $.each(_comments.split("\n"), function(e, t){
                    if ($.trim(t).length > 0) {
                        _quantity++;
                    }
                });
            }
            var _service_id     = $("#service_id").val();
            $("#new_order .order-default-quantity input[name=quantity]").val(_quantity);
            var _service_max    = $("#order_resume input[name=service_max]").val();
            var _service_min    = $("#order_resume input[name=service_min]").val();
            var _service_price  = $("#order_resume input[name=service_price]").val();

            var _total_charge = (_quantity != "" && _service_price != "") ? (_quantity * _service_price)/1000 : 0;
            _total_charge = preparePrice(_total_charge);
            var _currency_symbol = $("#new_order input[name=currency_symbol]").val();
            $("#new_order input[name=total_charge]").val(_total_charge);
            $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
        })

        // callback ajax_custom_lists
        $(document).on("keyup", ".ajax_custom_lists" , function(){
            var _quantity = $("#new_order .order-usernames-custom textarea[name=usernames_custom]").val();
            if (_quantity == "") {
                var _quantity = 0;
            }else{
                var _quantity = _quantity.split("\n").length;
            }

            var _service_id     = $("#service_id").val();
            $("#new_order .order-default-quantity input[name=quantity]").val(_quantity);
            var _service_max    = $("#order_resume input[name=service_max]").val();
            var _service_min    = $("#order_resume input[name=service_min]").val();
            var _service_price  = $("#order_resume input[name=service_price]").val();

            var _total_charge = (_quantity != "" && _service_price != "") ? (_quantity * _service_price)/1000 : 0;
            _total_charge = preparePrice(_total_charge);
            var _currency_symbol = $("#new_order input[name=currency_symbol]").val();
            $("#new_order input[name=total_charge]").val(_total_charge);
            $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
        })

    }

    this.General = function() {
        /*----------  View User/back to admin----------*/
        $(document).on("click", ".ajaxViewUser", function () {
            event.preventDefault();
            pageOverlay.show();
            var element = $(this),
                url = element.attr("href"),
                data = $.param({ token: token });
            callPostAjax(element, url, data, '');
        }) 

        // Insert hyper-link
        $(document).on('focusin', function(e) {
            if ($(event.target).closest(".mce-window").length) {
              e.stopImmediatePropagation();
            }
        });

        // load ajax-Modal
        $(document).on("click", ".ajaxModal", function(){
            var element = $(this);
            var url = element.attr("href");
            $('#modal-ajax').load(url, function(){
                $('#modal-ajax').modal({
                    backdrop: 'static',
                    keyboard: false 
                });
                $('#modal-ajax').modal('show');
            });
            return false;
        });
        
        
        $(document).ready(function() {
            // Check if news announcement was shown in last 24 hours
            var lastShown = localStorage.getItem('newsAnnouncementLastShown');
            var now = new Date().getTime();
        
            if (!lastShown || (now - lastShown > 24 * 60 * 60 * 1000)) {
                // Trigger news announcement modal
                $('.nav-item.notifcation .ajaxModal').trigger('click');
                
                // Store current timestamp
                localStorage.setItem('newsAnnouncementLastShown', now);
            }
        });
		
		// load ajax-Modal
        $(document).on("click", ".ajaxSubOrders", function(){
            var element = $(this);
            var url     = element.attr("href");
            $('#modal-ajax').load(url, function(){
                $('#modal-ajax').modal({
                    backdrop: 'static',
                    keyboard: false 
                });
                $('#modal-ajax').modal('show');
            });
            return false;
        });

        /*----------  ajaxChangeTicketSubject  ----------*/
        $(document).on("change", ".ajaxChangeTicketSubject", function(){
            event.preventDefault();
            var element   = $(this);
            var type    = element.val();
            switch(type) {

              case "subject_order":
                $("#add_new_ticket .subject-order").removeClass("d-none");
                $("#add_new_ticket .subject-payment").addClass("d-none");
                break;  
                              
              case "subject_payment":
                $("#add_new_ticket .subject-order").addClass("d-none");
                $("#add_new_ticket .subject-payment").removeClass("d-none");
                break;

              default:
                $("#add_new_ticket .subject-order").addClass("d-none");
                $("#add_new_ticket .subject-payment").addClass("d-none");
                break;
            }
        })

        // ajaxChangeLanguage (footer top)
        $(document).on("change", ".ajaxChangeLanguage", function(){
            event.preventDefault();
            var element    = $(this);
            var pathname   = element.data("url") + "?" + "ids=" + element.val() + "&" + "redirect=" + element.data("redirect");
            window.location.href = pathname;
        })

        // ajaxChangeLanguageSecond (header top)
        $(document).on("click", ".ajaxChangeLanguageSecond", function(){
            event.preventDefault();
            var element    = $(this);
            var pathname   = element.data("url") + "?" + "ids=" + element.data("ids") + "&" + "redirect=" + element.data("redirect");
            window.location.href = pathname;
        })

        // callback ajaxChange
        $(document).on("change", ".ajaxChange" , function(){
            pageOverlay.show();
            event.preventDefault();
            var element = $(this);
            var id      = element.val();
            if (id == "") {
                pageOverlay.hide();
                return false;
            }
            var url     = element.data("url") + id;
            var data    = $.param({token:token});
            $.post( url, data, function(_result){
                pageOverlay.hide();
                setTimeout(function () {
                    $("#result_ajaxSearch").html(_result);
                }, 100);
            });
        }) 

        // callback ajaxSearch
        $(document).on("submit", ".ajaxSearchItem" , function(){
            pageOverlay.show();
            event.preventDefault();
            var _that       = $(this),
                _action     = _that.attr("action"),
                _data       = _that.serialize();

            _data       = _data + '&' + $.param({token:token});
            $.post( _action, _data, function(_result){
                setTimeout(function () {
                    pageOverlay.hide();
                    $("#result_ajaxSearch").html(_result);
                }, 300);
            });
        })

        // callback ajaxSearchItemsKeyUp with keyup and Submit from
        var typingTimer;                //timer identifier
        $(document).on("keyup", ".ajaxSearchItemsKeyUp" , function(){
            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                  event.preventDefault();
                  return false;
                }
            });
            event.preventDefault();
            clearTimeout(typingTimer);
            $(".ajaxSearchItemsKeyUp .btn-searchItem").addClass("btn-loading");
            var _that       = $(this),
                _form       = _that.closest('form'),
                _action     = _form.attr("action"),
                _data       = _form.serialize();
            _data       = _data + '&' + $.param({token:token});

            // if ( $("input:text").val().length < 2 ) {
            //     $(".ajaxSearchItemsKeyUp .btn-searchItem").removeClass("btn-loading");
            //     return;
            // }

            typingTimer = setTimeout(function () {
                $.post( _action, _data, function(_result){
                    setTimeout(function () {
                        $(".ajaxSearchItemsKeyUp .btn-searchItem").removeClass("btn-loading");
                        $("#result_ajaxSearch").html(_result);
                    }, 10);
                });
            }, 1500);

        })

        $(document).on("submit", ".ajaxSearchItemsKeyUp" , function(){
            event.preventDefault();
        })

        // callback actionForm
        $(document).on("submit", ".actionForm", function () {
            pageOverlay.show();
            event.preventDefault();
            var _that       = $(this),
                _action     = _that.attr("action"),
                _redirect   = _that.data("redirect");
            if ($("#mass_order").hasClass("active")) {
                var _data = $("#mass_order").find("input[name!=mass_order]").serialize();
				var selectData = $("#mass_order").find("select").serialize();
                var _mass_order_array = [];
                var _mass_orders = $("#mass_order").find("textarea[name=mass_order]").val();
                if (_mass_orders.length > 0) {
                    _mass_orders = _mass_orders.split(/\n/);
                    for (var i = 0; i < _mass_orders.length; i++) {
                        // only push this line if it contains a non whitespace character.
                        if (/\S/.test(_mass_orders[i])) {
                            _mass_order_array.push($.trim(_mass_orders[i]));
                        }
                    }
                }
                var _data = _data + '&' + selectData + '&' + $.param({ mass_order: _mass_order_array, token: token });
            } else {
                var _token = _that.find("input[name=token]").val();
                var _data = _that.serialize();
                if (typeof _token == "undefined") {
                    _data = _data + '&' + $.param({ token: token });
                }
            }
            
            $.post(_action, _data, function (_result) {
                setTimeout(function () {
                    pageOverlay.hide();
                }, 1500)
                
                if (is_json(_result)) {
                    _result = JSON.parse(_result);
                    if(_result.status == 'success' && _result.notification_type == 'place-order') {
                        setTimeout(function () {
                            show_success_message_place_order(_result);
                        }, 1000);
                    } else {
                        if ($("#order_resume").length > 0) {
                            if (!$(".order-success").hasClass('d-none')) {
                                $(".order-success").addClass('d-none')
                            }
                        }
                        setTimeout(function () {
                            notify(_result.message, _result.status);
                        }, 1500);
                        if (_result.status == 'success' && typeof _redirect != "undefined") {
                            setTimeout(function () {
								if(_result.redirect){
									reloadPage(_result.redirect);
								} else {
									reloadPage(_redirect);
								}
                            }, 2200);
                        } else {
                            grecaptcha.reset(); //New V4.1
                        } 
                    }
                } else {
                    setTimeout(function () {
                        $("#result_notification").html(_result);
                    }, 1500)
                }
            })
            return false;
        })

        // Show success message on place order page
        function show_success_message_place_order(data) {
            var notification_area = $("#order-message-area");

            var order_detail = data.order_detail;
            notification_area.find(".order-success .id span").html(order_detail.id);
            notification_area.find(".order-success .service_name span").html(order_detail.service_name);
            notification_area.find(".order-success .charge span").html(order_detail.charge);
            notification_area.find(".order-success .balance span").html(order_detail.balance);
            
            if (data.order_type == 'default') {
                notification_area.find(".order-success .username").addClass('d-none');
                notification_area.find(".order-success .posts").addClass('d-none');
                notification_area.find(".order-success .link").removeClass('d-none');
                notification_area.find(".order-success .quantity").removeClass('d-none');
                
                notification_area.find(".order-success .link span").html(order_detail.link);
                notification_area.find(".order-success .quantity span").html(order_detail.quantity);
            }

            if (data.order_type == 'subscriptions') {
                notification_area.find(".order-success .username").removeClass('d-none');
                notification_area.find(".order-success .posts").removeClass('d-none');
                notification_area.find(".order-success .link").addClass('d-none');
                notification_area.find(".order-success .quantity").addClass('d-none');
                
                notification_area.find(".order-success .username span").html(order_detail.username);
                notification_area.find(".order-success .posts span").html(order_detail.posts);
            }
            if ($(".order-success").hasClass('d-none')) {
                $(".order-success").removeClass('d-none')
            }
            $(".user-balance").html(data.user_balance);
        }

        // actionFormWithoutToast
        $(document).on("submit", ".actionFormWithoutToast", function(){
            alertMessage.hide();
            event.preventDefault();
            var _that       = $(this),
                _action     = _that.attr("action"),
                _data       = _that.serialize();
                _data       = _data + '&' + $.param({token:token});
            var _redirect   = _that.data("redirect");
            _that.find(".btn-submit").addClass('btn-loading');
            $.post(_action, _data, function (_result) {
                if (is_json(_result)) {
                    _result = JSON.parse(_result);
                    setTimeout(function () {
                        alertMessage.show(_result.message, _result.status);
                    }, 1500)

                    setTimeout(function () {
                        if (_result.status == 'success' && typeof _redirect != "undefined") {
                            reloadPage(_redirect);
                        }
                    }, 2000)
                } else {
                    setTimeout(function () {
                        $("#resultActionForm").html(_result);
                    }, 1500)
                }

                setTimeout(function () {
                    _that.find(".btn-submit").removeClass('btn-loading');
                }, 1500)
            })
            return false;
        })
    }
}

General = new General();
$(function () {
    General.init();
});