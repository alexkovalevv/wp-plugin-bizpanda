(function($) {
	if( !window.bizpanda ) {
		window.bizpanda = {};
	}
	if( !window.bizpanda.lockerOptions ) {
		window.bizpanda.lockerOptions = {};
	}

	/**
	 * Init lockers.
	 */
	window.bizpanda.initLockers = function() {

		var init = function($holder, ignorePageview) {
			if( !$holder ) {
				$holder = $(document);
			}

			var inlineMine = window.bizpanda.createInlineLockers($holder);
			var cssMine = window.bizpanda.createCssLockers($holder);

			if( ( inlineMine.has() || cssMine.has() ) && !this._inited ) {
				if( !ignorePageview ) {
					window.bizpanda.countPageViews();
				}
				window.bizpanda.countLockerViews();
			}

			inlineMine.explode();
			cssMine.explode();
		};

		if( !window.__pandalockers || (window.__pandalockers && !window.__pandalockers.managedInitHook) ) {

			// we are viewing a single post,
			// inits lockers immediately

			init(null, true);

		} else {

			// if window.bizpanda.managedInitHook set,
			// waits a hook to create lockers

			if( this._inited ) {
				return;
			}

			$(document).bind(__pandalockers.managedInitHook, function(e, $content, ignorePageview) {
				init($content, ignorePageview);
			});
		}

		this._inited = true;

		window.bizpanda.inited = true;
		$(document).trigger('bp-init');
	};

	/**
	 * Create inline lockers.
	 */
	window.bizpanda.createInlineLockers = function($holder) {
		var mine = window.bizpanda.createMine();

		if( !$holder ) {
			$holder = $(document);
		}

		var $elements = $holder.find(".onp-locker-call");
		for( var n = 0; n < $elements.length; n++ ) {
			var $target = $($elements[n]);

			var lockId = $target.data('lock-id');

			var applied = $target.data('bp-locker-applied');
			if( applied ) {
				continue;
			}

			$target.data('bp-locker-applied', true);

			var data = window.bizpanda.lockerOptions[lockId]
				? window.bizpanda.lockerOptions[lockId]
				: $.parseJSON($target.next().text());

			var runner = window.bizpanda.createLocker($target, data, lockId, true);
			mine.triggers.push(runner);
		}

		return mine;
	};

	/**
	 * Create CSS Lockers.
	 */
	window.bizpanda.createCssLockers = function($holder) {
		var mine = window.bizpanda.createMine();

		if( !$holder ) {
			$holder = $(document);
		}
		if( !window.bizpanda.bulkCssSelectors ) {
			return mine;
		}

		for( var index in window.bizpanda.bulkCssSelectors ) {
			var selector = window.bizpanda.bulkCssSelectors[index]['selector'];
			var lockId = window.bizpanda.bulkCssSelectors[index]['lockId'];

			var limitCounter = 0;

			var $elements = $holder.find(selector);
			for( var n = 0; n < $elements.length; n++ ) {
				var $target = $($elements[n]);

				var applied = $target.data('bp-locker-applied');
				if( applied ) {
					continue;
				}

				$target.data('bp-locker-applied', true);

				limitCounter++;
				if( limitCounter > 20 ) {
					return false;
				}

				var data = window.bizpanda.lockerOptions[lockId];

				var runner = window.bizpanda.createLocker($target, data, lockId, true);
				mine.triggers.push(runner);
			}
			;
		}

		return mine;
	};

	/**
	 * Creates a new mine.
	 */
	window.bizpanda.createMine = function() {

		return {
			has: function() {
				return this.triggers.length > 0;
			},
			triggers: [],
			explode: function() {

				for( var i = 0; i < this.triggers.length; i++ ) {
					this.triggers[i]();
				}
			}
		};
	};

	/**
	 * Counts page views.
	 */
	window.bizpanda.countPageViews = function() {
		if( !window.bizpanda.bp_ut_count_pageview ) {
			return;
		}
		window.bizpanda.bp_ut_count_pageview();
	};

	/**
	 * Counts locker views.
	 */
	window.bizpanda.countLockerViews = function() {
		if( !window.bizpanda.bp_ut_count_locker_pageview ) {
			return;
		}
		window.bizpanda.bp_ut_count_locker_pageview();
	};

	window.bizpanda.createLocker = function($target, data, lockId, mine) {

		// helpers

		/**
		 * Returns the current context data.
		 */
		var getContextData = function() {

			var context = {};
			context.postId = data.postId;
			if( !context.postId && window.__pandalockers ) {
				context.postId = window.__pandalockers.postId;
			}

			context.postTitle = ( document.getElementsByTagName("title")[0] )
				? document.getElementsByTagName("title")[0].innerHTML
				: "(no title)";

			context.postUrl = window.location.href;
			context.itemId = data.lockerId;
			return context;
		};

		/**
		 * Pings the stats.
		 */
		var pingStats = function(eventName, eventType) {

			var statsItem = {
				eventName: eventName,
				eventType: eventType,
				visitorId: $.pandalocker.tools.getValue("opanda_vid", null)
			};

			var req = $.ajax({
				url: data.ajaxUrl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'opanda_statistics',
					opandaStats: statsItem,
					opandaContext: getContextData()
				},
				success: function(data) {

					if( data && data.error ) {
						console && console.log && console.log(data.error);
						return;
					}
				},
				error: function(data) {
					if( !console || !console.log ) {
						return;
					}
					console.log('Unexpected error occurred during the ajax request:');
					console.log(req.responseText);
				}
			});
		};

		var options = data.options;
		options.id = lockId;
		options.lockerId = data.lockerId;

		// loading the locked content via ajax

		if( data.ajax ) {

			options.content = {
				url: data.ajaxUrl,
				type: 'POST',
				data: {
					lockerId: data.lockerId,
					action: 'opanda_loader',
					hash: data.contentHash
				}
			};
		}

		// tracking subscribers & subscriptions

		$.pandalocker.filters.add(lockId + '.ajax-data', function(dataToPass) {
			dataToPass.opandaContextData = getContextData();
			return dataToPass;
		});

		// tracking stats

		if( typeof data.stats == 'undefined' || data.stats ) {

			$.pandalocker.hooks.add(lockId + '.unlock', function(locker, sender, senderName) {
				if( $.inArray(sender, ['button', 'timer', 'cross']) === -1 ) {
					return;
				}
				if( 'button' === sender ) {
					pingStats(senderName, 'unlock');
				} else {
					pingStats(sender, 'skip');
				}

				$(window).resize();

				// for the 'mtouch-quiz' plugin
				if( window.mtq_resize_quizzes ) {
					window.mtq_resize_quizzes();
				}
			});

			var hooksToTrack = [
				'impress',
				'error', 'social-app-declined',
				'got-linkedin-follower',
				'got-youtube-subscriber'
			];

			$.each(hooksToTrack, function(index, value) {
				$.pandalocker.hooks.add(lockId + '.' + value, function() {
					pingStats(value);
				});
			});
		}

		// removes the called and creates the locker

		$target.removeClass("onp-locker-call");
		if( !window.bizpanda.lockerOptions[lockId] ) {
			$target.next().remove();
		}

		var result = function() {

			// added support a popup mode
			if( !data || !data.options ) {
				throw new Error('Locker options is not set.');
			}

			if( data.options.open_locker_trigger ) {
				if( data.options.open_locker_trigger == 'click' || data.options.open_locker_trigger == 'hover' ) {
					var openLockerLinkSelector = '.onp-open-locker-selector';

					if( data.options.open_locker_selector && typeof data.options.open_locker_selector === 'string' ) {
						try {
							document.querySelector(data.options.open_locker_selector);
							openLockerLinkSelector += ',' + data.options.open_locker_selector;
						}
						catch( e ) {
							console.log('Invalid css selector.');
						}
					}

					var eventName = 'click';
					if( data.options.open_locker_trigger == 'hover' ) {
						eventName = 'hover'
					}

					$(document).on(eventName, openLockerLinkSelector, function() {
						$target.pandalocker(options);
						return false;
					});

					return;
				}
				if( data.options.open_locker_trigger == 'adblock' ) {
					// detect adblock
					if( document.getElementById('LfgHlQpezcEr') ) {
						return;
					}
				}
			}

			if( data.options.locker && data.options.locker.delay ) {
				setTimeout(function() {
					$target.pandalocker(options);
				}, data.options.locker.delay * 1000);
			} else {
				$target.pandalocker(options);
			}
		};

		if( mine ) {
			return result;
		}
		result();
	};

	// dynamic themes

	var bindFunction = function() {
		$(document).ajaxComplete(function() {
			window.bizpanda.initLockers();
			setTimeout(function() {
				window.bizpanda.initLockers();
			}, 3000);
			setTimeout(function() {
				window.bizpanda.initLockers();
			}, 5000);
		});

		if( !window.bizpanda.dynamicThemeSupport ) {
			return;
		}

		if( window.bizpanda.dynamicThemeEvent !== '' ) {
			$(document).bind(window.bizpanda.dynamicThemeEvent, function() {
				window.bizpanda.initLockers();
			});
		}
	};

	if( window.bizpanda.dynamicThemeSupport ) {
		bindFunction();
	} else {
		$(function() {
			bindFunction();
		});
	}

	// visibility providers

	var visibilityVars;
	if( void 0 !== window.__pandalockers && window.__pandalockers["visibility"] ) {
		visibilityVars = window.__pandalockers.visibility;
	}

	var visibilityParams = ['user-role', 'user-registered', 'post-published'];

	var setVisibilityProvider = function(visibilityParam) {

		$.pandalocker.services.visibilityProviders[visibilityParam] = {
			getValue: function() {

				var val = $.pandalocker.tools.cookie('bp_' + visibilityParam);
				if( val ) {
					return val;
				}

				if( !visibilityVars[visibilityParam] ) {
					return null;
				}
				return visibilityVars[visibilityParam];
			}
		};
	};

	for( var i = 0; i < visibilityParams.length; i++ ) {
		setVisibilityProvider(visibilityParams[i]);
	}

	var getUserTracker = function() {
		var obj = null;

		if( !window.bizpanda.bp_can_store_localy ) {
			console.log && console.log('[Lockers] User Tracker code is not loaded.');
			return null;
		}

		if( window.bizpanda.bp_can_store_localy() ) {
			obj = window.localStorage.getItem('bp_ut_session');
		} else {
			obj = $.pandalocker.tools.cookie('bp_ut_session');
		}

		if( !obj ) {
			return obj;
		}

		obj = obj.replace(/\-c\-/g, ',');
		obj = obj.replace(/\-q\-/g, '"');
		obj = JSON.parse(obj);

		return obj;
	}

	$.pandalocker.services.visibilityProviders['session-pageviews'] = {
		getValue: function() {
			var obj = getUserTracker();

			if( !obj ) {
				return 0;
			}
			return obj.pageviews;
		}
	};

	$.pandalocker.services.visibilityProviders['session-locker-pageviews'] = {
		getValue: function() {
			var obj = getUserTracker();
			if( !obj ) {
				return 0;
			}
			return obj.lockerPageviews ? obj.lockerPageviews : 0;
		}
	};

	$.pandalocker.services.visibilityProviders['session-landing-page'] = {
		getValue: function() {
			var obj = getUserTracker();
			return obj.landingPage;
		}
	};

	$.pandalocker.services.visibilityProviders['session-referrer'] = {
		getValue: function() {
			var obj = getUserTracker();
			return obj.referrer;
		}
	};

	$.pandalocker.services.visibilityProviders['user-cookie-name'] = {
		getValue: function(condition) {
			return $.pandalocker.tools.cookie(condition.value) ? condition.value : false;
		}
	};

})(__$onp);

/*!
 * Creater Script
 * Copyright 2014, OnePress, http://byonepress.com
 */
(function($) {
	$(function() {
		window.bizpanda.initLockers();
		setTimeout(function() {
			window.bizpanda.initLockers();
		}, 2000);
	});
})(__$onp);