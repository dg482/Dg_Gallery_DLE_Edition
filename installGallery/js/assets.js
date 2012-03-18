var selectDecorator = function(element) {
	$(element)
			.each(
					function() {
						var select = $(this);
						var name = select.attr('name');
						var option = $('option', select);
						if (select.hasClass('Gselect')) {
							return;
						}
						if (select.attr('multiple')) {
							return;
						}
						var label = $('body').find('label[for="' + name + '"]');
						var lW = label.width();
						if (!label.is('label')) {
							label = option.first();
						}
						var labeltext = label.html();
						label.remove();
						var wrap = select.addClass('Gselect').css({
							display : 'none'
						}).wrap('<div class="GselectBox"></div>');
						wrap.parent().css({
							width : select.width() + lW
						});
						wrap
								.parent()
								.prepend(
										'<span id="label_' + name
												+ '" class="GselectLabel">'
												+ labeltext + '</spn>',
										'<div class="GselectOptions"></div><span class="GselectArrow"></span>');
						var ul = $('<ul class="GselectList"></ul>');
						var selected = '';
						$('option', select)
								.each(
										function(i) {
											if ($(this).attr('selected')) {
												selected = $(this).html();
												ul
														.append('<li><a href="javascript:" id="'
																+ i
																+ '" class="GselectOptionActive">'
																+ $(this)
																		.html()
																+ '</a></li>')
											} else {
												ul
														.append('<li><a href="javascript:" id="'
																+ i
																+ '">'
																+ $(this)
																		.html()
																+ '</a></li>')
											}
										});
						$('.GselectOptions', wrap.parent()).css({
							width : wrap.parent().outerWidth(true)
						}).hide()
						$('.GselectOptions', wrap.parent()).append(ul);
						wrap.parent().append(
								'<span class="GselectOptionSelected">'
										+ selected + '</span>');
						wrap.parent().hover(function() {
							var box = $('.GselectOptions', this);
							box.fadeIn('fast');
						}, function() {
							$('.GselectOptions', this).fadeOut('fast')
						});
						$('.GselectOptions > ul > li', wrap.parent())
								.click(
										function() {
											var i = $('a', this).attr('id');
											$('option', select).removeAttr(
													'selected');
											var so = $('option', select).eq(i);
											so.attr('selected', 'selected');
											$(
													'.GselectOptionSelected',
													$(this).parent().parent()
															.parent()).html(
													so.html());
											$('li > a', $(this).parent())
													.removeClass(
															'GselectOptionActive');
											$('li', $(this).parent())
													.each(
															function() {
																var I = $('a',
																		this)
																		.attr(
																				'id');
																if (I == i) {
																	$('a', this)
																			.addClass(
																					'GselectOptionActive')
																}
															})
											$('.GselectOptions', wrap.parent())
													.fadeOut('fast');
											if (select.attr('onchange'))
												select.change();
										})
					})
}
function checkBoxDecorator() {
	$('p.switch').each(
			function() {
				var v = $(this).children('input[type="checkbox"]').val();
				if (v == 1) {
					$(this).children('label.on').addClass('active');
				} else {
					$(this).children('label.off').addClass('active');
				}
				$(this).children('label.on, label.off').click(
						function() {
							var cv = $(this).parent('p.switch').children(
									'input[type="checkbox"]').val();
							if (cv == 1) {
								$(this).removeClass('active').next('label.off')
										.addClass('active').parent('p.switch')
										.children('input[type="checkbox"]')
										.val(0);
							} else if (cv == 0) {
								$(this).removeClass('active')
								$(this).parent('p.switch').children('label.on')
										.addClass('active')
								$(this).parent('p.switch').children(
										'input[type="checkbox"]').val(1);
							}
						});
			});
}
function installStart() {
	var form = $('form#install').serializeArray();
	$('.loading').show();
	$.ajax({
		type : "POST",
		url : "/installGallery/index.php",
		data : form,
		dataType : "json",
		success : function(data) {
			if (data.stopimport) {
				$('div#proccess').children('div.success').fadeIn('slow');
				$('.loading').hide();
			} else {
				$('div#proccess').children('div.info').fadeIn('slow',
						function() {
							$('#count').html(data.up + ' / ' + data.count)
							setTimeout(function() {
								updateFile(data.up)
							}, 1000)
						});
			}
		},
		error : function() {
		}
	});
	return false;
}
function updateFile(s) {
	$.ajax({
		type : "POST",
		url : "/installGallery/index.php",
		data : ({
			action : 'updateFile',
			start : s
		}),
		dataType : "json",
		success : function(data) {
			$('#count').html(data.up + ' / ' + data.count);
			if (data.stop) {
				setTimeout(function() {
					updateFile(data.up)
				}, 1000)
			} else {
				$('.loading').hide();
				$('div#proccess').children('div.info').fadeOut('slow');
				$('div#proccess').children('div.success').fadeIn('slow');
			}
		},
		error : function() {
		}
	});
	return false;
}
$(document).ready(function() {
	selectDecorator('.selection');
	$('ul.nav').children('li').click(function() {
		$('ul.nav').children('li').removeClass('selected');
		$(this).addClass('selected');
		var id = $(this).index();
		$('#work-area').children('div.step').css('display', 'none')
		$('#work-area').children('div.step').eq(id).css('display', 'block')
	});
	$('a.close').click(function() {
		$(this).parent('div').fadeOut('slow', function() {
			$(this).remove();
		});
	});
	checkBoxDecorator();
});