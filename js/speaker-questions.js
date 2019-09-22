var $ = jQuery,
	spJSONp = function (event, method, params, callbackFunc) {
		// data needed to send jsonp
		var data = {
			action: 'questionAjax',	// wp ajax action
			ajaxSSLNonce: spkquest_ajax.wpAJAXNonce, // nonce
			method: method, // server has switch/case for processing
			params: params // data to be processed
		};

		jQuery.ajax({
			type: "GET", // this is the essence of jsonp
			url: spkquest_ajax.ajaxurl, // wp ajax url
			cache: false, // to ensure proper data response
			dataType: "jsonp", // jsonp
			crossDomain: true, // enable ssl/nonssl
			data: data, // data to be sent

			success: function (response) {
				// console.log('success', response);
				// your callback function
				callbackFunc(response);
			},

			complete: function (response) {
				//console.log('complete', response);
			},

			error: function (response) {
				//console.log('error', response);
			}
		});
	},
	Questions = {
		starQuestion: function (response) {
			if ('starred' === response.status) {
				$('#q_' + response.id + ' .dashicons-star-empty').removeClass('dashicons-star-empty').addClass('dashicons-star-filled');
			} else if ('emptied' === response.status) {
				$('#q_' + response.id + ' .dashicons-star-filled').removeClass('dashicons-star-filled').addClass('dashicons-star-empty');
			}
		},

		dismissQuestion: function (response) {
			if ('draft' === response.status) {
				$('#q_' + response.id).remove();
			}
		},
		reorderQuestions: function (response) {
			// console.log(response);
		},
		getQuestionStats: function () {
			var q = [];
			$("#sq_list li").each(function (i, el) {
				q[$('.array_index', el).val()] = {
					ID: ('undefined' !== typeof $(el).attr('id')) ? $(el).attr('id').substring(2) : '',
					star: ( $('.dashicons', el).hasClass('dashicons-star-filled') ) ? 'filled' : 'empty'
				};
			});
			return q;
		},
		setupNewQuestions: function (response) {
			if (!response.same && 0 === $('#sq_list').find('li.ui-sortable-helper').length) {
				var source = $('#sq_list-template').html(),
					template = Handlebars.compile(source),
					html = template(response.questions);
				$('#sq_list').html(html);
			}
			spJSONp("wp_jsonp", "pollStarsOrder", {
				current: Questions.getQuestionStats(),
				session: $('[name=session_slug]').val()
			}, Questions.setupNewQuestions);
		},
		orderByStar: function () {
			$('#sq_list').find('li').each(function (ind, elem) {
				if ($('.dashicons', elem).hasClass('dashicons-star-filled')) {
					$(elem).insertAfter('#sq_list li.star-filled:last');
				} else if ($('.dashicons', elem).hasClass('dashicons-star-empty')) {
					if ($('#sq_list li:last').hasClass('star-filled')) {
						$(elem).insertAfter('#sq_list li:last');
					} else {
						$(elem).insertAfter('#sq_list li.star-empty:last');
					}
				}
			});
		},

		saveOrder: function () {
			var sortedIDs = $("#sq_list").sortable("toArray");
			spJSONp("wp_jsonp", "reorderQuestions", {
				task: "resort",
				order: sortedIDs
			}, Questions.reorderQuestions);
		},

		textareaLen: function (maxLen) {
			var qLen = $('textarea[name=session_question]').val().length;
			if (qLen >= parseInt(maxLen)) {
				$('textarea[name=session_question]').val($('textarea[name=session_question]').val().substring(0, parseInt(maxLen)));
				qLen = $('textarea[name=session_question]').val().length;
			}

			$('textarea[name=session_question] ~ i > span.charLeft').text(maxLen - qLen);
		},

		init: function () {
			// compile handlebars template
			if (document.getElementById('sq_list')) {
				// start long polling
				spJSONp("wp_jsonp", "pollStarsOrder", {
					current: Questions.getQuestionStats(),
					session: $('[name=session_slug]').val()
				}, Questions.setupNewQuestions);

				$('#sq_list').sortable({
					handle: ".dashicons-image-flip-vertical",
					stop: function (event, ui) {
						Questions.saveOrder();
					}
				});

				$('#sq_list').on('click', '.dashicons[class*=dashicons-star-]', function (e) {
					spJSONp("wp_jsonp", "starQuestion", {
						task: "star",
						id: $(this).closest('li').attr('id')
					}, Questions.starQuestion);
				});

				$('#sq_list').on('click', '.dashicons.dashicons-dismiss', function (e) {
					spJSONp("wp_jsonp", "dismissQuestion", {
						task: "dismiss",
						id: $(this).closest('li').attr('id')
					}, Questions.dismissQuestion);
				});
			}
			$('#unhide').click(function (e) {
				spJSONp("wp_jsonp", "unhideAll", {
					task: "unhide",
					session: $('[name=session_slug]').val()
				}, Questions.reorderQuestions);
			});
			$('#starsToTop').click(function (e) {
				Questions.orderByStar();
				Questions.saveOrder();
			});

			// limit text in Questions box to 1000 characters.
			var maxLen = 1000;
			$('textarea[name=session_question]').after('<br><i>You have <span class="charLeft">' + maxLen + '</span> characters remaining.</i>');
			$('textarea[name=session_question]').keyup(function (e) {
				Questions.textareaLen(maxLen);
			});
			$('textarea[name=session_question]').focusout(function (e) {
				Questions.textareaLen(maxLen);
			});
		}
	};

	$(document).ready(function () {
		Questions.init();
	});
