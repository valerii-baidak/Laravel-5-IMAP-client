$(document).ready(function() {
	var menu;
	var table;
	var unseenMsgNum = 0;
	var notify;
	var notifyLoading;
	var mailbox;

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.ajax({
		url: "/start",
		type: "POST",
		data: '',
		beforeSend: function () {
				notify = $.notify('<strong>Connecting...</strong>', {
				allow_dismiss: false,
				showProgressbar: true,
				placement: {
					from: 'bottom',
					align: 'right'
				}
			});

			setTimeout(function() {
				notify.update('message', '<strong>Updating DataBase</strong>');
			}, 2000);


			notifyLoading = $.notify('<strong>Loading...</strong>', {
				allow_dismiss: false,
				timer: 60000,
				placement: {
					from: 'bottom',
					align: 'right'
				}
			});
		},
		success: function( data ) {
			notify.close();
			notifyLoading.close();
			DrawMenu (data);
			$.ajax({
				url: "/mailbox",
				type: "POST",
				data: {
					'mailbox' : 'INBOX'
				},
				success: function( data ) {      // messages
					$.notify("Loading from DB was successful", {
						placement: {
							from: 'bottom',
							align: 'right'
						}
					});
					for (var i = 0; i<data.length; i++){
						if (data[i].read == "false") unseenMsgNum ++;
					}
					NumOfUnreadMSG (unseenMsgNum);
					DrawTable (data);
				}
			});

		}

	});

// Count of INBOX unseen messages
	function NumOfUnreadMSG (unseenMsgNum){
		$('.js-inbox>a>span').remove();
		$('.js-inbox>a').append('<span class="badge pull-right">'+unseenMsgNum+'</span>');
	}


	function DrawMenu (result) {
		menu = result;
		$('.js-active>li').each (function () {
			$(this).remove();
		});
		for (var i = 0; i < menu.length; i++) {
			if (menu[i].nameBox == 'INBOX'){
				$('.js-active').append('<li class="js-inbox" data-id ="'+i+'"><a href="#">' +menu[i].nameBox + '</a></li>');
				continue;
			}
			$('.js-active').append('<li data-id ="'+i+'"><a href="#">' +menu[i].nameBox + '</a></li>');
		}

		$('.js-inbox>a').css ({
			color: 'red'
		});


		$('.js-collapse-menu>li').each (function () {
			$(this).remove();
		});
		for (var i = 0; i < menu.length; i++) {
			if (menu[i].nameBox=='INBOX'){
				$('.js-collapse-menu').append('<li class="visible-xs js-collapse-menu-li" data-id ="'+i+'"><a href="#" class="button-menu" data-toggle="collapse" data-target=".navbar-collapse.in"><button class="btn btn-default btn-group-justified">'+menu[i].nameBox +'</button></a></li>');
				continue;
			}
			$('.js-collapse-menu').append('<li class="visible-xs js-collapse-menu-li" data-id ="'+i+'"><a href="#" class="button-menu" data-toggle="collapse" data-target=".navbar-collapse.in"><button class="btn btn-default btn-group-justified" >'+menu[i].nameBox +'</button></a></li>');
		}
	}

/* change font-weight for Reed/Unread messages */
	function readEmails() {
		$('tr').each(function (){
			var read = $(this).attr('data-read');
			if (read=='true'){
				$(this).children('.mail-text,.autor').css({
					fontWeight: 'normal'
				});
			}
		})
	}

	function timeConverter(UNIX_timestamp){
		var a = new Date(UNIX_timestamp * 1000);
		var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
		var year = a.getFullYear();
		var month =months[a.getMonth()];
		var date = a.getDate();
		var hour = a.getHours();
		var min = a.getMinutes();
		var b = new Date();
		var timeToday = b.getDate() + '-' + months[b.getMonth()] + '-' + b.getFullYear();
		var time = date + '-' + month + '-' + year;
		if (time == timeToday) time = hour + ':' + min;
		return time;
	}



	function DrawTable (result) {
		table =result;
		$('.js-show-hide>tr').each (function () {
			$(this).remove();
		});


		var msg = result;
		var numb =  msg.length - 1;
		for (var i = numb; i>-1; i--){
			$('.js-show-hide').append('<tr class="js-table" data-read ="'+
			msg[i].read + '" data-stared ="'+msg[i].stared+'" '+'data-id="'+i+'">'+
			'<td class="checkbox-check-mark"><input type = "checkbox" class="js-select"></td>'+
			'<td class="checkbox-star"><input type="checkbox" name="star" class="css-checkbox" >' +
			'<label  class="css-label"></td>' +
			'<td class="autor"><span>'+msg[i].sender+'</span></td>'+
			'<td class="mail-text"><div class="msgBody">'+
			msg[i].title+'<span class="text-body"> - ' +msg[i].body+'</span></div></td>'+
			'<td class="mail-date">'+timeConverter (msg[i].timeID)+'</td>'+
			'</tr>');
		}
		var time = 0;
		$('.js-table').each(function(){
			time +=250;
			$(this).fadeIn(time);
		})

		/* id for checkbox-star*/
		$('.checkbox-star').each(function (index){
			$(this).find ('input')
					.attr('id','star-'+ index);
			$(this).find ('label')
					.attr('for','star-'+ index);
			var stared = $(this).closest ('tr')
					.attr('data-stared');
			if (stared === 'true'){
				$(this).find('.css-label').click();
			}
		});
		readEmails();
	}

/*collapse menu and left navbar active color*/
	$ ('body').on ('click', '.js-active>li , .js-collapse-menu>li', function (){
		var id= $(this).attr('data-id');
		var mailbox = menu[id].pathBox;
		$('.js-active>li, .js-collapse-menu>li').each (function(){
			$ (this).find('a, .btn-default').css ({
				color: '#337ab7'
			});
			if ($(this).attr('data-id') == id){
				$ (this).find('a, .btn-default').css ({
					color: 'red'
				});
			}

		})

		$.ajax({
			url: "/mailbox",
			type: "POST",
			data: {
				'mailbox' : mailbox
			},
			beforeSend: function (){
				var time = 0;
				var el = $('.js-show-hide>tr').get().reverse();
				$(el).each(function () {
					time += 250;
					$(this).fadeOut(time);
				});
			},
			success: function( data ) {
				notify.close();
				DrawTable (data);
			}
		});
	})


	/*left navbar active color*/
	$('body').on('click', '.js-active>li>a', function(){
		chekboxArr =[];
		$('.js-active>li>a').css ({
			color: '#337ab7'
		});
		$(this).css({
			color:'red'
		});
	});


/*select all checkbox*/

	$('#js-select-all').on('change', function(){
		$('.js-select').prop('checked', this.checked);
		if ($('#js-select-all').prop('checked')){
		$('.js-show-hide tr').addClass("warning");
		}else{
			$('.js-show-hide tr').removeClass("warning");
		};
	});


/*function show emails*/

	function showEmail (id, attr){
		$(id).on('click', function(){
			$('.js-show-hide>tr').each(function(){
				$(this).removeClass('hide');
				var trData = $(this).attr(attr);
				switch (attr) {
					case "data-read":
						if(trData== 'false') $(this).addClass('hide');
						break;
					case "unread":
						var trData = $(this).attr('data-read');
						if(trData == 'true') $(this).addClass('hide');
						break;
					case "data-stared":
						if(trData == 'false') $(this).addClass('hide');
						break;
				};
			});
		});
	};
	showEmail('.js-read-email', 'data-read');
	showEmail('.js-unread-email', 'unread');
	showEmail('.js-stared-email', 'data-stared');


/*show all emails*/
	$('.js-all-email').on('click', function(){
		$('.js-show-hide>tr').each(function(){
			$(this).removeClass('hide');
		});
	});


/*select email*/

$('body').on('change','.js-select', function(){
		var parent = $(this).closest('tr');
		if ($(this).prop('checked')) {
			parent.addClass("warning");
		} else {
			parent.removeClass("warning");
		};
	});


/* modal massege text and change status read*/

	function openEmail (id) {
		$('body').on('click', id ,function(){
			var dataId= $(this).closest('.js-table').attr('data-id');
			var body = table[dataId].body;
			var senderaddr =table[dataId].senderadr;
			var title = table[dataId].title;

			$('.js-open-msg>p').each (function () {
				$(this).remove();
			});
			$('.js-open-msg').append('<p><b>' +senderaddr+ '</b></p>'+'<p><b>' +
					title+ '</b></p>'+'<p class="massege-text">' +body+ '</p>');
			$('#myModal1').modal('show');
			var thistr = $(this).closest('.js-table');
			var statusUnread = $(this).closest('.js-table').attr('data-read');
			if (statusUnread == 'false') {
				$.ajax({
					url: "/read",
					type: "POST",
					data: {
						read : "true",
						timeID : table[dataId].timeID,
						UID: table[dataId].uid,
						mailbox: table[dataId].mailbox
					},
					success: function( data ) {
						if (data == 1) {
							table[dataId].read = "true"
							unseenMsgNum -= 1;
							NumOfUnreadMSG (unseenMsgNum);
							thistr.attr('data-read', 'true');
							readEmails();
						}
					}
				});
			}
		});
	};

	openEmail ('.mail-text');
	openEmail ('.autor');


	function StarredMsgToggle () {
		$('body').on('change', '.css-checkbox', function(){
			var tr = $(this).closest('tr'),
				dataStarred = tr.attr('data-stared'),
				dataId= tr.attr('data-id');
			var timeId= table[dataId].timeID,
				mailbox = table[dataId].mailbox,
				UID = table[dataId].uid;
			if ($(this).prop('checked')) {
				if (dataStarred === 'true') return;
				dataStarred = 'true';
				tr.attr('data-stared', 'true');
				SentStarredStatus (dataStarred, timeId, mailbox, UID);
			} else {
				if (dataStarred === 'false') return;
				dataStarred = 'false';
				tr.attr('data-stared', 'false');
				SentStarredStatus (dataStarred, timeId, mailbox, UID);
			};
		});
	}
	StarredMsgToggle ();

	function SentStarredStatus (stared, timeId, mailbox, UID) {
		$.ajax({
			url: "/stared",
			type: "POST",
			data: {
				stared : stared,
				timeID: timeId,
				mailbox: mailbox,
				UID: UID
			}
		});
	}

 var chekboxArr =[];
/* select messages for delete*/
	$('body').on('change', '.js-select', function(){
		var dataId =  $(this).closest('tr').attr('data-id');
		var timeId= table[dataId].timeID;
		mailbox = table[dataId].mailbox;
		var UID = table[dataId].uid;
		if(this.checked) {

			var msgDel = {
				timeId:timeId,
				UID: UID
			}
			chekboxArr.push(msgDel);
		}else {
			chekboxArr = chekboxArr.filter(function(el) {
				return el.UID !== UID;
			});
		}
	});

/*sent request to delete select messages in DB and IMAP*/
	$('.js-delete-select-msg').on('click', function (){
		var jp = $.confirm({
			title: 'Confirm!',
			content: 'Are you sure to delete?',
			draggable: true,
			type: 'red',
			buttons: {
				confirm: {
					btnClass: 'btn-red',
					action: function(){
						$.ajax({
							url: "/delete",
							type: "POST",
							data: {
								delete: chekboxArr,
								mailbox: mailbox
							},
							success: function( data ) {
								$('.warning').each(function(){
									$(this).addClass ('hide');
								})
								if (data == 1) {
									$.notify({
										message: '<strong>Deleted successfully</strong>',
									},{
										type: 'success',
										placement: {
											from: 'bottom',
											align: 'right'
										}
									});
								} else {
									$.notify({
										title: '<strong>Error IMAP!</strong>',
										message: 'CANNOT Delete Messages',
									},{
										type: 'danger',
										placement: {
											from: 'bottom',
											align: 'right'
										}
									});
								}
							},
							error: function (){
								$.notify({
									title: '<strong>Error POST!</strong>',
									message: 'CANNOT Delete Messages',

								},{
									type: 'danger',
									placement: {
										from: 'bottom',
										align: 'right'
									}
								});
							}
						});
					}
				},
				cancel: function () {
				},
			}
		});
	})

/*reaload*/

	$('#reload').on ('click', function (){
		$.ajax({
			url: "/update",
			type: "POST",
			data: {
				mailbox: table[0].mailbox
			},
			beforeSend: function () {
				    notify = $.notify('<strong>Updating DataBase</strong> Do not close this page...', {
					allow_dismiss: false,
					showProgressbar: true,
					placement: {
						from: 'bottom',
						align: 'right'
					}
				});

				setTimeout(function() {
					notify.update({'type': 'success',
						'message': '<strong>Saving Data!</strong>',
						'progress': 10});
				}, 4500);

				setTimeout(function() {
					notify.update({'type': 'success',
						'message': '<strong>Saving Data!</strong>',
						'progress': 30});
				}, 7000);

				setTimeout(function() {
					notify.update({'type': 'success',
						'message': '<strong>Saving Data!</strong>',
						'progress': 50});
				}, 10000);

				setTimeout(function() {
					notify.update({'type': 'success',
						'message': '<strong>Saving Data!</strong>',
						'progress': 75});
				}, 13000);

				setTimeout(function() {
					notify.update({'type': 'success',
						'message': '<strong>Saving Data!</strong>',
						'progress': 90});
				}, 15000);
			},
			success: function( data ) {
				notify.close();
				$.notify("MySQL UPDATE was successful", {
					placement: {
						from: 'bottom',
						align: 'right'
					}
				});
				unseenMsgNum = 0;
				for (var i = 0; i<data.length; i++){
					if (data[i].read === "false") unseenMsgNum ++;
				}
				NumOfUnreadMSG (unseenMsgNum);
				DrawTable (data);
			},
			error: function (){
				$.notify("ERROR MySQL UPDATE", {
					type: 'danger',
					placement: {
						from: 'bottom',
						align: 'right'
					}
				});

			}
		});
	});

	/*
	*
	*
	* Send Message (PHPMailer)
	*
	*/

	var validMail = false;
	var data= false;

	$('#Send').on('click',function(){
		$(".js-form textarea").each(function (){
			$(this).parent().removeClass ('has-error');
			$(this).parent().removeClass ('has-warning');
			if (!(validMail)){
				setTimeout(function (){
					alert ('Please specify recipient!');
				},100);
				$('#email').parent().addClass('has-error');
				return;
			}
			var j =  $(this).val();
			j = $.trim (j);
			if (j == ""){
				$(this).parent().addClass ('has-warning');
				setTimeout (function (){
					if (confirm('Send this message without message body?') && validMail){
						ajax();
						};
				},200) ;
			}else{
				ajax ();
			}
		});

	})

	function ajax (){
		data = $('form.js-form').serialize();
		$.ajax({
			url:'/send',
			type: 'POST',
			contentType: 'application/x-www-form-urlencoded',
			data: data,
			beforeSend:  function (){
				$('.sending, #loader ').removeClass ('hide');
			},
			success: function (res) {
				$(".js-form textarea, input").each(function () {
					$(this).val('');
					$(this).parent().removeClass('has-error');
					$(this).parent().removeClass('has-warning');
					$(this).parent().removeClass('has-success');
					$('.sending, #loader ').addClass ('hide');
					validMail = false;
				})
				$('#myModal').modal('hide');
				var status = '';
				if (res == 1){
					$.notify('Message has been sent', {
						placement: {
							from: 'bottom',
							align: 'right'
						}
					});
				} else {
					$.notify('Message could not be sent.', {
						type: 'danger',
						placement: {
							from: 'bottom',
							align: 'right'
						}
					});
				}
			},
			error:function (){
				alert ('Error POST');
			}
		});
	}



	$('#email').focusout (function (){
		validMail = false;
		$(this).parent().removeClass('has-error');
		var data = $(this).val();
		var reg = /.+@.+\..+/i;
		var form = reg.test(data);
		if (!form) {
			$(this).parent().addClass('has-error');
		}else {
			$(this).parent().addClass('has-success');
			validMail = true;
		}
	});

	$('#subject').focusout (function (){
		$(this).parent().removeClass('has-warning');
		var j =  $(this).val();
		j = $.trim (j);
		if (j == "") {
			$(this).parent().addClass('has-warning');
		}else {
			$(this).parent().addClass('has-success');
		}
	})

	/* clear inputs  */
	$('#closebtn').on("click", function (){
		$(".js-form textarea, input").each(function(){
			$(this).val('');
			$(this).parent().removeClass('has-error');
			$(this).parent().removeClass('has-warning');
			$(this).parent().removeClass('has-success');
			validMail = false;
		})
	})



});