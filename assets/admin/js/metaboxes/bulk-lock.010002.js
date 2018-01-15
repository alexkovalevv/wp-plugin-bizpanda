(function($) {

	/**
	 * Methods to manager bulk lockiing options.
	 */
	window.metaboxBulkLock = {

		wrap: $("#OPanda_BulkLockingMetaBox"),

		/**
		 * Inits the bulk locking metabox.
		 */
		init: function() {

			var self = this;

			$(document).on('click', '#onp-sl-bulk-locking-way-selector .btn', function() {
				self.selectWay($(this).data('name'));
				return false;
			});

			$(".onp-sl-setup-bulk-locking").click(function() {
				var defaultSettignsTab = $('input[type="hidden"][name="onp_sl_way"]').val();

				if( defaultSettignsTab && $("#onp-sl-bulk-locking-way-selector ." + defaultSettignsTab).length ) {
					self.selectWay(defaultSettignsTab);
				}
			});

			$("#onp-sl-save-bulk-locking-btn").click(function() {
				self.saveOptions();
				return false;
			});

			this.wrap.find(".onp-sl-cancel").click(function() {
				self.cancel();
				return false;
			});

			if( this.wrap.find(".onp-sl-has-options-content").is(".onp-sl-css-selector-state") ) {
				this.disableVisiblityOptions();
			}

			$("#onp-sl-bulk-lock-modal").on("keydown keypress keyup", function(e) {
				if( e.keyCode == 13 ) {
					e.preventDefault();
					e.stopPropagation();
					$("#onp-sl-save-bulk-locking-btn").click();
					return false;
				}
			});

			this.initSmartSelection();
		},

		/**
		 * Selects a given bulk locking way by its name.
		 */
		selectWay: function(name) {
			$("#onp-sl-bulk-locking-way-selector .active").removeClass("active");
			$(".onp-sl-bulk-locking-options").addClass('hide');

			var $this = $("#onp-sl-bulk-locking-way-selector ." + name);
			$this.addClass("active");

			var target = $this.data('target');
			$(target).removeClass('hide');
		},

		/**
		 * Cancels the bulk locking.
		 */
		cancel: function() {
			var self = this;

			$("#onp-sl-bulk-lock-options").html("");
			this.enableVisiblityOptions();

			this.wrap.find(".onp-sl-setup-section").fadeOut(300, function() {

				self.wrap.find(".onp-sl-setup-section")
					.removeClass("onp-sl-has-options-state")
					.addClass("onp-sl-empty-state");

				self.wrap.addClass("onp-sl-changed");
				self.wrap.find(".onp-sl-interrelation-hint").removeClass("onp-sl-has-options-state");
				self.wrap.find(".onp-sl-setup-section").fadeIn(500);

				$(document).trigger('onp-bizpanda-cancel-bulk-lock');
			});
		},

		/**
		 * Иницилизирует умные поля, для выборки статей и страниц
		 */
		initSmartSelection: function() {
			// Решение проблемы с потерей фокуса у текстовых полей
			$.fn.factoryBootstrap000_modal.Constructor.prototype.enforceFocus = function() {
			};

			var select2Config = {
				//dropdownParent: $('#onp-sl-bulk-lock-modal'),
				width: 300,
				ajax: {
					type: 'post',
					url: ajaxurl,
					dataType: 'json',
					delay: 500,
					data: function(params) {

						var data = {
							action: 'opanda_search_post',
							search_query: params.term // search term
						};

						var way = $("#onp-sl-bulk-locking-way-selector .active").data("name");

						if( way === "skip-lock" || way === "more-tag" || way == 'fullscreen-lock' ) {

							var $base;

							if( way === "skip-lock" ) {
								$base = $("#onp-sl-skip-lock-options");
							} else if( way === "more-tag" ) {
								$base = $("#onp-sl-more-tags-options");
							} else {
								$base = $("#onp-sl-fullscreen-lock-options");
							}

							var postTypes = [];
							$base.find(".onp-sl-post-type:checked").each(function() {
								postTypes.push($(this).val());
							});

							data['post_types'] = postTypes;
						}

						return data;

					},
					processResults: function(data, params) {
						return {
							results: data
						};
					},
					cache: true
				},

				escapeMarkup: function(markup) {
					return markup;
				},
				minimumInputLength: 1,
				templateSelection: function(dataPost) {
					return dataPost.id || dataPost.text;
				}
			};
			var lockPagesConfig = $.extend(true, {}, select2Config);
			lockPagesConfig.width = 650;

			// Только включенные страницы
			$(".onp-sl-select-lock-posts").bizpanda_select2(lockPagesConfig);

			// Исключенные страницы
			$(".onp-sl-search-posts-field").bizpanda_select2(select2Config);

			var excludeLockCatsConfig = $.extend(true, {}, select2Config);
			excludeLockCatsConfig.ajax.data = function(params) {
				return {
					action: 'opanda_search_cats',
					search_query: params.term // search term
				};
			};

			// Исключенные категории
			$(".onp-sl-search-posts-cats").bizpanda_select2(excludeLockCatsConfig);
		},

		/**
		 * Saves selected bulk locking options.
		 */
		saveOptions: function() {
			var self = this;
			if( !this.validateOptions() ) {
				return;
			}

			var options = {};

			options['way'] = $("#onp-sl-bulk-locking-way-selector .active").data("name");
			if( options['way'] === "skip-lock" ) {
				options['skip_number'] = parseInt($("#onp-sl-skip-lock-options .onp-sl-skip-number").val());
			} else if( options['way'] === "css-selector" ) {
				options['css_selector'] = $("#onp-sl-css-selector-options .onp-sl-css-selector").val();
			}

			if( options['way'] === "skip-lock" || options['way'] === "more-tag" || options['way'] == 'fullscreen-lock' ) {

				var $base;

				if( options['way'] === "skip-lock" ) {
					$base = $("#onp-sl-skip-lock-options");
				} else if( options['way'] === "more-tag" ) {
					$base = $("#onp-sl-more-tags-options");
				} else {
					$base = $("#onp-sl-fullscreen-lock-options");
				}

				var postTypes = [];
				$base.find(".onp-sl-post-type:checked").each(function() {
					postTypes.push($(this).val());
				});
				options['post_types'] = postTypes.join(', ');

				var selectPosts = $base.find('.onp-sl-select-lock-posts').val(),
					excludePosts = $base.find('.onp-sl-exclude-posts').val(),
					excludeCategories = $base.find('.onp-sl-exclude-categories').val();

				if( selectPosts ) {
					options['select_posts'] = selectPosts.join(',');
				}

				if( excludePosts ) {
					options['exclude_posts'] = excludePosts.join(',');
				}

				if( excludeCategories ) {
					options['exclude_categories'] = excludeCategories.join(',');
				}

				this.enableVisiblityOptions();
			} else {
				this.disableVisiblityOptions();
			}

			$("#onp-sl-bulk-lock-modal").factoryBootstrap000_modal("hide");

			// generating hidden fields to save on form submitting

			$("#onp-sl-bulk-lock-options").html("");
			for( var optionName in options ) {

				var $h = $("<input type='hidden' />")
					.attr('name', "onp_sl_" + optionName)
					.val(options[optionName]);

				$("#onp-sl-bulk-lock-options").append($h);
			}

			// shows selected values

			this.wrap.find(".onp-sl-setup-section").fadeOut(300, function() {

				self.wrap.find(".onp-sl-setup-section")
					.removeClass("onp-sl-empty-state")
					.addClass("onp-sl-has-options-state");

				self.wrap.find(".onp-sl-has-options-content")
					.removeClass("onp-sl-fullscreen-lock-state")
					.removeClass("onp-sl-skip-lock-state")
					.removeClass("onp-sl-more-tag-state")
					.removeClass("onp-sl-css-selector-state")
					.addClass("onp-sl-" + options['way'] + '-state');

				if( options['way'] === "skip-lock" ) {

					self.wrap.find(".onp-sl-skip-lock-content")
						.removeClass('onp-sl-skip-lock-0-state')
						.removeClass('onp-sl-skip-lock-1-state')
						.removeClass('onp-sl-skip-lock-2-state');

					if( options['skip_number'] === 0 ) {
						self.wrap.find(".onp-sl-skip-lock-content").addClass("onp-sl-skip-lock-0-state");
					} else if( options['skip_number'] === 1 ) {
						self.wrap.find(".onp-sl-skip-lock-content").addClass("onp-sl-skip-lock-1-state");
					} else if( options['skip_number'] > 1 ) {
						var text = window.bizpanda.lang.everyPostWillBeLockedEntirelyExceptFirstsParagraphs.replace("%s", options['skip_number']);
						self.wrap.find(".onp-sl-skip-lock-2-content").text(text);
						self.wrap.find(".onp-sl-skip-lock-content").addClass("onp-sl-skip-lock-2-state");
					}

				} else if( options['way'] === 'css-selector' ) {
					self.wrap.find(".onp-sl-css-selector-view").text(options['css_selector']);
				}

				self.wrap.find(".onp-sl-has-options-content")
					.removeClass("onp-sl-select-post-ids-rule-state")
					.removeClass("onp-sl-post-types-rule-state")
					.removeClass("onp-sl-exclude-post-ids-rule-state")
					.removeClass("onp-sl-exclude-categories-ids-rule-state");

				if( options['way'] === "skip-lock" || options['way'] === "more-tag" || options['way'] == 'fullscreen-lock' ) {

					var $base = options['way'] === "skip-lock"
						? $("#onp-sl-skip-lock-options")
						: $("#onp-sl-more-tags-options");

					self.wrap.find(".onp-sl-post-types-rule").text(
						window.bizpanda.lang.appliesToTypes.replace("%s", options['post_types'])
					);
					self.wrap.find(".onp-sl-select-post-ids-rule").text(
						window.bizpanda.lang.selectPosts.replace("%s", options['select_posts'])
					);
					self.wrap.find(".onp-sl-exclude-post-ids-rule").text(
						window.bizpanda.lang.excludesPosts.replace("%s", options['exclude_posts'])
					);
					self.wrap.find(".onp-sl-exclude-categories-ids-rule").text(
						window.bizpanda.lang.excludesCategories.replace("%s", options['exclude_categories'])
					);

					self.wrap.find(".onp-sl-has-options-content")
						.addClass("onp-sl-post-types-rule-state");

					if( options['select_posts'] ) {
						self.wrap.find(".onp-sl-has-options-content")
							.addClass("onp-sl-select-post-ids-rule-state");
					}

					if( options['exclude_posts'] ) {
						self.wrap.find(".onp-sl-has-options-content")
							.addClass("onp-sl-exclude-post-ids-rule-state");
					}

					if( options['exclude_categories'] ) {
						self.wrap.find(".onp-sl-has-options-content")
							.addClass("onp-sl-exclude-categories-ids-rule-state");
					}
				}

				self.wrap.find(".onp-sl-setup-section").fadeIn(500);
				self.wrap.addClass("onp-sl-changed");
				self.wrap.find(".onp-sl-interrelation-hint").addClass("onp-sl-has-options-state");

				$(document).trigger('onp-bizpanda-save-bulk-lock');
			});

			return false;
		},

		/**
		 * Validates the bulk locking options.
		 */
		validateOptions: function() {
			this.wrap.find(".has-error").removeClass('has-error');

			var way = $("#onp-sl-bulk-locking-way-selector .active").data("name");
			if( way === "skip-lock" ) {

				if( $("#onp-sl-skip-lock-options .alert").length > 0 ) {
					$("#onp-sl-skip-lock-options .alert").fadeOut(300, function() {
						$("#onp-sl-skip-lock-options .alert").fadeIn();
					});
					return false;
				}

				var skipNumber = parseInt($("#onp-sl-skip-lock-options .onp-sl-skip-number").val());
				if( isNaN(skipNumber) || skipNumber < 0 ) {
					$("#onp-sl-skip-lock-options .onp-sl-skip-number-row").addClass('has-error');
					return false;
				}

				if( $("#onp-sl-skip-lock-options .onp-sl-post-type:checked").length === 0 ) {
					$("#onp-sl-skip-lock-options .onp-sl-post-types").addClass('has-error');
					return false;
				}

			} else if( way === 'more-tag' ) {

				if( $("#onp-sl-more-tags-options .alert").length > 0 ) {
					$("#onp-sl-more-tags-options .alert").fadeOut(300, function() {
						$("#onp-sl-more-tags-options .alert").fadeIn();
					});
					return false;
				}

				if( $("#onp-sl-more-tags-options .onp-sl-post-type:checked").length === 0 ) {
					$("#onp-sl-more-tags-options .onp-sl-post-types").addClass('has-error');
					return false;
				}

			} else if( way === 'css-selector' ) {
				var cssSelector = $.trim($("#onp-sl-css-selector-options .onp-sl-css-selector").val());
				if( !cssSelector ) {
					$("#onp-sl-css-selector-options .onp-sl-content").addClass('has-error');
					return false;
				}
				return true;
			}
			return true;
		},

		disableVisiblityOptions: function() {
			var $message = this.wrap.find(".onp-sl-visibility-options-disabled").clone();
			$("#OPanda_VisabilityOptionsMetaBox .form-horizontal").hide().after($message);
			$message.fadeIn();
		},

		enableVisiblityOptions: function() {
			$("#OPanda_VisabilityOptionsMetaBox .onp-sl-visibility-options-disabled").remove();
			$("#OPanda_VisabilityOptionsMetaBox .form-horizontal").fadeIn();
		}
	};

	$(function() {
		window.metaboxBulkLock.init();
	});

})(jQuery);