<form action="{$smarty.const.IA_SELF}" method="post" id="submit-rss" class="sap-form form-horizontal">
{preventCsrf}
<div class="wrap-list">
{if !empty($rss_data)}
	{foreach from=$rss_data item="item" key="key"}
		<fieldset class="wrap-group" id="rss-form-{$key}">
			<div class="row">
				<label class="col col-lg-1 control-label">{lang key='rss_title'}:</label>
				<div class="col col-lg-2">
					<input type="text" name="title-{$key}" value="{$item.title}">
				</div>
				<label class="col col-lg-1 control-label">{lang key='rss_refresh_time'}:</label>
				<div class="col col-lg-2">
					<input type="text" name="refresh-{$key}" value="{$item.refresh|default:600}">
				</div>
			</div>
			<label class="col col-lg-1 control-label">{lang key='entries_limit'}:</label>
			<div class="col col-lg-2">
				<input type="text" name="entries_limit-{$key}" value="{$item.entries_limit}">
			</div>
			<div class="row">
				<label class="col col-lg-1 control-label">{lang key='rss_links'}:</label>
				<div class="col col-lg-6">
					<textarea name="feed_url-{$key}" cols="100" rows="8">{$item.feed_url}</textarea>
				</div>
			</div>

			<div class="row">
				<input type="button" value="{lang key='delete_rss'}" onclick="delete_rss({$key})" class="btn btn-danger delete_rss">
				<input type="hidden" name="rss_id[]" value="{$key}">
			</div>
		</fieldset>
	{/foreach}
{/if}
</div>
<input type="button" class="btn btn-primary" value="{lang key='add_rss'}" id="add_rss">
<input type="submit" class="btn btn-success" value="{lang key='save'}">
</form>
{ia_add_media files='js:_IA_URL_plugins/rss_reader/js/rss_reader'}