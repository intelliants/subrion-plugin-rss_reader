{if isset($rss_reader) && $rss_reader}
	{if isset($rss_reader.errors)}
		<div class="alert-error">{'<br>'|implode:$rss_reader.errors}</div>
	{/if}
	{if isset($rss_reader)}
		<div class="ia-items">
			{foreach $rss_reader as $item}
				<div class="ia-item ia-item--border-bottom">
					<h5>
						<span class="fa fa-rss" style="color:orange;"></span> <a href="{$item.link}" target="_blank">{$item.title}</a>
					</h5>
					{if isset($item.description) && !empty($item.description)}
						<p>{$item.description|strip_tags|truncate:150:'...':false}</p>
					{/if}
				</div>
			{/foreach}
		</div>
	{/if}
{/if}