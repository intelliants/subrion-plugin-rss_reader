function delete_rss(fid)
{
	intelli.confirm(_t('rm_rss_conf'), null, function(result) {
		if(result)
		{
			$('#rss-form-' + fid).remove();

			var url = intelli.config.admin_url + '/rss_reader/post.json';

			var post_data = new Object();
			post_data.act = 'delete';
			post_data.id = fid;

			$.post(url, post_data, function(data)
			{
				var type = data.error ? 'error' : 'success';
				intelli.notifBox({msg: data.msg, type: type, autohide: true});
			});
		}
	});
}

$(function()
{
	$('#add_rss').click(function()
	{
		var rss_ids = $('input[name="rss_id[]"]').map(function () {
			return this.value;
		}).get();
		if(rss_ids.length < 1)
		{
			var new_id = 1;
		}
		else
		{
			var last_id = rss_ids[rss_ids.length-1];
			var new_id = parseInt(last_id) + 1;
		}

		td1_content =	'<fieldset class="wrap-group" id="rss-form-' + new_id + '">';

		td1_content +=	'<div class="row">';
		td1_content +=	'<label class="col col-lg-1 control-label">' + _t('rss_title') + ':</label>';
		td1_content +=	'<div class="col col-lg-2">';
		td1_content +=	'<input type="text" name="title-' + new_id + '">';
		td1_content +=	'</div>';

		td1_content +=	'<label class="col col-lg-1 control-label">' + _t('rss_refresh_time') + ':</label>';
		td1_content +=	'<div class="col col-lg-2">';
		td1_content +=	'<input type="text" name="refresh-' + new_id + '" value="600">';
		td1_content +=	'</div>';
		td1_content +=	'</div>';

		td1_content +=	'<label class="col col-lg-1 control-label">' + _t('entries_limit') + ':</label>';
		td1_content +=	'<div class="col col-lg-2">';
		td1_content +=	'<input type="text" name="entries_limit-' + new_id + '" value="6">';
		td1_content +=	'</div>';
		td1_content +=	'</div>';

		td1_content +=	'<div class="row">';
		td1_content +=	'<label class="col col-lg-1 control-label">' + _t('rss_links') + ':</label>';
		td1_content +=	'<div class="col col-lg-6">';
		td1_content +=	'<textarea name="feed_url-' + new_id + '" cols="100" rows="8"></textarea>';
		td1_content +=	'</div>';
		td1_content +=	'</div>';

		td1_content +=	'<div class="row">';
		td1_content +=	'<input type="button" value="' + _t('delete_rss') + '" onclick="delete_rss(' + new_id + ')" class="btn btn-danger delete_rss">';

		td1_content +=	'<input type="hidden" name="rss_id[]" value="' + new_id + '">';
		td1_content +=	'</div>';

		td1_content +=	'</fieldset>';

		$('#submit-rss .wrap-list').append(td1_content);
	});

	$("#submit-rss").submit(function(event) {
		event.preventDefault(); 

		var rss_ids = $('input[name="rss_id[]"]').map(function () {
			return this.value;
		}).get();

		var url = $(this).attr('action') + 'post.json';
		
		$.each(rss_ids, function(i, v)
		{
			//Only save RSS if feed_url exists, default title if missing title
			if($('textarea[name="feed_url-' + v + '"]').val() != "" ) {
				var post_data = new Object();
				post_data.act = 'save';
				post_data.id = v;
				post_data.title = $('input[name="title-' + v + '"]').val();
				post_data.refresh = $('input[name="refresh-' + v + '"]').val();
				post_data.entries_limit = $('input[name="entries_limit-' + v + '"]').val();
				post_data.feed_url = $('textarea[name="feed_url-' + v + '"]').val();

				$.post(url, post_data, function(data)
				{
					var type = data.error ? 'error' : 'success';
					intelli.notifBox({msg: data.msg, type: type, autohide: true});
				});
			}
		});
	});
});
